# HOSTING REQUIREMENTS & COMPATIBILITY AUDIT
**Project:** PEMIRA WEB (Sistem Pemilihan Raya Mahasiswa)  
**Evaluated Stack:** Laravel 13.x | PHP 8.3 | Livewire 4.x | MySQL 8.x / MariaDB 10.6+

---

## 1. Summary of Requirements

### A. REQUIRED (Wajib Ada)
1. **PHP 8.3+ Runtime:** PHP 8.3 dengan ekstensi aktif: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `hash`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_mysql` (atau `pdo_pgsql`), `session`, `tokenizer`, `xml`.
2. **Database Relasional:** MySQL 8.0+ atau MariaDB 10.6+ dengan dukungan `utf8mb4_unicode_ci` dan foreign key constraints.
3. **Document Root Customization:** Web server WAJIB mengarahkan document root ke folder `/public` (BUKAN ke root folder aplikasi).
4. **HTTPS / SSL Certificate:** Enkripsi TLS/SSL wajib aktif untuk session cookie secure dan integritas transmisi suara bilik digital.
5. **Dukungan Filesystem Symlink:** Server harus mendukung pembuatan symlink Linux (`ln -s`) untuk menjalankan `php artisan storage:link`.
6. **Izin Tulis Direktori (Write Permissions):** Hak tulis untuk user web server pada direktori `storage/` dan `bootstrap/cache/`.
7. **Environment Variable / File `.env` Support:** Dukungan pembacaan file konfigurasi `.env`.

### B. RECOMMENDED (Sangat Disarankan)
1. **Cloud VPS / Managed Container:** Minimum 2 vCPU, 4 GB RAM, SSD/NVMe Storage.
2. **Process Monitor (Supervisor / Systemd):** Untuk menjalankan Laravel Queue Worker di latar belakang (`php artisan queue:work`) untuk pengiriman OTP email dan undangan admin.
3. **Cron Job / Scheduled Tasks:** Akses crontab per menit (`* * * * *`) untuk menjalankan `php artisan schedule:run` (reminder otomatis voting).
4. **PHP OPcache Aktif:** Mengurangi beban CPU dan mempercepat eksekusi PHP hingga 3x lipat pada beban konkurensi tinggi.

### C. OPTIONAL
1. **Redis Cache & Session Store:** Untuk beban di atas 2.000 mahasiswa bersamaan per menit.
2. **Dedicated Outbound SMTP Provider:** Layanan SMTP terpercaya (Mailgun, SendGrid, Amazon SES, atau server SMTP kampus).

### D. BLOCKER (Menggagalkan Deployment)
1. **PHP Versi < 8.3:** Codebase menggunakan fitur PHP 8.3. Versi lebih rendah akan mengalami fatal parse error.
2. **Document Root Terkunci di Root Folder:** Mengekspos `.env` dan kode aplikasi ke publik via browser jika tidak diarahkan ke `/public`.
3. **Fungsi `symlink()` Dinonaktifkan:** Foto kandidat dan logo sponsor tidak dapat diakses publik dari `public/storage`.
4. **Tidak Ada Akses Cron / Background Task:** Pengingat jadwal voting otomatis tidak dapat berjalan.

---

## 2. Shared Hosting vs Cloud VPS Comparison

| Fitur / Parameter | Shared Hosting Standar | Cloud VPS (Disarankan) | Analisis Dampak pada PEMIRA |
|---|---|---|---|
| **PHP 8.3+** | Terbatas | Bebas dikonfigurasi penuh | Wajib 8.3+ |
| **Document Root /public** | Seringkali dipaksa ke `public_html` | Fleksibel di Nginx/Apache vhost | Jika shared hosting, perlu symlink `public_html -> public` |
| **Queue Worker (Supervisor)** | Tidak didukung | Didukung penuh via Supervisor | Shared hosting harus fallback ke `QUEUE_CONNECTION=sync` |
| **Cron Job per Menit** | Sering dibatasi (min 15 menit) | Bebas per menit (`* * * * *`) | Diperlukan cron per menit untuk reminder voting |
| **SSH / Terminal Access** | Kadang tidak tersedia | Tersedia penuh (Root / Sudo) | Mempermudah migrasi, caching, dan troubleshooting |
