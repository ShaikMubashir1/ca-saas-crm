<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Checklist templates / instances
        Schema::create('document_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->cascadeOnDelete();
            $table->string('service_type'); // itr, gst, tds, audit, roc, etc.
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_template')->default(false);
            $table->timestamps();
        });

        // Individual items in a checklist
        Schema::create('document_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_checklist_id')->constrained('document_checklists')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(true);
            $table->string('document_type')->nullable(); // PAN, Aadhaar, Form 16, etc.
            $table->integer('sort_order')->default(0);
            $table->string('status')->default('pending'); // pending, received, under_review, verified, rejected
            $table->foreignId('current_document_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_checklist_items');
        Schema::dropIfExists('document_checklists');
    }
};
