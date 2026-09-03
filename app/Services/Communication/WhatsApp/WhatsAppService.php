<?php

namespace App\Services\Communication\WhatsApp;

use App\Models\Client;
use App\Models\CommunicationMessage;
use App\Models\CommunicationConsent;
use App\Models\CommunicationLog;
use App\Enums\CommunicationChannel;
use App\Enums\MessageDirection;
use App\Enums\MessageStatus;
use App\Enums\ConsentStatus;
use Illuminate\Support\Facades\Auth;

class WhatsAppService
{
    protected WhatsAppProviderInterface $provider;

    public function __construct(?WhatsAppProviderInterface $provider = null)
    {
        $this->provider = $provider ?? new Providers\NullWhatsAppProvider();
    }

    public function hasConsent(Client $client, string $purpose = 'utility'): bool
    {
        $consent = CommunicationConsent::where('tenant_id', $client->tenant_id)
            ->where('client_id', $client->id)
            ->where('channel', CommunicationChannel::WHATSAPP->value)
            ->where('purpose', $purpose)
            ->first();

        return $consent ? $consent->status === ConsentStatus::OPTED_IN : true;
    }

    public function sendTemplate(Client $client, string $templateKey, array $variables = [], ?string $subject = null): CommunicationMessage
    {
        if (!$this->hasConsent($client, 'utility')) {
            throw new \Exception('Client has opted out of WhatsApp messages.');
        }

        $recipient = $client->phone ?? '910000000000';
        $res = $this->provider->sendTemplateMessage($recipient, $templateKey, $variables);

        $body = 'Template: ' . $templateKey . ' | Variables: ' . json_encode($variables);

        $msg = CommunicationMessage::create([
            'tenant_id' => $client->tenant_id,
            'client_id' => $client->id,
            'user_id' => Auth::id(),
            'channel' => CommunicationChannel::WHATSAPP->value,
            'direction' => MessageDirection::OUTBOUND->value,
            'message_type' => 'template',
            'provider_message_id' => $res['provider_message_id'] ?? null,
            'recipient' => $recipient,
            'subject' => $subject ?? ('WhatsApp Template: ' . $templateKey),
            'body' => $body,
            'status' => $res['success'] ? MessageStatus::DELIVERED->value : MessageStatus::FAILED->value,
            'sent_at' => now(),
            'delivered_at' => $res['success'] ? now() : null,
        ]);

        CommunicationLog::create([
            'tenant_id' => $client->tenant_id,
            'client_id' => $client->id,
            'communication_message_id' => $msg->id,
            'event' => 'Message Sent',
            'payload' => $res,
        ]);

        return $msg;
    }

    public function sendText(Client $client, string $body): CommunicationMessage
    {
        if (!$this->hasConsent($client, 'utility')) {
            throw new \Exception('Client has opted out of WhatsApp messages.');
        }

        $recipient = $client->phone ?? '910000000000';
        $res = $this->provider->sendTextMessage($recipient, $body);

        $msg = CommunicationMessage::create([
            'tenant_id' => $client->tenant_id,
            'client_id' => $client->id,
            'user_id' => Auth::id(),
            'channel' => CommunicationChannel::WHATSAPP->value,
            'direction' => MessageDirection::OUTBOUND->value,
            'message_type' => 'text',
            'provider_message_id' => $res['provider_message_id'] ?? null,
            'recipient' => $recipient,
            'body' => $body,
            'status' => $res['success'] ? MessageStatus::DELIVERED->value : MessageStatus::FAILED->value,
            'sent_at' => now(),
            'delivered_at' => $res['success'] ? now() : null,
        ]);

        CommunicationLog::create([
            'tenant_id' => $client->tenant_id,
            'client_id' => $client->id,
            'communication_message_id' => $msg->id,
            'event' => 'Text Message Sent',
            'payload' => $res,
        ]);

        return $msg;
    }
}

