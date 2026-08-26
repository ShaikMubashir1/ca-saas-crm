<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Add completed_at timestamp
            $table->timestamp('completed_at')->nullable()->after('description');

            // Add created_by to track who created the task
            $table->foreignId('created_by')->nullable()->after('completed_at')->constrained('users')->nullOnDelete();

            // Performance indexes
            $table->index('tenant_id');
            $table->index('client_id');
            $table->index('assigned_to');
            $table->index('due_date');
            $table->index('status');
        });

        // Normalize existing status values to new standard
        DB::table('tasks')->where('status', 'To Do')->update(['status' => 'pending']);
        DB::table('tasks')->where('status', 'In Progress')->update(['status' => 'in_progress']);
        DB::table('tasks')->where('status', 'Review')->update(['status' => 'in_progress']);
        DB::table('tasks')->where('status', 'Filed')->update(['status' => 'completed']);

        // Change default status value
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['tenant_id']);
            $table->dropIndex(['client_id']);
            $table->dropIndex(['assigned_to']);
            $table->dropIndex(['due_date']);
            $table->dropIndex(['status']);
            $table->dropForeign(['created_by']);
            $table->dropColumn(['completed_at', 'created_by']);
            $table->string('status')->default('To Do')->change();
        });
    }
};
