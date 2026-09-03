<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\CommunicationTemplate;
use Illuminate\Database\Seeder;

class CommunicationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        $standardTemplates = [
            [
                'name' => 'Document Request Notification',
                'category' => 'utility',
                'template_key' => 'doc_request',
                'body' => "Hello {{client_name}},\n\nPlease submit the following pending documents for your {{service_name}} (FY {{financial_year}}):\n\n{{pending_documents}}\n\nPlease upload them using your secure portal link: {{upload_link}}\n\nThank you,\n{{firm_name}}",
                'variables' => ['client_name', 'service_name', 'financial_year', 'pending_documents', 'upload_link', 'firm_name'],
            ],
            [
                'name' => 'GST Filing Due Reminder',
                'category' => 'reminder',
                'template_key' => 'gst_due_reminder',
                'body' => "Hello {{client_name}},\n\nThis is a friendly reminder that your GST Return filing for {{financial_year}} is due on {{deadline}}. Please ensure sales and purchase registers are submitted.\n\nThank you,\n{{firm_name}}",
                'variables' => ['client_name', 'financial_year', 'deadline', 'firm_name'],
            ],
            [
                'name' => 'ITR Return Filing Due Reminder',
                'category' => 'reminder',
                'template_key' => 'itr_due_reminder',
                'body' => "Hello {{client_name}},\n\nYour Income Tax Return filing deadline ({{deadline}}) is approaching for FY {{financial_year}}. Please upload your Form 16 and bank statements.\n\nThank you,\n{{firm_name}}",
                'variables' => ['client_name', 'financial_year', 'deadline', 'firm_name'],
            ],
            [
                'name' => 'Payment & Fee Invoice Reminder',
                'category' => 'reminder',
                'template_key' => 'payment_reminder',
                'body' => "Hello {{client_name}},\n\nAn outstanding balance of ₹{{payment_amount}} is due for your {{service_name}} compliance. Please find your invoice attached or reach out to {{assigned_staff}} for queries.\n\nThank you,\n{{firm_name}}",
                'variables' => ['client_name', 'payment_amount', 'service_name', 'assigned_staff', 'firm_name'],
            ],
            [
                'name' => 'Welcome & Client Onboarding',
                'category' => 'marketing',
                'template_key' => 'welcome_onboarding',
                'body' => "Welcome to {{firm_name}}, {{client_name}}!\n\nWe have set up your client portal workspace. Your assigned compliance officer is {{assigned_staff}}.\n\nBest regards,\n{{firm_name}}",
                'variables' => ['firm_name', 'client_name', 'assigned_staff'],
            ],
        ];

        foreach ($tenants as $tenant) {
            foreach ($standardTemplates as $tpl) {
                CommunicationTemplate::firstOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'template_key' => $tpl['template_key'],
                    ],
                    [
                        'name' => $tpl['name'],
                        'category' => $tpl['category'],
                        'channel' => 'whatsapp',
                        'language' => 'en',
                        'body' => $tpl['body'],
                        'variables' => $tpl['variables'],
                        'status' => 'approved',
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}

