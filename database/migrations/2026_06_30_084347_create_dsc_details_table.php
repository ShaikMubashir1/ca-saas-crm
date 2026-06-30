<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('dsc_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('holder_name');
            $table->date('expiry_date');
            $table->text('password')->nullable(); // encrypted
            $table->boolean('is_with_firm')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('dsc_details');
    }
};
