<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->after('client_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('checklist_item_id')->nullable()->after('service_id'); // nullable, linked manually or via foreign key later
            $table->string('document_type')->nullable()->after('category');
            $table->string('original_filename')->nullable()->after('file_path');
            $table->string('mime_type')->nullable()->after('original_filename');
            $table->unsignedBigInteger('file_size')->nullable()->after('mime_type');
            $table->string('status')->default('received')->after('file_size'); // pending, received, under_review, verified, rejected
            $table->foreignId('verified_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable()->after('verified_by');
            $table->text('rejection_reason')->nullable()->after('verified_at');
            $table->foreignId('replaced_by_id')->nullable()->after('rejection_reason')->constrained('documents')->nullOnDelete();
            $table->boolean('is_current')->default(true)->after('replaced_by_id');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropForeign(['verified_by']);
            $table->dropForeign(['replaced_by_id']);
            $table->dropColumn([
                'service_id',
                'checklist_item_id',
                'document_type',
                'original_filename',
                'mime_type',
                'file_size',
                'status',
                'verified_by',
                'verified_at',
                'rejection_reason',
                'replaced_by_id',
                'is_current',
            ]);
        });
    }
};
