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
        Schema::create('whatsapp_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone_number');
            $table->string('wa_contact_id')->nullable();
            $table->string('status')->default('open'); // open, closed, archived
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'client_id']);
            $table->index(['tenant_id', 'phone_number']);
        });

        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('conversation_id')->constrained('whatsapp_conversations')->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->string('direction'); // inbound, outbound
            $table->string('message_type')->default('text'); // text, template, image, document
            $table->string('provider_message_id')->nullable();
            $table->string('template_name')->nullable();
            $table->text('body')->nullable();
            $table->string('media_url')->nullable();
            $table->string('status')->default('queued'); // queued, sent, delivered, read, failed
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'conversation_id']);
            $table->index(['tenant_id', 'status']);
            $table->index('provider_message_id');
        });

        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('category')->default('utility'); // utility, marketing, authentication
            $table->string('language')->default('en_US');
            $table->text('body');
            $table->json('variables')->nullable();
            $table->string('provider_template_id')->nullable();
            $table->string('status')->default('approved'); // draft, pending, approved, rejected, disabled
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });

        Schema::create('whatsapp_consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('phone_number');
            $table->boolean('marketing_opt_in')->default(true);
            $table->boolean('transactional_opt_in')->default(true);
            $table->timestamp('opted_in_at')->nullable();
            $table->timestamp('opted_out_at')->nullable();
            $table->string('source')->default('system');
            $table->timestamps();

            $table->unique(['tenant_id', 'client_id']);
            $table->index(['tenant_id', 'phone_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_tables');
    }
};
