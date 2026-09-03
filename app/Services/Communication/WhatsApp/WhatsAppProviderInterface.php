<?php

namespace App\Services\Communication\WhatsApp;

interface WhatsAppProviderInterface
{
    public function sendTemplateMessage(string $to, string $templateKey, array $variables = [], ?string $mediaUrl = null): array;
    public function sendTextMessage(string $to, string $text): array;
    public function sendDocumentMessage(string $to, string $documentUrl, string $filename, string $caption = ''): array;
    public function handleIncomingMessage(array $payload): array;
    public function handleDeliveryStatus(array $payload): array;
}

