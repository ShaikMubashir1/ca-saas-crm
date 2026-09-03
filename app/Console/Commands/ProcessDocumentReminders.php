<?php

namespace App\Console\Commands;

use App\Models\DocumentRequest;
use App\Models\Task;
use App\Models\TimelineEvent;
use App\Enums\DocumentRequestStatus;
use App\Services\Communication\WhatsApp\WhatsAppService;
use Illuminate\Console\Command;

class ProcessDocumentReminders extends Command
{
    protected $signature = 'crm:process-document-reminders';
    protected $description = 'Process automated WhatsApp document reminders and trigger task escalation on max attempts';

    public function handle()
    {
        $wsService = new WhatsAppService();

        $activeRequests = DocumentRequest::withoutGlobalScopes()
            ->get()
            ->filter(fn($r) => in_array($r->status->value ?? $r->status, ['sent', 'partially_received']));

        $processedCount = 0;
        $escalatedCount = 0;

        $this->info("Active requests count: " . $activeRequests->count());
        foreach ($activeRequests as $req) {
            $this->info("Req ID {$req->id}: count {$req->reminder_count}, max {$req->max_reminders}");
            $pendingItems = $req->items ? $req->items->whereNull('received_at') : collect();
            if ($req->items->count() > 0 && $pendingItems->isEmpty()) {
                $req->update(['status' => DocumentRequestStatus::COMPLETED->value]);
                continue;
            }

            // Check max reminders limit for escalation
            if ($req->reminder_count >= $req->max_reminders) {
                // Trigger Smart Task Escalation if not already created
                $taskExists = Task::withoutGlobalScopes()
                    ->where('tenant_id', $req->tenant_id)
                    ->where('client_id', $req->client_id)
                    ->where('title', 'like', "%Follow up for missing documents%")
                    ->where('status', '!=', 'completed')
                    ->exists();

                if (!$taskExists) {
                    $staffId = $req->service ? $req->service->assigned_staff_id : null;
                    Task::create([
                        'tenant_id' => $req->tenant_id,
                        'client_id' => $req->client_id,
                        'assigned_to' => $staffId,
                        'title' => "Follow up for missing documents - " . ($req->service ? $req->service->type->label() : 'Compliance'),
                        'service_type' => $req->service ? $req->service->type->value : 'document_chasing',
                        'status' => 'pending',
                        'priority' => 'high',
                        'due_date' => now()->addDays(2),
                        'description' => "Client {$req->client->name} has not submitted requested documents after {$req->reminder_count} automated reminders.",
                        'created_by' => $req->created_by,
                    ]);

                    TimelineEvent::create([
                        'tenant_id' => $req->tenant_id,
                        'client_id' => $req->client_id,
                        'user_id' => null,
                        'event_type' => 'Reminder Escalated to Task',
                        'description' => "Created high-priority follow-up task for staff after {$req->reminder_count} unanswered reminders",
                    ]);

                    $escalatedCount++;
                }
                continue;
            }

            // Check if reminder interval has elapsed (e.g. 3 days)
            if ($req->last_reminder_sent_at && $req->last_reminder_sent_at->greaterThan(now()->subDays(3))) {
                continue;
            }

            // Dispatch automated reminder
            try {
                if ($wsService->hasConsent($req->client, 'reminder')) {
                    $pendingListStr = $pendingItems->pluck('item_name')->implode(', ');
                    $msgBody = "Reminder ({$req->reminder_count}/{$req->max_reminders}): Hello {$req->client->name}, please submit pending documents ({$pendingListStr}) for your return filing.";

                    // Create Communication record via provider-independent service
                    $commService = new \App\Services\Communication\CommunicationService();
                    $commService->send(
                        client: $req->client,
                        channel: \App\Enums\CommunicationChannel::WHATSAPP,
                        message: $msgBody,
                        subject: "Document Reminder #{$req->reminder_count}",
                        metadata: ['document_request_id' => $req->id]
                    );

                    $req->increment('reminder_count');
                    $req->update(['last_reminder_sent_at' => now()]);

                    $processedCount++;
                }
            } catch (\Exception $e) {
                $this->error("Failed sending reminder to client {$req->client_id}: " . $e->getMessage());
            }
        }

        $this->info("Processed {$processedCount} document reminders. Escalated {$escalatedCount} requests to tasks.");
        return 0;
    }
}

