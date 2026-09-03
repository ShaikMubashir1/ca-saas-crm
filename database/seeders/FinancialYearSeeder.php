<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\FinancialYear;

class FinancialYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $years = [
            'FY 2025-26',
            'FY 2026-27',
        ];

        // For each tenant, ensure each year exists without duplication
        foreach (Tenant::all() as $tenant) {
            foreach ($years as $year) {
                FinancialYear::firstOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'year_label' => $year,
                    ]
                );
            }
        }
    }
}
?>
