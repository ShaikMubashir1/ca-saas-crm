<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\DocumentChecklist;
use App\Models\DocumentChecklistItem;
use Illuminate\Database\Seeder;

class DocumentChecklistTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        $templates = [
            'itr' => [
                'title' => 'Income Tax Return (ITR) Document Checklist',
                'description' => 'Standard required & optional documents for ITR filing',
                'items' => [
                    ['name' => 'PAN Card', 'document_type' => 'PAN', 'is_required' => true, 'sort_order' => 1],
                    ['name' => 'Aadhaar Card', 'document_type' => 'Aadhaar', 'is_required' => true, 'sort_order' => 2],
                    ['name' => 'Form 16 / Form 16A', 'document_type' => 'Form 16', 'is_required' => true, 'sort_order' => 3],
                    ['name' => 'Bank Statements (All accounts for FY)', 'document_type' => 'Bank Statement', 'is_required' => true, 'sort_order' => 4],
                    ['name' => 'Section 80C Deductions Proofs (LIC, PPF, ELSS)', 'document_type' => '80C Proof', 'is_required' => false, 'sort_order' => 5],
                    ['name' => 'Rent Receipts & House Owner PAN (HRA Claim)', 'document_type' => 'Rent Receipt', 'is_required' => false, 'sort_order' => 6],
                    ['name' => 'Capital Gains Tax Statement (Mutual Funds / Shares)', 'document_type' => 'Capital Gains', 'is_required' => false, 'sort_order' => 7],
                ],
            ],
            'gst' => [
                'title' => 'GST Return Filing Document Checklist',
                'description' => 'Required monthly/quarterly records for GST filing',
                'items' => [
                    ['name' => 'Sales Register / Outward Invoices', 'document_type' => 'Sales Register', 'is_required' => true, 'sort_order' => 1],
                    ['name' => 'Purchase Register / Inward Invoices (ITC Claim)', 'document_type' => 'Purchase Register', 'is_required' => true, 'sort_order' => 2],
                    ['name' => 'Bank Statement (For Payment / Cash ledger reconciliation)', 'document_type' => 'Bank Statement', 'is_required' => true, 'sort_order' => 3],
                    ['name' => 'Expense Records & Input Tax Receipts', 'document_type' => 'Expense Records', 'is_required' => false, 'sort_order' => 4],
                    ['name' => 'Previous GST Returns Acknowledgement', 'document_type' => 'Previous GST Return', 'is_required' => false, 'sort_order' => 5],
                ],
            ],
            'tds' => [
                'title' => 'TDS Return Filing Document Checklist',
                'description' => 'Required deduction & challan records for quarterly TDS returns',
                'items' => [
                    ['name' => 'TDS Payment Challans (ITNS 281)', 'document_type' => 'TDS Challan', 'is_required' => true, 'sort_order' => 1],
                    ['name' => 'Salary / Contractor Payment Register', 'document_type' => 'Payment Register', 'is_required' => true, 'sort_order' => 2],
                    ['name' => 'Deductee Details & PAN List', 'document_type' => 'Deductee Details', 'is_required' => true, 'sort_order' => 3],
                    ['name' => 'Previous Quarter TDS Return Copy', 'document_type' => 'Previous TDS Return', 'is_required' => false, 'sort_order' => 4],
                ],
            ],
            'audit' => [
                'title' => 'Statutory / Tax Audit Document Checklist',
                'description' => 'Comprehensive financial books & ledger checklist for audit',
                'items' => [
                    ['name' => 'Trial Balance & Final Accounts Draft', 'document_type' => 'Trial Balance', 'is_required' => true, 'sort_order' => 1],
                    ['name' => 'General Ledger & Cash Book', 'document_type' => 'Ledger', 'is_required' => true, 'sort_order' => 2],
                    ['name' => 'Bank Statements & Reconciliations', 'document_type' => 'Bank Statement', 'is_required' => true, 'sort_order' => 3],
                    ['name' => 'Fixed Asset Register & Invoices', 'document_type' => 'Fixed Asset Register', 'is_required' => true, 'sort_order' => 4],
                    ['name' => 'Previous Year Tax Audit Report & ITR', 'document_type' => 'Previous Audit Report', 'is_required' => false, 'sort_order' => 5],
                ],
            ],
            'roc' => [
                'title' => 'ROC Compliance & Annual Return Checklist',
                'description' => 'Required secretarial and financial records for ROC filings',
                'items' => [
                    ['name' => 'Audited Financial Statements (MGT-7 / AOC-4)', 'document_type' => 'Financial Statement', 'is_required' => true, 'sort_order' => 1],
                    ['name' => 'Directors Report & Statutory Registers', 'document_type' => 'ROC', 'is_required' => true, 'sort_order' => 2],
                    ['name' => 'Board Resolution Copies', 'document_type' => 'Agreement', 'is_required' => true, 'sort_order' => 3],
                    ['name' => 'Shareholder / MBP-1 Declarations', 'document_type' => 'Other', 'is_required' => false, 'sort_order' => 4],
                ],
            ],
        ];

        foreach ($tenants as $tenant) {
            foreach ($templates as $serviceType => $data) {
                $checklist = DocumentChecklist::firstOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'service_type' => $serviceType,
                        'is_template' => true,
                    ],
                    [
                        'title' => $data['title'],
                        'description' => $data['description'],
                    ]
                );

                foreach ($data['items'] as $item) {
                    DocumentChecklistItem::firstOrCreate(
                        [
                            'tenant_id' => $tenant->id,
                            'document_checklist_id' => $checklist->id,
                            'name' => $item['name'],
                        ],
                        [
                            'document_type' => $item['document_type'],
                            'is_required' => $item['is_required'],
                            'sort_order' => $item['sort_order'],
                            'status' => 'pending',
                        ]
                    );
                }
            }
        }
    }
}
