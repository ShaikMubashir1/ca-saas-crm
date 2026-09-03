# Phase 6 — WhatsApp Communication & Automation Documentation

## Overview
Phase 6 introduces a provider-agnostic, tenant-safe WhatsApp automation & two-way inbox engine for small CA practices.

---

## Configuration & Drivers

### Environment Variables
```env
WHATSAPP_PROVIDER=mock
WHATSAPP_ENABLED=true
WHATSAPP_ACCESS_TOKEN=your_meta_access_token_here
WHATSAPP_PHONE_NUMBER_ID=your_meta_phone_id_here
WHATSAPP_BUSINESS_ACCOUNT_ID=your_meta_waba_id_here
WHATSAPP_API_VERSION=v19.0
WHATSAPP_WEBHOOK_VERIFY_TOKEN=ca_crm_webhook_secret
```

---

## Architecture Components

1. **Provider Abstraction**:
   - Interface: `App\Services\Communication\WhatsApp\WhatsAppProviderInterface`
   - Mock Driver: `App\Services\Communication\WhatsApp\Providers\NullWhatsAppProvider`
   - Live Meta Cloud API Driver: `App\Services\Communication\Providers\WhatsAppProvider`

2. **Database Models**:
   - `WhatsAppConversation` (`whatsapp_conversations` table)
   - `WhatsAppMessage` (`whatsapp_messages` table)
   - `WhatsAppTemplate` (`whatsapp_templates` table)
   - `WhatsAppConsent` (`whatsapp_consents` table)

3. **Consent & STOP Opt-Out**:
   - Automatically processes inbound `STOP` text messages via `WhatsAppWebhookController` to mark transactional and marketing consent `false`.

4. **UI Routes**:
   - `/whatsapp`: Livewire two-way WhatsApp Inbox & Chat Box.
   - `/whatsapp/broadcasts`: Target audience filter & template broadcast dispatcher.
