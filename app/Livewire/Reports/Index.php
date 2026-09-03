<?php

namespace App\Livewire\Reports;

use App\Models\Client;
use App\Models\Service;
use App\Models\ComplianceInstance;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\Payment;
use App\Enums\ComplianceStatus;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    public function render()
    {
        $tenantId = Auth::user()->tenant_id;

        // Client Report
        $clientStats = [
            'total' => Client::where('tenant_id', $tenantId)->count(),
            'active' => Client::where('tenant_id', $tenantId)->whereHas('services')->count(),
            'individual' => Client::where('tenant_id', $tenantId)->where('client_type', 'individual')->count(),
            'company' => Client::where('tenant_id', $tenantId)->where('client_type', 'company')->count(),
        ];

        // Compliance Report
        $allCompliance = ComplianceInstance::where('tenant_id', $tenantId)->get();
        $complianceStats = [
            'total' => $allCompliance->count(),
            'overdue' => $allCompliance->where('status', ComplianceStatus::OVERDUE)->count(),
            'filed' => $allCompliance->whereIn('status', [ComplianceStatus::FILED, ComplianceStatus::ACKNOWLEDGED])->count(),
            'docs_pending' => $allCompliance->where('status', ComplianceStatus::DOCS_PENDING)->count(),
        ];

        // Billing Report
        $invoices = Invoice::where('tenant_id', $tenantId)->get();
        $payments = Payment::where('tenant_id', $tenantId)->get();
        $billingStats = [
            'total_invoiced' => $invoices->sum('total_amount'),
            'total_paid' => $payments->where('status', PaymentStatus::COMPLETED)->sum('amount'),
            'outstanding' => $invoices->where('status', '!=', \App\Enums\InvoiceStatus::CANCELLED)->sum('balance_due'),
        ];

        return view('livewire.reports.index', [
            'clientStats' => $clientStats,
            'complianceStats' => $complianceStats,
            'billingStats' => $billingStats,
        ]);
    }
}
