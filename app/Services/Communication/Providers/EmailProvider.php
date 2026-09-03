<?php

namespace App\Services\Communication\Providers;

use App\Models\Client;
use App\Services\Communication\Contracts\CommunicationProvider;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailProvider implements CommunicationProvider
{
    public function send(Client $client, string $recipient, string $message, ?string $subject = null, array $metadata = []): array
    {
        try {
            $emailSubject = $subject ?? 'Notification from CA Firm';
            
            Mail::raw($message, function ($mail) use ($recipient, $emailSubject) {
                $mail->to($recipient)->subject($emailSubject);
            });

            return [
                'success' => true,
                'external_message_id' => 'mail_' . uniqid(),
                'error' => null,
                'raw' => ['driver' => config('mail.default')],
            ];
        } catch (\Throwable $e) {
            Log::error("Email dispatch failed for client {$client->id}: " . $e->getMessage());
            return [
                'success' => false,
                'external_message_id' => null,
                'error' => $e->getMessage(),
                'raw' => null,
            ];
        }
    }
}
