<?php

namespace App\Livewire\WhatsApp;

use App\Models\WhatsAppTemplate;
use App\Models\Client;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Enums\WhatsAppMessageDirection;
use App\Enums\WhatsAppMessageStatus;
use App\Enums\WhatsAppTemplateCategory;
use App\Services\Communication\CommunicationService;
use App\Enums\CommunicationChannel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Broadcast extends Component
{
    public ?string $selectedTemplateName = null;
    public string $filterClientType = '';
    public string $filterServiceType = '';
    public bool $marketingConsentOnly = true;
    public string $customMessage = '';

    public function updatedSelectedTemplateName($val): void
    {
        if ($val) {
            $tmpl = WhatsAppTemplate::where('name', $val)->first();
            if ($tmpl) {
                $this->customMessage = $tmpl->body;
            }
        }
    }

    public function sendBroadcast(): void
    {
        $this->validate([
            'customMessage' => 'required|string|min:5',
        ]);

        $tenantId = Auth::user()->tenant_id;

        $clientsQuery = Client::where('tenant_id', $tenantId)
            ->when($this->filterClientType, fn($q) => $q->where('client_type', $this->filterClientType))
            ->when($this->filterServiceType, fn($q) => $q->whereHas('services', fn($s) => $s->where('type', $this->filterServiceType)))
            ->when($this->marketingConsentOnly, fn($q) => $q->whereDoesntHave('whatsappConsent', fn($c) => $c->where('marketing_opt_in', false)));

        $recipients = $clientsQuery->get();

        if ($recipients->isEmpty()) {
            session()->flash('warning', 'No matching opted-in clients found for broadcast criteria.');
            return;
        }

        $commService = new CommunicationService();
        $dispatchedCount = 0;

        foreach ($recipients as $client) {
            if (empty($client->phone)) continue;

            $commService->send(
                client: $client,
                channel: CommunicationChannel::WHATSAPP,
                message: $this->customMessage,
                subject: "Broadcast Message"
            );

            $conv = WhatsAppConversation::firstOrCreate(
                ['tenant_id' => $tenantId, 'phone_number' => $client->phone],
                ['client_id' => $client->id, 'status' => 'open']
            );

            WhatsAppMessage::create([
                'tenant_id' => $tenantId,
                'conversation_id' => $conv->id,
                'client_id' => $client->id,
                'direction' => WhatsAppMessageDirection::OUTBOUND,
                'message_type' => $this->selectedTemplateName ? 'template' : 'text',
                'template_name' => $this->selectedTemplateName,
                'body' => $this->customMessage,
                'status' => WhatsAppMessageStatus::DELIVERED,
                'sent_at' => now(),
                'delivered_at' => now(),
            ]);

            $dispatchedCount++;
        }

        session()->flash('success', "Broadcast successfully dispatched to {$dispatchedCount} clients in Mock Mode.");
        $this->customMessage = '';
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;

        $templates = WhatsAppTemplate::where(function ($q) use ($tenantId) {
            $q->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
        })->where('status', 'approved')->get();

        $estimatedCount = Client::where('tenant_id', $tenantId)
            ->when($this->filterClientType, fn($q) => $q->where('client_type', $this->filterClientType))
            ->when($this->filterServiceType, fn($q) => $q->whereHas('services', fn($s) => $s->where('type', $this->filterServiceType)))
            ->when($this->marketingConsentOnly, fn($q) => $q->whereDoesntHave('whatsappConsent', fn($c) => $c->where('marketing_opt_in', false)))
            ->count();

        return view('livewire.whats-app.broadcast', [
            'templates' => $templates,
            'estimatedCount' => $estimatedCount,
        ]);
    }
}
