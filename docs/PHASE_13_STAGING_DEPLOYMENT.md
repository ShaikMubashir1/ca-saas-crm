# Phase 13 — Staging Deployment Preparation & Server Configuration Package

## 1. Environment & Software Version Constraints

Based on `composer.json` (`php: ^8.2`, `laravel/framework: ^12.0`), the system requirements for staging and production Linux deployment are:

- **PHP Version**: `PHP 8.2` or `PHP 8.3` (Recommended: `PHP 8.2-FPM` or `PHP 8.3-FPM`)
- **Laravel Framework**: `Laravel 12.x`
- **Node.js**: `Node.js 18.x` or `20.x LTS` (Vite 7.x)
- **Database Engine**: `MySQL 8.0+` or `MariaDB 10.11+`
- **Queue / Cache**: `Redis 7.0+` or `Database` driver

---

## 2. Server Package Installation Script

Run on clean Ubuntu 22.04 LTS server:

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y software-properties-common curl git unzip zip nginx supervisor mysql-server redis-server

# Add PHP 8.3 PPA
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring php8.3-xml php8.3-bcmath php8.3-curl php8.3-zip php8.3-intl php8.3-gd php8.3-redis

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

---

## 3. Nginx Server Configuration Template

`/etc/nginx/sites-available/ca-saas-crm-staging.conf`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name staging-crm.yourfirmdomain.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name staging-crm.yourfirmdomain.com;
    root /var/www/ca-saas-crm/public;

    ssl_certificate /etc/letsencrypt/live/staging-crm.yourfirmdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/staging-crm.yourfirmdomain.com/privkey.pem;
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
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
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

`/etc/supervisor/conf.d/ca-crm-worker.conf`:

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

## 5. Storage Permissions & Security Setup

```bash
sudo chown -R www-data:www-data /var/www/ca-saas-crm
sudo find /var/www/ca-saas-crm -type f -exec chmod 644 {} \;
sudo find /var/www/ca-saas-crm -type d -exec chmod 755 {} \;
sudo chmod -R 775 /var/www/ca-saas-crm/storage /var/www/ca-saas-crm/bootstrap/cache
```

---

## 6. Staging Deployment Execution Sequence

1. `git clone <repo_url> /var/www/ca-saas-crm`
2. `cd /var/www/ca-saas-crm`
3. `cp .env.example .env` (configure DB & domain settings)
4. `composer install --no-dev --optimize-autoloader`
5. `npm ci && npm run build`
6. `php artisan key:generate`
7. `php artisan migrate --force`
8. `php artisan storage:link`
9. `php artisan config:cache && php artisan route:cache && php artisan view:cache`
10. `sudo systemctl restart php8.3-fpm nginx`
11. `sudo supervisorctl update && sudo supervisorctl restart all`

---

## 7. Pass / Fail Verification Matrix

| Verification Dimension | Status | Notes |
|---|---|---|
| **Local Test Suite** | **PASS** | 66/66 tests passing (213 assertions) |
| **Local Database Migrations** | **PASS** | 34/34 migrations ran cleanly |
| **Local Asset Compilation** | **PASS** | `npm run build` compiled assets |
| **Remote Staging Provisioning** | **PENDING REMOTE SSH ACCESS** | Awaiting remote Linux server SSH credentials to execute deployment package |
