<?php

namespace App\Services\Compliance;

use App\Models\Client;
use App\Models\Service;
use App\Models\FinancialYear;
use App\Models\ComplianceTemplate;
use App\Models\ComplianceInstance;
use App\Models\Task;
use App\Models\TimelineEvent;
use App\Enums\ComplianceStatus;
use Carbon\Carbon;

class ComplianceGenerator
{
    /**
     * Generate applicable compliance instances & corresponding tasks for a client and financial year.
     * Idempotent: running multiple times will not duplicate existing instances.
     */
    public function generateForClient(Client $client, FinancialYear $fy): array
    {
        $createdInstances = [];
        $services = Service::where('tenant_id', $client->tenant_id)
            ->where('client_id', $client->id)
            ->where('financial_year_id', $fy->id)
            ->get();

        foreach ($services as $service) {
            $serviceTypeCode = strtolower($service->type->value);
            
            $templates = ComplianceTemplate::where('active', true)
                ->where('service_type', $serviceTypeCode)
                ->get();

            foreach ($templates as $tmpl) {
                // Check client applicability if specified
                if (!empty($tmpl->applicable_client_types) && !in_array($client->client_type->label(), $tmpl->applicable_client_types) && !in_array($client->entity_type, $tmpl->applicable_client_types)) {
                    continue;
                }

                $periods = $this->determinePeriodsAndDueDates($tmpl, $fy);

                foreach ($periods as $p) {
                    $periodLabel = $p['period'];
                    $dueDate = $p['due_date'];

                    $instance = ComplianceInstance::withoutGlobalScopes()
                        ->where('tenant_id', $client->tenant_id)
                        ->where('client_id', $client->id)
                        ->where('compliance_template_id', $tmpl->id)
                        ->where('financial_year_id', $fy->id)
                        ->where('period', $periodLabel)
                        ->first();

                    if (!$instance) {
                        $instance = ComplianceInstance::create([
                            'tenant_id' => $client->tenant_id,
                            'client_id' => $client->id,
                            'service_id' => $service->id,
                            'financial_year_id' => $fy->id,
                            'compliance_template_id' => $tmpl->id,
                            'period' => $periodLabel,
                            'due_date' => $dueDate,
                            'status' => ComplianceStatus::UPCOMING,
                            'assigned_to' => $service->assigned_staff_id,
                            'reviewer_id' => $service->reviewer_id,
                        ]);

                        // Automatically generate linked task
                        Task::create([
                            'tenant_id' => $client->tenant_id,
                            'client_id' => $client->id,
                            'compliance_instance_id' => $instance->id,
                            'financial_year_id' => $fy->id,
                            'assigned_to' => $service->assigned_staff_id,
                            'reviewer_id' => $service->reviewer_id,
                            'title' => "{$tmpl->code}: {$client->name} ({$periodLabel})",
                            'service_type' => $tmpl->service_type,
                            'status' => 'pending',
                            'priority' => 'high',
                            'due_date' => $dueDate,
                            'description' => "Automated compliance task for {$tmpl->name} - Period {$periodLabel}",
                            'created_by' => null,
                        ]);

                        TimelineEvent::create([
                            'tenant_id' => $client->tenant_id,
                            'client_id' => $client->id,
                            'user_id' => null,
                            'event_type' => 'Compliance Instance Generated',
                            'description' => "Generated compliance {$tmpl->name} for period {$periodLabel} due {$dueDate->format('d M Y')}",
                        ]);

                        $createdInstances[] = $instance;
                    }
                }
            }
        }

        return $createdInstances;
    }

    private function determinePeriodsAndDueDates(ComplianceTemplate $tmpl, FinancialYear $fy): array
    {
        $periods = [];
        $fyLabel = $fy->year_label; // e.g. "FY 2026-27"
        $yearParts = explode('-', str_replace('FY ', '', $fyLabel));
        $startYear = isset($yearParts[0]) ? (int)$yearParts[0] : (int)date('Y');
        if ($startYear < 2000) $startYear = (int)date('Y');

        if ($tmpl->frequency === 'annual') {
            $dueMonth = $tmpl->default_due_month ?? 7;
            $dueYear = ($dueMonth < 4) ? $startYear + 1 : $startYear;
            $dueDate = Carbon::createFromDate($dueYear, $dueMonth, $tmpl->default_due_day);
            $periods[] = [
                'period' => "Annual ({$fyLabel})",
                'due_date' => $dueDate,
            ];
        } elseif ($tmpl->frequency === 'monthly') {
            for ($month = 4; $month <= 15; $month++) { // Apr to Mar next year
                $m = ($month > 12) ? $month - 12 : $month;
                $y = ($month > 12) ? $startYear + 1 : $startYear;
                $monthName = Carbon::createFromDate($y, $m, 1)->format('M Y');
                $dueM = ($m === 12) ? 1 : $m + 1;
                $dueY = ($m === 12) ? $y + 1 : $y;
                $dueDate = Carbon::createFromDate($dueY, $dueM, min($tmpl->default_due_day, 28));

                $periods[] = [
                    'period' => $monthName,
                    'due_date' => $dueDate,
                ];
            }
        } elseif ($tmpl->frequency === 'quarterly') {
            $quarters = [
                ['q' => 'Q1 (Apr-Jun)', 'due_m' => 7, 'due_y' => $startYear],
                ['q' => 'Q2 (Jul-Sep)', 'due_m' => 10, 'due_y' => $startYear],
                ['q' => 'Q3 (Oct-Dec)', 'due_m' => 1, 'due_y' => $startYear + 1],
                ['q' => 'Q4 (Jan-Mar)', 'due_m' => 4, 'due_y' => $startYear + 1],
            ];
            foreach ($quarters as $q) {
                $dueDate = Carbon::createFromDate($q['due_y'], $q['due_m'], min($tmpl->default_due_day, 28));
                $periods[] = [
                    'period' => "{$q['q']} {$fyLabel}",
                    'due_date' => $dueDate,
                ];
            }
        }

        return $periods;
    }
}
