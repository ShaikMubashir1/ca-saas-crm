<?php

namespace App\Services\Communication\Providers;

use App\Models\Client;
use App\Services\Communication\Contracts\CommunicationProvider;
use Illuminate\Support\Facades\Log;

class SmsProvider implements CommunicationProvider
{
    public function send(Client $client, string $recipient, string $message, ?string $subject = null, array $metadata = []): array
    {
        Log::info("SMS dispatch skipped for client {$client->id}: SMS provider placeholder executed.");

        return [
            'success' => true,
            'external_message_id' => 'sms_mock_' . uniqid(),
            'error' => null,
            'raw' => ['driver' => 'sms_placeholder', 'recipient' => $recipient],
        ];
    }
}
