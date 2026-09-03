# Phase 12 — Deployment Readiness & DevOps Audit Report

## 1. Local vs. Staging/Production Verification Status

- **Local Development Environment**: **PASS** (100% tests passing, 0 pending migrations, clean Vite build).
- **Remote Linux Production Server Provisioning**: **PENDING SERVER DEPLOYMENT** (Target Linux server credentials/environment not yet connected).

---

## 2. Linux Production Environment Prerequisites

### A. System Packages & PHP Modules
- **OS**: Ubuntu 22.04 LTS or Debian 12
- **Web Server**: Nginx (1.24+) or Apache (2.4+) with HTTP/2 and TLS 1.3
- **PHP**: PHP 8.2+ CLI, FPM, and extensions (`php8.2-fpm`, `php8.2-mysql`, `php8.2-mbstring`, `php8.2-xml`, `php8.2-bcmath`, `php8.2-curl`, `php8.2-zip`, `php8.2-intl`)
- **Database**: MySQL 8.0+ or MariaDB 10.11+
- **In-Memory Cache / Queue**: Redis 7.0+ (`php8.2-redis`)

---

## 3. Nginx Web Server Configuration Template

Create `/etc/nginx/sites-available/ca-saas-crm.conf`:
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name crm.yourfirmdomain.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name crm.yourfirmdomain.com;
    root /var/www/ca-saas-crm/public;

    ssl_certificate /etc/letsencrypt/live/crm.yourfirmdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/crm.yourfirmdomain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 4. Supervisor Queue Worker Configuration

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

---

## 5. Deployment Verification Commands Checklist

1. `git pull origin main`
2. `composer install --no-dev --optimize-autoloader`
3. `npm ci && npm run build`
4. `php artisan migrate --force`
5. `php artisan config:cache`
6. `php artisan route:cache`
7. `php artisan view:cache`
8. `sudo systemctl restart php8.2-fpm nginx`
9. `sudo supervisorctl restart all`
