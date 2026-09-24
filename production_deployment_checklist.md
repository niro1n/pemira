# PRODUCTION DEPLOYMENT CHECKLIST
**Project:** PEMIRA WEB (Sistem Pemilihan Raya Mahasiswa)  
**Target Environment:** Production Server (VPS / Dedicated Server / Cloud PaaS)  
**PHP Version:** 8.3+ | **Database:** MySQL 8.0+ / MariaDB 10.6+ / PostgreSQL 15+

---

## 1. Server Requirements

### PHP Configuration & Extensions
- **PHP Version:** Minimum 8.3.x (disarankan 8.3.16+)
- **Required Extensions:**
  - `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `hash`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_mysql` (atau `pdo_pgsql`), `session`, `tokenizer`, `xml`
- **Recommended PHP INI Directives:**
  ```ini
  memory_limit = 256M
  upload_max_filesize = 25M
  post_max_size = 30M
  max_execution_time = 60
  opcache.enable = 1
  opcache.memory_consumption = 128
  opcache.interned_strings_buffer = 16
  opcache.max_accelerated_files = 10000
  opcache.revalidate_freq = 0
  ```

### Database Server
- **MySQL:** 8.0.28+ atau **MariaDB:** 10.6.10+
- **Charset & Collation:** `utf8mb4` / `utf8mb4_unicode_ci`

---

## 2. Environment Variables Checklist (`.env`)

Pastikan seluruh key berikut tersedia di file `.env` production (**JANGAN masukkan file `.env` ke Git**):

```dotenv
APP_NAME="PEMIRA PNB 2026"
APP_ENV=production
APP_KEY=base64:...             # Wajib di-generate via php artisan key:generate
APP_DEBUG=false               # WAJIB FALSE DI PRODUCTION
APP_URL=https://pemira.pnb.ac.id
APP_TIMEZONE=Asia/Makassar
APP_LOCALE=id
APP_FALLBACK_LOCALE=id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pemira_production
DB_USERNAME=pemira_user
DB_PASSWORD="[SECURE_DB_PASSWORD]"
DB_TIMEZONE="+08:00"

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true

CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME="[SMTP_USER]"
MAIL_PASSWORD="[SMTP_PASSWORD]"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@pemira.pnb.ac.id"
MAIL_FROM_NAME="Panitia PEMIRA PNB"

HUMAS_WHATSAPP=6281234567890
```

---

## 3. Deployment Commands Sequence

```bash
# 1. Aktifkan Maintenance Mode Sementara
php artisan down --retry=60

# 2. Tarik Kode Terbaru
git pull origin main

# 3. Install Dependensi PHP (Production Only)
composer install --no-dev --optimize-autoloader --no-interaction

# 4. Compile Frontend Assets (Jika tidak via CI/CD)
npm ci && npm run build

# 5. Jalankan Database Migrations
php artisan migrate --force

# 6. Buat Storage Symlink
php artisan storage:link

# 7. Optimasi & Cache Laravel Framework
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 8. Restart Background Queue Workers
php artisan queue:restart

# 9. Nonaktifkan Maintenance Mode
php artisan up
```

---

## 4. Background Workers & Task Scheduling

### Supervisor Queue Worker (`/etc/supervisor/conf.d/pemira-worker.conf`):
```ini
[program:pemira-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/pemira/artisan queue:work database --sleep=3 --tries=3 --max-time=3600 --timeout=90
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/pemira/storage/logs/worker.log
```

### Crontab (`crontab -e -u www-data`):
```cron
* * * * * cd /var/www/pemira && php artisan schedule:run >> /dev/null 2>&1
```

---

## 5. Storage Permissions

```bash
sudo chown -R www-data:www-data /var/www/pemira
sudo chmod -R 775 /var/www/pemira/storage
sudo chmod -R 775 /var/www/pemira/bootstrap/cache
```

---

## 6. Rollback Procedure

```bash
php artisan down
git reset --hard HEAD~1
php artisan migrate:rollback --step=1 --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan up
```
