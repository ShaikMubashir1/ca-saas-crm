<?php

namespace App\Services\Communication\Providers;

use App\Models\Client;
use App\Services\Communication\Contracts\CommunicationProvider;
use Illuminate\Support\Facades\Log;

class WhatsAppProvider implements CommunicationProvider
{
    public function send(Client $client, string $recipient, string $message, ?string $subject = null, array $metadata = []): array
    {
        $enabled = config('services.whatsapp.enabled', false);
        $accessToken = config('services.whatsapp.access_token');
        $phoneId = config('services.whatsapp.phone_number_id');

        if (!$enabled || empty($accessToken) || empty($phoneId)) {
            Log::info("WhatsApp dispatch skipped for client {$client->id}: WhatsApp API disabled or credentials missing.");
            
            // Safe fallback driver response (mocking delivery in dev/test)
            return [
                'success' => true,
                'external_message_id' => 'wa_mock_' . uniqid(),
                'error' => null,
                'raw' => ['driver' => 'mock_null_driver', 'recipient' => $recipient],
            ];
        }

        // Live Meta WhatsApp Cloud API call structure
        try {
            $version = config('services.whatsapp.api_version', 'v19.0');
            $url = "https://graph.facebook.com/{$version}/{$phoneId}/messages";

            $response = \Illuminate\Support\Facades\Http::withToken($accessToken)
                ->post($url, [
                    'messaging_product' => 'whatsapp',
                    'to' => $recipient,
                    'type' => 'text',
                    'text' => ['body' => $message],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'external_message_id' => $data['messages'][0]['id'] ?? 'wa_' . uniqid(),
                    'error' => null,
                    'raw' => $data,
                ];
            }

            return [
                'success' => false,
                'external_message_id' => null,
                'error' => $response->body(),
                'raw' => $response->json(),
            ];
        } catch (\Throwable $e) {
            Log::error("WhatsApp dispatch error for client {$client->id}: " . $e->getMessage());
            return [
                'success' => false,
                'external_message_id' => null,
                'error' => $e->getMessage(),
                'raw' => null,
            ];
        }
    }
}
