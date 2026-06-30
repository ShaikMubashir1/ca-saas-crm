<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('entity_type'); // Individual, HUF, Company, Firm, LLP, Trust
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('pan')->nullable(); // encrypted
            $table->text('aadhaar')->nullable(); // encrypted
            $table->string('gstin')->nullable();
            $table->string('tan')->nullable();
            $table->string('cin')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('clients');
    }
};
