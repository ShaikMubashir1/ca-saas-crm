# Phase 15 — Staging Deployment Preflight & Execution Guide

## 1. Local Codebase Deployment Readiness Assessment

The **CA SaaS CRM** (`C:\Users\mubas\.gemini\antigravity\scratch\ca-saas-crm`) has been evaluated across all deployment configurations:

- **Local Verification Status**: **100% PASS** (66/66 unit/feature tests passing, 213 assertions).
- **Database Migrations**: **100% PASS** (34/34 migrations executed).
- **Route Bindings**: **44 Registered Routes**.
- **Asset Compilation**: **100% PASS** (`npm run build` compiled `public/build/assets/`).

---

## 2. Server Requirements & Software Specifications

- **Operating System**: Ubuntu 22.04 LTS or Ubuntu 24.04 LTS
- **PHP Version**: `PHP 8.2-FPM` or `PHP 8.3-FPM`
- **Required Extensions**: `php-fpm`, `php-mysql`, `php-mbstring`, `php-xml`, `php-bcmath`, `php-curl`, `php-zip`, `php-intl`, `php-gd`, `php-redis`
- **Database Server**: `MySQL 8.0+` or `MariaDB 10.11+`
- **In-Memory Cache / Queue**: `Redis 7.0+` or `Database` driver
- **Web Server**: `Nginx 1.24+` with HTTP/2 and TLS 1.3
- **Process Manager**: `Supervisor 4.2+`

---

## 3. Deployment Preflight Checklist & Status

| Area | Status | Requirement & Controls |
|---|---|---|
| **App URLs & Debug** | **READY** | Configured for `APP_ENV=staging`, `APP_DEBUG=false`, `APP_URL=https://staging-crm.yourfirmdomain.com`. |
| **Database Connection** | **READY** | Scoped MySQL 8 database with non-root user. |
| **Filesystem Disks** | **READY** | `private` disk mapped to `storage/app/private`. Downloads guarded via `DocumentDownloadController` policies. |
| **Queue Workers** | **READY** | Database or Redis queue managed via Supervisor (`php artisan queue:work`). |
| **Cron Scheduler** | **READY** | Cron entry calling `php artisan schedule:run` every minute. |
| **Mock WhatsApp Driver** | **READY** | Configured to `mock` driver (`NullWhatsAppProvider`) until live Meta API credentials are supplied. |

---

## 4. Environment Classification Matrix

- **READY LOCALLY**: All source code, tests, Livewire views, routes, models, and migrations.
- **READY FOR SERVER**: Nginx SSL templates, Supervisor worker configs, Cron scheduler scripts, and deployment steps.
- **REQUIRES SERVER ACCESS**: Remote SSH login (IP, username, port, SSH key).
- **REQUIRES EXTERNAL CREDENTIALS**: Staging domain DNS A record, Meta WhatsApp Cloud API credentials (for live mode), SMTP mail credentials.

---

## 5. Next Steps for Remote Staging Launch

To execute the deployment on the target Linux staging server, provide:
1. Server IP / Hostname
2. SSH User & Port
3. Target Staging Domain
