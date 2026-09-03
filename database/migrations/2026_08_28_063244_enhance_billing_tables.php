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
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('tenant_id')->constrained('users')->nullOnDelete();
            $table->decimal('discount_amount', 10, 2)->default(0)->after('subtotal');
            $table->text('terms')->nullable()->after('notes');
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->after('invoice_id')->constrained()->nullOnDelete();
            $table->decimal('tax_amount', 10, 2)->default(0)->after('tax_rate');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->after('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('received_by')->nullable()->after('reference_number')->constrained('users')->nullOnDelete();
            $table->string('status')->default('completed')->after('method');
            $table->text('notes')->nullable()->after('status');
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
