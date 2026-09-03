<?php

namespace App\Providers;

use App\Models\Document;
use App\Models\DocumentChecklist;
use App\Policies\DocumentPolicy;
use App\Policies\ChecklistPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected array $policies = [
        Document::class => DocumentPolicy::class,
        DocumentChecklist::class => ChecklistPolicy::class,
        \App\Models\CommunicationTemplate::class => \App\Policies\CommunicationTemplatePolicy::class,
        \App\Models\CommunicationMessage::class => \App\Policies\CommunicationMessagePolicy::class,
        \App\Models\Communication::class => \App\Policies\CommunicationPolicy::class,
        \App\Models\Invoice::class => \App\Policies\InvoicePolicy::class,
        \App\Models\Payment::class => \App\Policies\PaymentPolicy::class,
        \App\Models\ComplianceInstance::class => \App\Policies\ComplianceInstancePolicy::class,
        \App\Models\WhatsAppConversation::class => \App\Policies\WhatsAppConversationPolicy::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}

