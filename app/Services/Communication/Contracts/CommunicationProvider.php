<?php

namespace App\Services\Communication\Contracts;

use App\Models\Client;
use App\Models\Communication;

interface CommunicationProvider
{
    /**
     * Send communication payload via specific provider channel.
     *
     * @return array ['success' => bool, 'external_message_id' => ?string, 'error' => ?string, 'raw' => ?array]
     */
    public function send(Client $client, string $recipient, string $message, ?string $subject = null, array $metadata = []): array;
}
