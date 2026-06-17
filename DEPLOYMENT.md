# BongoGames Backend Deployment Guide

This guide deploys the Laravel backend on a VPS using **Ubuntu 24.04 LTS**, **PHP 8.3-FPM**, **Nginx**, **MySQL/MariaDB**, and **Composer**.

The application will be served at `https://api.bongogames.online`.

---

## 1. Provision the VPS

Recommended specs:

- **OS**: Ubuntu 24.04 LTS
- **CPU**: 2 vCPU
- **RAM**: 4 GB
- **Disk**: 40 GB SSD
- **Domain**: `api.bongogames.online` pointed to the VPS public IP

Open firewall ports:

```bash
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw enable
```

---

## 2. Install Dependencies

```bash
sudo apt update && sudo apt upgrade -y

# Add PHP 8.3 repository
sudo apt install -y software-properties-common ca-certificates lsb-release apt-transport-https curl gnupg2
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.3 and required extensions
sudo apt install -y \
  php8.3-fpm php8.3-cli php8.3-common \
  php8.3-mysql php8.3-mbstring php8.3-xml php8.3-bcmath \
  php8.3-zip php8.3-curl php8.3-gd php8.3-intl \
  php8.3-sqlite3 php8.3-opcache

# Install Nginx, MySQL, Git, Composer, Certbot
sudo apt install -y nginx mysql-server git unzip certbot python3-certbot-nginx

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

---

## 3. Configure PHP 8.3-FPM

Edit `/etc/php/8.3/fpm/pool.d/www.conf`:

```ini
user = www-data
group = www-data
listen = /run/php/php8.3-fpm.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660
```

Edit `/etc/php/8.3/fpm/php.ini` for production:

```ini
memory_limit = 512M
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 60
date.timezone = Africa/Dar_es_Salaam
```

Restart PHP-FPM:

```bash
sudo systemctl restart php8.3-fpm
sudo systemctl enable php8.3-fpm
```

---

## 4. Create the Application User and Directory

```bash
# Create a dedicated deployer user
sudo useradd -m -s /bin/bash deployer
sudo usermod -aG www-data deployer

# Create application directory
sudo mkdir -p /var/www/api.bongogames.online
sudo chown -R deployer:www-data /var/www/api.bongogames.online
```

---

## 5. Deploy the Application Code

As the `deployer` user:

```bash
cd /var/www/api.bongogames.online
git clone git@github.com:yourusername/bongogames-backend.git .
# Or upload the project files here, including the backend/ contents
```

Install Composer dependencies (no dev packages for production):

```bash
composer install --no-dev --optimize-autoloader --no-interaction
```

Set permissions:

```bash
sudo chown -R deployer:www-data /var/www/api.bongogames.online
sudo chmod -R 775 storage bootstrap/cache
sudo find storage -type d -exec chmod 2775 {} \;
sudo find storage -type f -exec chmod 664 {} \;
```

---

## 6. Configure the Environment

Copy the example environment file and edit it:

```bash
cp .env.example .env
nano .env
```

Set at least these values:

```env
APP_NAME=BongoGames
APP_ENV=production
APP_KEY=base64:REPLACE_WITH_APP_KEY
APP_DEBUG=false
APP_URL=https://api.bongogames.online

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bongogames
DB_USERNAME=bongogames_user
DB_PASSWORD=STRONG_DB_PASSWORD

BROADCAST_CONNECTION=log
CACHE_STORE=database
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=database
SESSION_LIFETIME=120

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@bongogames.online"
MAIL_FROM_NAME="${APP_NAME}"

