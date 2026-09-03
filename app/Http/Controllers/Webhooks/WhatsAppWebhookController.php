<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\CommunicationLog;
use App\Models\CommunicationMessage;
use App\Enums\MessageStatus;
use Illuminate\Http\Request;

class WhatsAppWebhookController extends Controller
{
    /**
     * Handle Webhook Verification GET Request.
     */
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $expectedToken = config('services.whatsapp.webhook_token', 'ca_crm_webhook_secret');

        if ($mode === 'subscribe' && $token === $expectedToken) {
            return response($challenge, 200);
        }

        return response()->json(['error' => 'Forbidden'], 403);
    }

    /**
     * Handle Incoming Webhook Events POST Request.
     */
    public function handle(Request $request)
    {
        $payload = $request->all();

        // Log raw webhook event
        CommunicationLog::create([
            'tenant_id' => 1, // Fallback system tenant
            'client_id' => null,
            'communication_message_id' => null,
            'event' => 'Webhook Event Received',
            'payload' => $payload,
        ]);

        // Process inbound message or status update callbacks
        if (isset($payload['from']) && isset($payload['body'])) {
            $phone = $payload['from'];
            $bodyText = trim($payload['body']);
            $tenantId = $payload['tenant_id'] ?? 1;

            $client = \App\Models\Client::where('phone', $phone)->first();
            $conv = \App\Models\WhatsAppConversation::firstOrCreate(
                ['tenant_id' => $tenantId, 'phone_number' => $phone],
                ['client_id' => $client?->id, 'status' => 'open']
            );

            // Handle STOP opt-out
            if (strtoupper($bodyText) === 'STOP' && $client) {
                \App\Models\WhatsAppConsent::updateOrCreate(
                    ['tenant_id' => $tenantId, 'client_id' => $client->id],
                    ['phone_number' => $phone, 'marketing_opt_in' => false, 'transactional_opt_in' => false, 'opted_out_at' => now()]
                );
            }

            \App\Models\WhatsAppMessage::create([
                'tenant_id' => $tenantId,
                'conversation_id' => $conv->id,
                'client_id' => $client?->id,
                'direction' => \App\Enums\WhatsAppMessageDirection::INBOUND,
                'message_type' => 'text',
                'provider_message_id' => $payload['id'] ?? 'wa_in_' . uniqid(),
                'body' => $bodyText,
                'status' => \App\Enums\WhatsAppMessageStatus::DELIVERED,
                'sent_at' => now(),
                'delivered_at' => now(),
            ]);

            $conv->update(['last_message_at' => now()]);
        } elseif (isset($payload['provider_message_id']) && isset($payload['status'])) {
            $msg = CommunicationMessage::where('provider_message_id', $payload['provider_message_id'])->first();

            if ($msg) {
                $statusStr = strtolower($payload['status']);
                if ($statusStr === 'delivered') {
                    $msg->update(['status' => MessageStatus::DELIVERED->value, 'delivered_at' => now()]);
                } elseif ($statusStr === 'read') {
                    $msg->update(['status' => MessageStatus::READ->value, 'read_at' => now()]);
                } elseif ($statusStr === 'failed') {
                    $msg->update([
                        'status' => MessageStatus::FAILED->value,
                        'failed_at' => now(),
                        'error_message' => $payload['error'] ?? 'Delivery failed',
                    ]);
                }
            }
        }

        return response()->json(['status' => 'success'], 200);
    }
}

