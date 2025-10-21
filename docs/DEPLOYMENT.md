# Deployment Guide

This guide covers deploying the CRM application to production environments.

## Table of Contents

- [Prerequisites](#prerequisites)
- [Server Requirements](#server-requirements)
- [Deployment Steps](#deployment-steps)
- [Environment Configuration](#environment-configuration)
- [Database Setup](#database-setup)
- [Web Server Configuration](#web-server-configuration)
- [SSL/TLS Configuration](#ssltls-configuration)
- [Queue Workers](#queue-workers)
- [Task Scheduling](#task-scheduling)
- [Monitoring and Logging](#monitoring-and-logging)
- [Backup Strategy](#backup-strategy)
- [Security Checklist](#security-checklist)
- [Troubleshooting](#troubleshooting)

## Prerequisites

Before deploying, ensure you have:

- Server with SSH access
- Domain name configured
- SSL certificate (recommended: Let's Encrypt)
- Database server credentials
- SMTP credentials for email
- Git access to the repository

## Server Requirements

### Minimum Requirements

- **OS**: Ubuntu 22.04 LTS or newer
- **PHP**: 8.4 or higher
- **Web Server**: Nginx 1.18+ or Apache 2.4+
- **Database**: MySQL 8.0+ or PostgreSQL 14+
- **Memory**: 2GB RAM (4GB+ recommended)
- **Storage**: 20GB SSD
- **CPU**: 2 cores (4+ recommended)

### PHP Extensions

Required PHP extensions:
```bash
php8.4-cli
php8.4-fpm
php8.4-mysql  # or php8.4-pgsql
php8.4-mbstring
php8.4-xml
php8.4-bcmath
php8.4-curl
php8.4-zip
php8.4-gd
php8.4-intl
php8.4-redis
```

### Additional Software

```bash
composer
nodejs (18+)
npm
redis-server
supervisor
certbot
```

## Deployment Steps

### 1. Server Setup

#### Update System

```bash
sudo apt update
sudo apt upgrade -y
```

#### Install PHP 8.4

```bash
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.4 php8.4-fpm php8.4-cli php8.4-mysql php8.4-mbstring \
  php8.4-xml php8.4-bcmath php8.4-curl php8.4-zip php8.4-gd php8.4-intl \
  php8.4-redis -y
```

#### Install Nginx

```bash
sudo apt install nginx -y
sudo systemctl enable nginx
sudo systemctl start nginx
```

#### Install MySQL

```bash
sudo apt install mysql-server -y
sudo mysql_secure_installation
```

#### Install Redis

```bash
sudo apt install redis-server -y
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

#### Install Composer

```bash
cd ~
curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
sudo php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
```

#### Install Node.js and npm

```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

#### Install Supervisor

```bash
sudo apt install supervisor -y
sudo systemctl enable supervisor
sudo systemctl start supervisor
```

### 2. Application Deployment

#### Create Application Directory

```bash
sudo mkdir -p /var/www/crm
sudo chown $USER:$USER /var/www/crm
cd /var/www/crm
```

#### Clone Repository

```bash
git clone git@github.com:your-org/crm.git .
```

Or using HTTPS:
```bash
git clone https://github.com/your-org/crm.git .
```

#### Install Dependencies

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

#### Set Permissions

```bash
sudo chown -R www-data:www-data /var/www/crm
sudo chmod -R 755 /var/www/crm
sudo chmod -R 775 /var/www/crm/storage
sudo chmod -R 775 /var/www/crm/bootstrap/cache
```

### 3. Environment Configuration

#### Create .env File

```bash
cp .env.example .env
nano .env
```

#### Configure Environment Variables

```env
APP_NAME="CRM Application"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_TIMEZONE=Europe/Budapest
APP_URL=https://crm.yourcompany.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crm_production
DB_USERNAME=crm_user
DB_PASSWORD=secure_password_here

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis

CACHE_STORE=redis
CACHE_PREFIX=crm_cache

SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_ENCRYPT=true

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.yourprovider.com
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourcompany.com
MAIL_FROM_NAME="${APP_NAME}"
```

#### Generate Application Key

```bash
php artisan key:generate
```

### 4. Database Setup

#### Create Database

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE crm_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'crm_user'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON crm_production.* TO 'crm_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### Run Migrations

```bash
php artisan migrate --force
```

#### Seed Permissions

```bash
php artisan db:seed --class=PermissionSeeder --force
```

#### Create Admin User

```bash
php artisan make:filament-user
```

### 5. Web Server Configuration

#### Nginx Configuration

Create `/etc/nginx/sites-available/crm`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name crm.yourcompany.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name crm.yourcompany.com;
    root /var/www/crm/public;

    index index.php;

    charset utf-8;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/crm.yourcompany.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/crm.yourcompany.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # Logging
    access_log /var/log/nginx/crm-access.log;
    error_log /var/log/nginx/crm-error.log;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Static file caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Prevent access to storage
    location ^~ /storage {
        internal;
    }

    # Client body size limit
    client_max_body_size 20M;
}
```

#### Enable Site

```bash
sudo ln -s /etc/nginx/sites-available/crm /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 6. SSL/TLS Configuration

#### Install Certbot

```bash
sudo apt install certbot python3-certbot-nginx -y
```

#### Obtain Certificate

```bash
sudo certbot --nginx -d crm.yourcompany.com
```

#### Auto-renewal

Certbot automatically sets up auto-renewal. Test it:

```bash
sudo certbot renew --dry-run
```

### 7. Queue Workers

#### Configure Supervisor

Create `/etc/supervisor/conf.d/crm-worker.conf`:

```ini
[program:crm-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/crm/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/crm/storage/logs/worker.log
stopwaitsecs=3600
```

#### Start Workers

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start crm-worker:*
```

#### Check Worker Status

```bash
sudo supervisorctl status
```

### 8. Task Scheduling

#### Add Cron Job

```bash
sudo crontab -e -u www-data
```

Add this line:
```
* * * * * cd /var/www/crm && php artisan schedule:run >> /dev/null 2>&1
```

Verify cron is running:
```bash
sudo systemctl status cron
```

### 9. Optimize Application

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan filament:cache-components
```

## Monitoring and Logging

### Application Logs

Logs are stored in `/var/www/crm/storage/logs/`

View recent errors:
```bash
tail -f /var/www/crm/storage/logs/laravel.log
```

### Web Server Logs

- Access: `/var/log/nginx/crm-access.log`
- Errors: `/var/log/nginx/crm-error.log`

### Database Logs

MySQL logs: `/var/log/mysql/error.log`

### Redis Logs

Redis logs: `/var/log/redis/redis-server.log`

### Log Rotation

Create `/etc/logrotate.d/crm`:

```
/var/www/crm/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

## Backup Strategy

### Database Backup

Create `/usr/local/bin/backup-crm-db.sh`:

```bash
#!/bin/bash

BACKUP_DIR="/var/backups/crm/database"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="crm_production"
DB_USER="crm_user"
DB_PASS="secure_password_here"

mkdir -p $BACKUP_DIR

mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/crm_db_$DATE.sql.gz

# Keep only last 30 days
find $BACKUP_DIR -name "crm_db_*.sql.gz" -mtime +30 -delete
```

Make executable:
```bash
sudo chmod +x /usr/local/bin/backup-crm-db.sh
```

Add to crontab:
```bash
0 2 * * * /usr/local/bin/backup-crm-db.sh
```

### File Backup

Create `/usr/local/bin/backup-crm-files.sh`:

```bash
#!/bin/bash

BACKUP_DIR="/var/backups/crm/files"
DATE=$(date +%Y%m%d_%H%M%S)
SOURCE_DIR="/var/www/crm/storage/app"

mkdir -p $BACKUP_DIR

tar -czf $BACKUP_DIR/crm_files_$DATE.tar.gz -C $SOURCE_DIR .

# Keep only last 30 days
find $BACKUP_DIR -name "crm_files_*.tar.gz" -mtime +30 -delete
```

### Off-site Backup

Consider using:
- AWS S3
- Azure Blob Storage
- Backblaze B2
- rsync to remote server

Example with AWS S3:
```bash
aws s3 sync /var/backups/crm/ s3://your-bucket/crm-backups/
```

## Security Checklist

### Server Security

- [ ] Firewall configured (UFW)
- [ ] SSH key-based authentication only
- [ ] SSH root login disabled
- [ ] Fail2ban installed and configured
- [ ] Automatic security updates enabled
- [ ] Server timezone set correctly

### Application Security

- [ ] APP_DEBUG=false in production
- [ ] APP_ENV=production
- [ ] Strong APP_KEY generated
- [ ] Database credentials secured
- [ ] HTTPS enforced
- [ ] Security headers configured
- [ ] File permissions correct (755/644)
- [ ] Storage directory not web accessible
- [ ] .env file not in version control
- [ ] Composer dependencies updated
- [ ] npm dependencies updated

### Database Security

- [ ] Database user has minimal privileges
- [ ] Strong database password
- [ ] Database not accessible from internet
- [ ] Regular backups configured
- [ ] Binary logging enabled (for point-in-time recovery)

### Firewall Configuration

```bash
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS
sudo ufw enable
```

## Deployment Automation

### Using Deployer

Install Deployer:
```bash
composer require deployer/deployer --dev
```

Create `deploy.php`:

```php
<?php

namespace Deployer;

require 'recipe/laravel.php';

set('application', 'CRM Application');
set('repository', 'git@github.com:your-org/crm.git');
set('keep_releases', 5);

host('production')
    ->set('remote_user', 'deployer')
    ->set('deploy_path', '/var/www/crm')
    ->set('hostname', 'crm.yourcompany.com');

task('deploy:secrets', function () {
    upload('.env.production', '{{deploy_path}}/shared/.env');
});

after('deploy:failed', 'deploy:unlock');
after('deploy:symlink', 'artisan:migrate');
after('deploy:symlink', 'artisan:config:cache');
after('deploy:symlink', 'artisan:route:cache');
after('deploy:symlink', 'artisan:view:cache');
```

Deploy:
```bash
vendor/bin/dep deploy production
```

## Updating the Application

### Standard Update Process

```bash
cd /var/www/crm

# Pull latest code
git pull origin main

# Update dependencies
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# Run migrations
php artisan migrate --force

# Clear and rebuild caches
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:cache-components

# Restart queue workers
sudo supervisorctl restart crm-worker:*

# Restart PHP-FPM
sudo systemctl restart php8.4-fpm
```

### Zero-Downtime Deployment

Use tools like:
- Laravel Envoyer
- Deployer
- GitHub Actions with deployment scripts

## Troubleshooting

### 500 Internal Server Error

Check logs:
```bash
tail -f /var/www/crm/storage/logs/laravel.log
tail -f /var/log/nginx/crm-error.log
```

Common causes:
- Incorrect file permissions
- Missing .env file
- Database connection issues
- PHP errors

### Queue Workers Not Processing Jobs

```bash
# Check supervisor status
sudo supervisorctl status

# Restart workers
sudo supervisorctl restart crm-worker:*

# Check worker logs
tail -f /var/www/crm/storage/logs/worker.log
```

### Database Connection Issues

Test connection:
```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

Check credentials in `.env`

### High Memory Usage

Increase PHP memory limit in `/etc/php/8.4/fpm/php.ini`:
```ini
memory_limit = 512M
```

Restart PHP-FPM:
```bash
sudo systemctl restart php8.4-fpm
```

### Slow Performance

Enable OPcache in `/etc/php/8.4/fpm/php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
```

## Performance Optimization

### Redis Configuration

Edit `/etc/redis/redis.conf`:
```
maxmemory 256mb
maxmemory-policy allkeys-lru
```

### MySQL Optimization

Edit `/etc/mysql/mysql.conf.d/mysqld.cnf`:
```ini
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
query_cache_type = 1
query_cache_size = 64M
```

### PHP-FPM Tuning

Edit `/etc/php/8.4/fpm/pool.d/www.conf`:
```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
```

## Health Checks

Create a health check endpoint and monitor:

```bash
curl https://crm.yourcompany.com/api/health
```

Use monitoring services:
- UptimeRobot
- Pingdom
- StatusCake
- Laravel Forge (built-in monitoring)

## Support

For deployment assistance:
- Email: devops@yourcompany.com
- Internal wiki: https://wiki.yourcompany.com/crm-deployment
- On-call: +36 30 123 4567
