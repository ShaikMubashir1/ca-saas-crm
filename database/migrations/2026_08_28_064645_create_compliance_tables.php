<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('compliance_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('service_type'); // itr, gst, tds, roc, audit
            $table->string('frequency'); // monthly, quarterly, annual
            $table->json('applicable_client_types')->nullable();
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->integer('default_due_day')->default(20);
            $table->integer('default_due_month')->nullable();
            $table->json('quarter_rules')->nullable();
            $table->timestamps();
        });

        Schema::create('compliance_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('financial_year_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('compliance_template_id')->constrained()->cascadeOnDelete();
            $table->string('period'); // e.g. "Q1 FY26", "AUG-2026", "AY 2026-27"
            $table->date('due_date');
            $table->string('status')->default('upcoming');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('filing_date')->nullable();
            $table->string('acknowledgement_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'client_id', 'compliance_template_id', 'financial_year_id', 'period'], 'uniq_compliance_period');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('compliance_instance_id')->nullable()->after('client_id')->constrained('compliance_instances')->nullOnDelete();
            $table->foreignId('financial_year_id')->nullable()->after('compliance_instance_id')->constrained()->nullOnDelete();
            $table->foreignId('reviewer_id')->nullable()->after('assigned_to')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_tables');
    }
};
