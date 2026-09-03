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
        Schema::create('firm_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('firm_name');
            $table->string('legal_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pin_code')->nullable();
            $table->string('ca_reg_number')->nullable();
            $table->string('gstin')->nullable();
            $table->string('pan')->nullable();
            $table->string('tan')->nullable();
            $table->string('logo_path')->nullable();
            $table->text('invoice_footer')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('upi_id')->nullable();
            $table->decimal('default_gst_percent', 5, 2)->default(18.00);
            $table->string('invoice_prefix')->default('INV');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('designation')->nullable()->after('phone');
            $table->string('status')->default('active')->after('designation'); // active, inactive
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->foreignId('primary_assignee_id')->nullable()->after('tenant_id')->constrained('users')->nullOnDelete();
            $table->foreignId('relationship_manager_id')->nullable()->after('primary_assignee_id')->constrained('users')->nullOnDelete();
            $table->string('onboarding_status')->default('lead')->after('address'); // lead, onboarding, docs_pending, review, active, inactive
            $table->integer('onboarding_progress')->default(0)->after('onboarding_status');
        });

        Schema::create('client_onboarding_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('item_name');
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tenant_id', 'client_id']);
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
