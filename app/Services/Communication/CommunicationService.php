<?php

namespace App\Services\Communication;

use App\Models\Client;
use App\Models\Communication;
use App\Models\TimelineEvent;
use App\Enums\CommunicationChannel;
use App\Enums\CommunicationStatus;
use App\Services\Communication\Contracts\CommunicationProvider;
use App\Services\Communication\Providers\WhatsAppProvider;
use App\Services\Communication\Providers\EmailProvider;
use App\Services\Communication\Providers\SmsProvider;
use Illuminate\Support\Facades\Auth;

class CommunicationService
{
    protected function getProvider(CommunicationChannel $channel): CommunicationProvider
    {
        return match ($channel) {
            CommunicationChannel::WHATSAPP => new WhatsAppProvider(),
            CommunicationChannel::EMAIL => new EmailProvider(),
            CommunicationChannel::SMS => new SmsProvider(),
        };
    }

    public function send(
        Client $client,
        CommunicationChannel $channel,
        string $message,
        ?string $subject = null,
        ?string $recipient = null,
        array $metadata = []
    ): Communication {
        $targetRecipient = $recipient ?? match ($channel) {
            CommunicationChannel::WHATSAPP, CommunicationChannel::SMS => $client->phone ?? '',
            CommunicationChannel::EMAIL => $client->email ?? '',
        };

        if (empty($targetRecipient)) {
            throw new \InvalidArgumentException("No valid {$channel->value} recipient address/phone found for client.");
        }

        // 1. Create Communication Record
        $comm = Communication::create([
            'tenant_id' => $client->tenant_id,
            'client_id' => $client->id,
            'user_id' => Auth::id(),
            'channel' => $channel,
            'status' => CommunicationStatus::PENDING,
            'recipient' => $targetRecipient,
            'subject' => $subject,
            'message' => $message,
            'metadata' => $metadata,
        ]);

        // 2. Resolve Provider & Dispatch
        $provider = $this->getProvider($channel);
        $result = $provider->send($client, $targetRecipient, $message, $subject, $metadata);

        // 3. Update Communication status
        if ($result['success']) {
            $comm->update([
                'status' => CommunicationStatus::DELIVERED,
                'external_message_id' => $result['external_message_id'] ?? null,
                'sent_at' => now(),
                'delivered_at' => now(),
            ]);
        } else {
            $comm->update([
                'status' => CommunicationStatus::FAILED,
                'failed_at' => now(),
                'failure_reason' => $result['error'] ?? 'Delivery failed',
            ]);
        }

        // 4. Log Timeline Event
        TimelineEvent::create([
            'tenant_id' => $client->tenant_id,
            'client_id' => $client->id,
            'user_id' => Auth::id(),
            'event_type' => "{$channel->label()} Communication",
            'description' => "Dispatched {$channel->label()} message to {$targetRecipient} - Status: " . ($result['success'] ? 'Delivered' : 'Failed'),
        ]);

        return $comm;
    }
}
