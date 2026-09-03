# Phase 10 — Architecture & Operations Audit

## 1. Overview
Phase 10 extends the CA SaaS CRM into a practice management platform for multi-staff firms (3–15 staff).

---

## 2. Authentication & Authorization Foundation
- **Multi-Tenant Scoping**: `BelongsToTenant` trait & `TenantScope` global filter on all tenant models.
- **Spatie Laravel-Permission**: Installed (`spatie/laravel-permission: ^6.25`) and active on `User` model (`HasRoles` trait).
- **Roles**:
  - `Admin`: Complete tenant practice access.
  - `Partner`: Clients, Services, Compliance, Billing, Reports, WhatsApp.
  - `Manager`: Clients, Tasks, Compliance, Documents.
  - `Staff`: Assigned Clients, Assigned Tasks, Work items.
  - `Billing`: Invoices, Payments, Billing Reports.

---

## 3. Database Schema Extensions (Phase 10)
- `firm_settings` table (Legal info, GSTIN, PAN, TAN, logo, invoice footer, bank details, UPI).
- `client_onboarding_checklists` table (Client Lead → Active lifecycle tracker).
- `notifications` table (In-app notifications for task deadlines, invoice receipts, document requests).
- `clients` table columns: `primary_assignee_id`, `relationship_manager_id`, `onboarding_status`, `onboarding_progress`.
- `users` table columns: `phone`, `designation`, `status` (`active`/`inactive`).
