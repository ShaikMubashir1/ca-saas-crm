# Phase 9 — Production Deployment Checklist

## 1. Server Prerequisites
- PHP 8.2 or higher with BCMath, OpenSSL, PDO, Mbstring, Tokenizer, XML, and Ctype extensions.
- MySQL 8.0+ or PostgreSQL 14+ database server.
- Nginx or Apache web server with HTTP/2 and TLS enabled.

---

## 2. Step-by-Step Deployment Guide

### Step 1: Environment Configuration
Copy `.env.example` to `.env` and set production environment variables:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-firm-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ca_saas_crm
DB_USERNAME=ca_db_user
DB_PASSWORD=your_secure_password

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

WHATSAPP_PROVIDER=mock # Set to 'meta' once Meta WhatsApp Cloud API credentials are ready
```

### Step 2: Dependencies & Asset Build
```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

### Step 3: Database Migrations & Storage Link
```bash
php artisan migrate --force
php artisan storage:link
```

### Step 4: Cache Optimization
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 5: Supervisor & Queue Worker Configuration
Create `/etc/supervisor/conf.d/ca-crm-worker.conf`:
```ini
[program:ca-crm-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/ca-saas-crm/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/ca-saas-crm/storage/logs/worker.log
```

### Step 6: Cron Scheduler Configuration
Add cron entry for daily compliance due-date & reminder sweeps:
```cron
* * * * * cd /var/www/ca-saas-crm && php artisan schedule:run >> /dev/null 2>&1
```
