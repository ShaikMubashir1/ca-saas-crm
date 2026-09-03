<?php

namespace Database\Seeders;

use App\Models\ComplianceTemplate;
use Illuminate\Database\Seeder;

class ComplianceTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'GST Return GSTR-1 (Outward Supplies)',
                'code' => 'GST_GSTR1',
                'service_type' => 'gst',
                'frequency' => 'monthly',
                'applicable_client_types' => ['Private Limited Company', 'Public Limited Company', 'LLP', 'Partnership Firm', 'Sole Proprietorship'],
                'description' => 'Monthly filing of outward supplies details under GST.',
                'default_due_day' => 11,
            ],
            [
                'name' => 'GST Return GSTR-3B (Summary Return)',
                'code' => 'GST_GSTR3B',
                'service_type' => 'gst',
                'frequency' => 'monthly',
                'applicable_client_types' => ['Private Limited Company', 'Public Limited Company', 'LLP', 'Partnership Firm', 'Sole Proprietorship'],
                'description' => 'Monthly summary return and tax payment under GST.',
                'default_due_day' => 20,
            ],
            [
                'name' => 'GST Annual Return GSTR-9',
                'code' => 'GST_GSTR9',
                'service_type' => 'gst',
                'frequency' => 'annual',
                'applicable_client_types' => ['Private Limited Company', 'Public Limited Company', 'LLP', 'Partnership Firm', 'Sole Proprietorship'],
                'description' => 'Annual consolidated return under GST.',
                'default_due_day' => 31,
                'default_due_month' => 12,
            ],
            [
                'name' => 'Income Tax Return (ITR)',
                'code' => 'ITR_FILING',
                'service_type' => 'itr',
                'frequency' => 'annual',
                'applicable_client_types' => ['Individual', 'HUF', 'Sole Proprietorship', 'Partnership Firm', 'LLP', 'Private Limited Company', 'Trust/Society/NGO'],
                'description' => 'Annual Income Tax Return filing.',
                'default_due_day' => 31,
                'default_due_month' => 7,
            ],
            [
                'name' => 'Advance Tax Quarterly Installment',
                'code' => 'ADVANCE_TAX',
                'service_type' => 'itr',
                'frequency' => 'quarterly',
                'applicable_client_types' => ['Individual', 'Sole Proprietorship', 'Partnership Firm', 'LLP', 'Private Limited Company'],
                'description' => 'Quarterly payment of Advance Income Tax.',
                'default_due_day' => 15,
            ],
            [
                'name' => 'TDS Quarterly Return (Form 24Q / 26Q / 27Q)',
                'code' => 'TDS_RETURN',
                'service_type' => 'tds',
                'frequency' => 'quarterly',
                'applicable_client_types' => ['Private Limited Company', 'Public Limited Company', 'LLP', 'Partnership Firm', 'Sole Proprietorship'],
                'description' => 'Quarterly statement of Tax Deducted at Source.',
                'default_due_day' => 31,
            ],
            [
                'name' => 'TDS Monthly Payment Challan 281',
                'code' => 'TDS_PAYMENT',
                'service_type' => 'tds',
                'frequency' => 'monthly',
                'applicable_client_types' => ['Private Limited Company', 'Public Limited Company', 'LLP', 'Partnership Firm', 'Sole Proprietorship'],
                'description' => 'Monthly deposit of deducted TDS into government portal.',
                'default_due_day' => 7,
            ],
            [
                'name' => 'ROC Annual Return (Form AOC-4 & MGT-7)',
                'code' => 'ROC_ANNUAL',
                'service_type' => 'roc',
                'frequency' => 'annual',
                'applicable_client_types' => ['Private Limited Company', 'Public Limited Company', 'OPC'],
                'description' => 'Annual financial statements & return filing with Registrar of Companies.',
                'default_due_day' => 30,
                'default_due_month' => 10,
            ],
            [
                'name' => 'Tax Audit u/s 44AB',
                'code' => 'TAX_AUDIT',
                'service_type' => 'audit',
                'frequency' => 'annual',
                'applicable_client_types' => ['Private Limited Company', 'Public Limited Company', 'LLP', 'Partnership Firm', 'Sole Proprietorship'],
                'description' => 'Statutory Tax Audit report u/s 44AB of Income Tax Act.',
                'default_due_day' => 30,
                'default_due_month' => 9,
            ],
        ];

        foreach ($templates as $data) {
            ComplianceTemplate::updateOrCreate(
                ['code' => $data['code']],
                $data
            );
        }
    }
}
