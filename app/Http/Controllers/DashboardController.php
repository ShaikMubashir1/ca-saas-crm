<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Task;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;

        // Client metrics
        $totalClients = Client::where('tenant_id', $tenantId)->count();
        $activeClients = Client::where('tenant_id', $tenantId)->whereHas('services')->count();

        // Task metrics
        $pendingTasks = Task::where('tenant_id', $tenantId)->where('status', 'pending')->count();
        $myTasks = Task::where('tenant_id', $tenantId)->where('assigned_to', Auth::id())->where('status', '!=', 'completed')->count();
        $overdueTasks = Task::where('tenant_id', $tenantId)->where('status', '!=', 'completed')->where('due_date', '<', date('Y-m-d'))->count();

        // Compliance metrics
        $complianceInstances = \App\Models\ComplianceInstance::where('tenant_id', $tenantId)->get();
        $compMetrics = [
            'due_today' => $complianceInstances->filter(fn($i) => $i->due_date && $i->due_date->isToday() && !in_array($i->status->value, ['filed', 'acknowledged', 'cancelled']))->count(),
            'due_7_days' => $complianceInstances->filter(fn($i) => $i->due_date && $i->due_date->diffInDays(now()) <= 7 && !in_array($i->status->value, ['filed', 'acknowledged', 'cancelled']))->count(),
            'overdue' => $complianceInstances->where('status', \App\Enums\ComplianceStatus::OVERDUE)->count(),
            'filed' => $complianceInstances->whereIn('status', [\App\Enums\ComplianceStatus::FILED, \App\Enums\ComplianceStatus::ACKNOWLEDGED])->count(),
        ];

        // Document metrics
        $totalDocuments = Document::where('tenant_id', $tenantId)->count();
        $pendingDocRequests = \App\Models\DocumentRequest::where('tenant_id', $tenantId)->where('status', 'sent')->count();

        // Billing metrics
        $invoices = \App\Models\Invoice::where('tenant_id', $tenantId)->get();
        $payments = \App\Models\Payment::where('tenant_id', $tenantId)->get();
        $billingMetrics = [
            'total_invoiced' => $invoices->sum('total_amount'),
            'total_paid' => $payments->where('status', \App\Enums\PaymentStatus::COMPLETED)->sum('amount'),
            'outstanding' => $invoices->where('status', '!=', \App\Enums\InvoiceStatus::CANCELLED)->sum('balance_due'),
            'overdue' => $invoices->where('status', \App\Enums\InvoiceStatus::OVERDUE)->sum('balance_due'),
        ];

        // WhatsApp metrics
        $openConversations = \App\Models\WhatsAppConversation::where('tenant_id', $tenantId)->where('status', \App\Enums\WhatsAppConversationStatus::OPEN)->count();

        // Upcoming compliance list
        $upcomingCompliance = \App\Models\ComplianceInstance::where('tenant_id', $tenantId)
            ->with(['client', 'template'])
            ->whereNotIn('status', [\App\Enums\ComplianceStatus::FILED, \App\Enums\ComplianceStatus::ACKNOWLEDGED, \App\Enums\ComplianceStatus::CANCELLED])
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        // Recent Timeline activities
        $activities = \App\Models\TimelineEvent::where('tenant_id', $tenantId)
            ->with('client')
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard', [
            'totalClients' => $totalClients,
            'activeClients' => $activeClients,
            'pendingTasks' => $pendingTasks,
            'myTasks' => $myTasks,
            'overdueTasks' => $overdueTasks,
            'compMetrics' => $compMetrics,
            'totalDocuments' => $totalDocuments,
            'pendingDocRequests' => $pendingDocRequests,
            'billingMetrics' => $billingMetrics,
            'openConversations' => $openConversations,
            'upcomingCompliance' => $upcomingCompliance,
            'activities' => $activities,
        ]);
    }
}