MOBILIPA_API_KEY=your_real_mobilipa_api_key_here
MOBILIPA_BASE_URL=https://api.mobilipa.store
```

Generate the application key:

```bash
php artisan key:generate --force
```

---

## 7. Create the MySQL Database

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE bongogames CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'bongogames_user'@'localhost' IDENTIFIED BY 'STRONG_DB_PASSWORD';
GRANT ALL PRIVILEGES ON bongogames.* TO 'bongogames_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 8. Run Migrations and Seeders

```bash
cd /var/www/api.bongogames.online
php artisan migrate --force --no-interaction
php artisan db:seed --force --no-interaction
php artisan storage:link
```

> **Warning**: `migrate --force` and `db:seed --force` will wipe existing data. Only run on fresh deployment or during planned maintenance.

---

## 9. Configure Nginx

Create `/etc/nginx/sites-available/api.bongogames.online`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name api.bongogames.online;
    root /var/www/api.bongogames.online/public;
    index index.php index.html;

    charset utf-8;

    # CORS preflight for API
    location /api/ {
        if ($request_method = 'OPTIONS') {
            add_header 'Access-Control-Allow-Origin' '*' always;
            add_header 'Access-Control-Allow-Methods' 'GET, POST, PUT, DELETE, OPTIONS' always;
            add_header 'Access-Control-Allow-Headers' 'Authorization, Content-Type, Accept, X-Requested-With' always;
            add_header 'Access-Control-Max-Age' 1728000 always;
            add_header 'Content-Type' 'text/plain; charset=utf-8' always;
            add_header 'Content-Length' 0 always;
            return 204;
        }

        try_files $uri $uri/ /index.php?$query_string;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;

        # Ensure response bodies are forwarded properly
        fastcgi_buffering off;
        fastcgi_request_buffering off;
        fastcgi_read_timeout 300;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 100M;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
}
```

Enable the site and remove the default:

```bash
sudo ln -s /etc/nginx/sites-available/api.bongogames.online /etc/nginx/sites-enabled/
sudo rm /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl restart nginx
sudo systemctl enable nginx
```

---

## 10. Obtain SSL Certificate (HTTPS)

```bash
sudo certbot --nginx -d api.bongogames.online --non-interactive --agree-tos --email admin@bongogames.online
sudo certbot renew --dry-run
```

Certbot will update the Nginx config automatically.

---

## 11. Configure Supervisor / Queue Workers

Create `/etc/supervisor/conf.d/bongogames-worker.conf`:

```ini
[program:bongogames-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/api.bongogames.online/artisan queue:work --sleep=3 --tries=3 --max-time=3600
type=queue
autostart=true
autorestart=true
user=deployer
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/bongogames-worker.log
stopwaitsecs=3600
```

Reload Supervisor:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start bongogames-worker:*
```

---

## 12. Configure the Scheduler

Open the crontab:

```bash
sudo crontab -e
```

Add this line:

```cron
* * * * * cd /var/www/api.bongogames.online && php artisan schedule:run >> /dev/null 2>&1
```

---

## 13. Optimization

```bash
cd /var/www/api.bongogames.online

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan optimize
```

> **Note**: If you need to update environment variables after caching, run `php artisan optimize:clear` first.

---

## 14. Health Check

Open your browser or run:

```bash
curl https://api.bongogames.online/api/auth/me
```

You should receive an unauthenticated response like:

```json
{ "message": "Unauthenticated." }
```

And the root page should show the BongoGames API status animation.

---

## 15. Useful Maintenance Commands

| Task | Command |
|------|---------|
| Restart PHP-FPM | `sudo systemctl restart php8.3-fpm` |
| Restart Nginx | `sudo systemctl restart nginx` |
| Check Nginx config | `sudo nginx -t` |
| View Laravel logs | `tail -f /var/www/api.bongogames.online/storage/logs/laravel.log` |
| Clear cache | `php artisan optimize:clear` |
| Rebuild cache | `php artisan optimize` |
| Run migrations | `php artisan migrate --force` |
| Restart queue workers | `sudo supervisorctl restart bongogames-worker:*` |

---

## 16. Troubleshooting

### Permission denied on storage/logs

```bash
sudo chown -R deployer:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 502 Bad Gateway

- Ensure PHP-FPM is running: `sudo systemctl status php8.3-fpm`
- Verify the socket path matches in Nginx and PHP-FPM pool config.

### Mixed content / CORS errors

Ensure `APP_URL=https://api.bongogames.online` and the frontend API URL points to `https://api.bongogames.online/api`.

### Uploaded files not showing

```bash
php artisan storage:link
```

Ensure `public/storage` is a symlink to `storage/app/public`.
