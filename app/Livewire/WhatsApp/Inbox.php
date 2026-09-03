<?php

namespace App\Livewire\WhatsApp;

use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppTemplate;
use App\Models\WhatsAppConsent;
use App\Models\Client;
use App\Models\TimelineEvent;
use App\Enums\WhatsAppConversationStatus;
use App\Enums\WhatsAppMessageDirection;
use App\Enums\WhatsAppMessageStatus;
use App\Services\Communication\CommunicationService;
use App\Enums\CommunicationChannel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Inbox extends Component
{
    use WithPagination;

    public string $search = '';
    public string $selectedStatus = 'all';
    public ?int $selectedConversationId = null;

    // Chat compose state
    public string $messageText = '';
    public ?string $selectedTemplateName = null;

    public function mount(?int $conversation = null): void
    {
        if ($conversation) {
            $this->selectedConversationId = $conversation;
        }
    }

    public function selectConversation(int $id): void
    {
        $this->selectedConversationId = $id;
    }

    public function applyTemplate(): void
    {
        if ($this->selectedTemplateName) {
            $tmpl = WhatsAppTemplate::where('name', $this->selectedTemplateName)->first();
            if ($tmpl) {
                $this->messageText = $tmpl->body;
            }
        }
    }

    public function sendMessage(): void
    {
        $this->validate([
            'messageText' => 'required|string|max:1000',
        ]);

        $tenantId = Auth::user()->tenant_id;
        $conv = WhatsAppConversation::where('tenant_id', $tenantId)->findOrFail($this->selectedConversationId);

        // Verify marketing/transactional consent check
        if ($conv->client && $conv->client->whatsappConsent && !$conv->client->whatsappConsent->transactional_opt_in) {
            session()->flash('warning', 'Client has opted out of WhatsApp messages.');
            return;
        }

        $client = $conv->client ?? Client::firstOrCreate(
            ['tenant_id' => $tenantId, 'phone' => $conv->phone_number],
            ['name' => 'WhatsApp Contact ' . substr($conv->phone_number, -4), 'entity_type' => 'Individual']
        );

        $commService = new CommunicationService();
        $comm = $commService->send(
            client: $client,
            channel: CommunicationChannel::WHATSAPP,
            message: $this->messageText,
            recipient: $conv->phone_number
        );

        // Store outbound WhatsAppMessage record
        $msg = WhatsAppMessage::create([
            'tenant_id' => $tenantId,
            'conversation_id' => $conv->id,
            'client_id' => $client->id,
            'direction' => WhatsAppMessageDirection::OUTBOUND,
            'message_type' => $this->selectedTemplateName ? 'template' : 'text',
            'template_name' => $this->selectedTemplateName,
            'body' => $this->messageText,
            'status' => WhatsAppMessageStatus::DELIVERED,
            'sent_at' => now(),
            'delivered_at' => now(),
        ]);

        $conv->update([
            'last_message_at' => now(),
            'status' => WhatsAppConversationStatus::OPEN,
        ]);

        $this->messageText = '';
        $this->selectedTemplateName = null;
        session()->flash('success', 'Message dispatched.');
    }

    public function toggleConversationStatus(int $id): void
    {
        $tenantId = Auth::user()->tenant_id;
        $conv = WhatsAppConversation::where('tenant_id', $tenantId)->findOrFail($id);

        $newStatus = $conv->status === WhatsAppConversationStatus::OPEN
            ? WhatsAppConversationStatus::CLOSED
            : WhatsAppConversationStatus::OPEN;

        $conv->update(['status' => $newStatus]);
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;

        $conversations = WhatsAppConversation::where('tenant_id', $tenantId)
            ->with(['client.whatsappConsent', 'messages' => fn($q) => $q->latest()->limit(1)])
            ->when($this->search, function ($q) {
                $q->where('phone_number', 'like', "%{$this->search}%")
                  ->orWhereHas('client', fn($c) => $c->where('name', 'like', "%{$this->search}%"));
            })
            ->when($this->selectedStatus !== 'all', fn($q) => $q->where('status', $this->selectedStatus))
            ->orderBy('last_message_at', 'desc')
            ->get();

        $activeConversation = null;
        $activeMessages = collect();

        if ($this->selectedConversationId) {
            $activeConversation = WhatsAppConversation::where('tenant_id', $tenantId)
                ->with(['client.whatsappConsent'])
                ->find($this->selectedConversationId);

            if ($activeConversation) {
                $activeMessages = WhatsAppMessage::where('tenant_id', $tenantId)
                    ->where('conversation_id', $activeConversation->id)
                    ->orderBy('created_at', 'asc')
                    ->get();
            }
        } elseif ($conversations->isNotEmpty()) {
            $activeConversation = $conversations->first();
            $this->selectedConversationId = $activeConversation->id;
            $activeMessages = WhatsAppMessage::where('tenant_id', $tenantId)
                ->where('conversation_id', $activeConversation->id)
                ->orderBy('created_at', 'asc')
                ->get();
        }

        $templates = WhatsAppTemplate::where(function ($q) use ($tenantId) {
            $q->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
        })->where('status', 'approved')->get();

        return view('livewire.whats-app.inbox', [
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
            'activeMessages' => $activeMessages,
            'templates' => $templates,
        ]);
    }
}
