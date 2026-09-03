<?php

namespace App\Services\Communication\WhatsApp\Providers;

use App\Services\Communication\WhatsApp\WhatsAppProviderInterface;

class NullWhatsAppProvider implements WhatsAppProviderInterface
{
    public function sendTemplateMessage(string $to, string $templateKey, array $variables = [], ?string $mediaUrl = null): array
    {
        return [
            'success' => true,
            'provider_message_id' => 'mock_msg_' . uniqid(),
            'status' => 'delivered',
            'driver' => 'null',
        ];
    }

    public function sendTextMessage(string $to, string $text): array
    {
        return [
            'success' => true,
            'provider_message_id' => 'mock_msg_' . uniqid(),
            'status' => 'delivered',
            'driver' => 'null',
        ];
    }

    public function sendDocumentMessage(string $to, string $documentUrl, string $filename, string $caption = ''): array
    {
        return [
            'success' => true,
            'provider_message_id' => 'mock_msg_' . uniqid(),
            'status' => 'delivered',
            'driver' => 'null',
        ];
    }

    public function handleIncomingMessage(array $payload): array
    {
        return [
            'from' => $payload['from'] ?? '919876543210',
            'body' => $payload['body'] ?? 'Mock reply text',
            'provider_message_id' => $payload['id'] ?? 'mock_in_' . uniqid(),
        ];
    }

    public function handleDeliveryStatus(array $payload): array
    {
        return [
            'provider_message_id' => $payload['id'] ?? 'mock_msg_id',
            'status' => $payload['status'] ?? 'delivered',
        ];
    }
}

