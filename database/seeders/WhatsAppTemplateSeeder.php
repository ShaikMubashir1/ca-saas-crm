<?php

namespace Database\Seeders;

use App\Models\WhatsAppTemplate;
use App\Enums\WhatsAppTemplateCategory;
use App\Enums\WhatsAppTemplateStatus;
use Illuminate\Database\Seeder;

class WhatsAppTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'document_request',
                'category' => WhatsAppTemplateCategory::UTILITY->value,
                'language' => 'en_US',
                'body' => "Hi {{client_name}},\n\nPlease submit the required documents for {{service}} ({{financial_year}}):\n{{document_list}}\n\nUpload securely here: {{upload_link}}\n\nThank you,\n{{firm_name}}",
                'variables' => ['client_name', 'service', 'financial_year', 'document_list', 'upload_link', 'firm_name'],
                'status' => WhatsAppTemplateStatus::APPROVED->value,
            ],
            [
                'name' => 'document_reminder',
                'category' => WhatsAppTemplateCategory::UTILITY->value,
                'language' => 'en_US',
                'body' => "Reminder (Attempt {{attempt}}): Hello {{client_name}}, please upload pending documents for {{service}} by {{due_date}}.\nUpload link: {{upload_link}}\n\nRegards,\n{{firm_name}}",
                'variables' => ['attempt', 'client_name', 'service', 'due_date', 'upload_link', 'firm_name'],
                'status' => WhatsAppTemplateStatus::APPROVED->value,
            ],
            [
                'name' => 'compliance_due_reminder',
                'category' => WhatsAppTemplateCategory::UTILITY->value,
                'language' => 'en_US',
                'body' => "Hi {{client_name}},\n\nReminder: your {{compliance_name}} filing for {{period}} is due on {{due_date}}.\n\nPlease submit required documents promptly.\n\n{{firm_name}}",
                'variables' => ['client_name', 'compliance_name', 'period', 'due_date', 'firm_name'],
                'status' => WhatsAppTemplateStatus::APPROVED->value,
            ],
            [
                'name' => 'compliance_overdue_notice',
                'category' => WhatsAppTemplateCategory::UTILITY->value,
                'language' => 'en_US',
                'body' => "URGENT: Hello {{client_name}}, your {{compliance_name}} filing for {{period}} was due on {{due_date}} and is currently OVERDUE.\n\nPlease contact us immediately to avoid penalties.",
                'variables' => ['client_name', 'compliance_name', 'period', 'due_date'],
                'status' => WhatsAppTemplateStatus::APPROVED->value,
            ],
            [
                'name' => 'invoice_issued',
                'category' => WhatsAppTemplateCategory::UTILITY->value,
                'language' => 'en_US',
                'body' => "Hi {{client_name}},\n\nInvoice {{invoice_number}} of ₹{{amount}} has been generated for your {{service}} services. Due Date: {{due_date}}.\n\nThank you,\n{{firm_name}}",
                'variables' => ['client_name', 'invoice_number', 'amount', 'service', 'due_date', 'firm_name'],
                'status' => WhatsAppTemplateStatus::APPROVED->value,
            ],
            [
                'name' => 'payment_received_receipt',
                'category' => WhatsAppTemplateCategory::UTILITY->value,
                'language' => 'en_US',
                'body' => "Payment Received: Dear {{client_name}}, we received ₹{{amount}} for Invoice {{invoice_number}}. Remaining Balance: ₹{{balance_due}}.\n\nThank you for your business!",
                'variables' => ['client_name', 'amount', 'invoice_number', 'balance_due'],
                'status' => WhatsAppTemplateStatus::APPROVED->value,
            ],
            [
                'name' => 'filing_completed_ack',
                'category' => WhatsAppTemplateCategory::UTILITY->value,
                'language' => 'en_US',
                'body' => "Dear {{client_name}},\n\nYour {{compliance_name}} filing for {{period}} has been successfully FILED! Acknowledgement Number: {{ack_number}}.\n\nThank you,\n{{firm_name}}",
                'variables' => ['client_name', 'compliance_name', 'period', 'ack_number', 'firm_name'],
                'status' => WhatsAppTemplateStatus::APPROVED->value,
            ],
            [
                'name' => 'client_onboarding_welcome',
                'category' => WhatsAppTemplateCategory::UTILITY->value,
                'language' => 'en_US',
                'body' => "Welcome {{client_name}} to {{firm_name}}! We are glad to partner with you for your tax & compliance management. Reply anytime to upload documents or check status.",
                'variables' => ['client_name', 'firm_name'],
                'status' => WhatsAppTemplateStatus::APPROVED->value,
            ],
        ];

        foreach ($templates as $data) {
            WhatsAppTemplate::updateOrCreate(
                ['name' => $data['name'], 'tenant_id' => null],
                $data
            );
        }
    }
}
