<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Templates Table
        Schema::create('communication_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('category')->default('utility'); // utility, marketing, reminder, authentication
            $table->string('channel')->default('whatsapp'); // whatsapp, email, sms
            $table->string('template_key')->index();
            $table->string('provider_template_id')->nullable();
            $table->string('language')->default('en');
            $table->text('body');
            $table->json('variables')->nullable();
            $table->string('status')->default('approved');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Messages Table
        Schema::create('communication_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('channel')->default('whatsapp');
            $table->string('direction')->default('outbound'); // inbound, outbound
            $table->string('message_type')->default('text'); // text, template, document
            $table->foreignId('template_id')->nullable()->constrained('communication_templates')->nullOnDelete();
            $table->string('provider_message_id')->nullable()->index();
            $table->string('recipient');
            $table->string('subject')->nullable();
            $table->text('body');
            $table->string('status')->default('queued'); // queued, sent, delivered, read, failed
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // 3. Logs Table
        Schema::create('communication_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->cascadeOnDelete();
            $table->foreignId('communication_message_id')->nullable()->constrained('communication_messages')->cascadeOnDelete();
            $table->string('event');
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        // 4. Consents Table
        Schema::create('communication_consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('channel')->default('whatsapp');
            $table->string('purpose')->default('utility'); // utility, marketing, transactional
            $table->string('status')->default('opted_in'); // opted_in, opted_out, pending
            $table->string('source')->default('system');
            $table->timestamp('consented_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'client_id', 'channel', 'purpose'], 'client_consent_unique');
        });

        // 5. Add token & reminder attributes to document_requests
        Schema::table('document_requests', function (Blueprint $table) {
            $table->string('upload_token')->nullable()->unique();
            $table->timestamp('token_expires_at')->nullable();
            $table->integer('reminder_count')->default(0);
            $table->timestamp('last_reminder_sent_at')->nullable();
            $table->integer('max_reminders')->default(3);
        });
    }

    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropColumn(['upload_token', 'token_expires_at', 'reminder_count', 'last_reminder_sent_at', 'max_reminders']);
        });

        Schema::dropIfExists('communication_consents');
        Schema::dropIfExists('communication_logs');
        Schema::dropIfExists('communication_messages');
        Schema::dropIfExists('communication_templates');
    }
};
