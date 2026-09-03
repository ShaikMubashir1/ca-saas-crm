<?php

namespace App\Jobs;

use App\Models\Communication;
use App\Models\TimelineEvent;
use App\Enums\CommunicationStatus;
use App\Services\Communication\Providers\WhatsAppProvider;
use App\Services\Communication\Providers\EmailProvider;
use App\Services\Communication\Providers\SmsProvider;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendCommunication implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public Communication $communication;

    public function __construct(Communication $communication)
    {
        $this->communication = $communication;
    }

    public function handle(): void
    {
        $comm = $this->communication;
        $client = $comm->client;

        if (!$client) {
            $comm->update([
                'status' => CommunicationStatus::FAILED,
                'failed_at' => now(),
                'failure_reason' => 'Client not found.',
            ]);
            return;
        }

        $provider = match ($comm->channel) {
            \App\Enums\CommunicationChannel::WHATSAPP => new WhatsAppProvider(),
            \App\Enums\CommunicationChannel::EMAIL => new EmailProvider(),
            \App\Enums\CommunicationChannel::SMS => new SmsProvider(),
        };

        $result = $provider->send($client, $comm->recipient, $comm->message, $comm->subject, $comm->metadata ?? []);

        if ($result['success']) {
            $comm->update([
                'status' => CommunicationStatus::DELIVERED,
                'external_message_id' => $result['external_message_id'] ?? null,
                'sent_at' => now(),
                'delivered_at' => now(),
            ]);

            TimelineEvent::create([
                'tenant_id' => $comm->tenant_id,
                'client_id' => $comm->client_id,
                'user_id' => $comm->user_id,
                'event_type' => 'Queued Communication Sent',
                'description' => "Async {$comm->channel->label()} delivered to {$comm->recipient}",
            ]);
        } else {
            $comm->update([
                'status' => CommunicationStatus::FAILED,
                'failed_at' => now(),
                'failure_reason' => $result['error'] ?? 'Queued delivery failed',
            ]);
        }
    }
}
