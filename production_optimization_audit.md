# PRODUCTION OPTIMIZATION AUDIT
**Project:** PEMIRA WEB (Sistem Pemilihan Raya Mahasiswa)  
**Date:** September 2026  
**Framework:** Laravel 13.31.0 | PHP 8.3 | Livewire 4.1 | Tailwind CSS 4.0  
**Audit Status:** Complete & Verified Baseline (375/375 Tests Passed)

---

## 1. Executive Summary

Audit menyeluruh (Full Production Optimization, Performance Audit, Codebase Cleanup, Security Hardening, Database Optimization, dan Reliability Audit) telah dilakukan terhadap seluruh codebase **PEMIRA WEB**. 

Aplikasi ini memiliki arsitektur yang solid, dirancang khusus untuk integritas pemilu digital mahasiswa dengan prinsip **Bilik Suara Terpisah (Anonymous Ballot Separation)** antara identitas pemilih (`voting_participations`) dan surat suara fisik/digital (`ballots`). Seluruh 375 automated unit dan feature tests (1.679 assertions) berstatus **PASSING (100%)**, zero package vulnerabilities pada `composer audit` dan `npm audit`, serta aset frontend terkompilasi bersih menggunakan Vite (`app.css` 87.09 kB, gzip 14.28 kB).

Audit mendalam mengidentifikasi beberapa bottleneck performa krusial dan peluang hardening yang signifikan untuk kesiapan produksi beban tinggi (*high-concurrency voting day*):
1. **Query N+1 pada Dashboard & Quick Count Hasil**: Perhitungan partisipasi program studi melakukan 2 query terpisah di dalam loop setiap program studi (`StudyProgram->map(...)`), menghasilkan 20–40 query berulang setiap kali dashboard admin atau halaman hasil dibuka.
2. **Database Query Berulang pada Middleware Maintenance Mode**: `SystemSetting::isMaintenanceMode()` mengeksekusi `SELECT * FROM system_settings WHERE key = 'maintenance_mode' LIMIT 1` pada **setiap request HTTP web**, membebani database engine di bawah ribuan request bersamaan tanpa caching.
3. **Redundant Sequential Queries pada Penentuan Pemilihan Aktif (`Election::current()`)**: Mengeksekusi hingga 4 query sequential (`where voting`, `where upcoming`, `where registration`, `where finished`) dan dipanggil berkali-kali dalam satu request lifecycle tanpa in-request memoization.
4. **CSS Render-blocking Font Import**: `@import url(...)` Google Fonts berada di dalam `resources/css/app.css`, menimbulkan network waterfall cascade yang dapat memperlambat First Contentful Paint (FCP) bagi pemilih via koneksi mobile/seluler.
5. **Dead Code & Komponen Redundan**: Ditemukan komponen duplikat `resources/views/components/public/navbar.blade.php` dan `footer.blade.php` yang tidak pernah direferensikan karena halaman publik menggunakan `partials.home.*`.

---

## 2. Architecture Overview

- **Backend Framework:** Laravel 13.31.0
- **PHP Version:** 8.3 (Strict types, Attribute annotations, Modern Enums)
- **Database Engine:** Relational (MySQL / MariaDB / SQLite) dengan UTF-8 MB4 charset dan foreign key constraint enforcement
- **Frontend Stack:** Livewire 4.1, Tailwind CSS v4, Alpine.js, Blade templating
- **Asset Pipeline:** Vite v8 (`@tailwindcss/vite`, `vite-plus`)
- **Authentication & Security:** Multi-auth state (Session-based, Argon2id/Bcrypt rounds 12, SHA-256 hashed OTP & Admin Invitation tokens, brute-force rate limiters, anti-session fixation `session()->regenerate()`)
- **Role-Based Access Control (RBAC):**
  - `super_admin`: Kontrol sistem penuh, approval perubahan jadwal, special actions, manajemen admin via undangan
  - `admin`: Manajemen teknis pemira, DPT mahasiswa, paslon, sponsor, pengajuan perubahan jadwal, verifikasi masukan
  - `voter`: Akses bilik suara digital satu kali per pemira, dashboard pemilih, umpan balik (feedback)
- **Critical Flow — Anonymous Voting Architecture:**
  - `voting_participations`: Mencatat `(election_id, voter_account_id, voted_at)` dengan **UNIQUE composite key constraint** `['election_id', 'voter_account_id']`. Menjamin pemilih hanya dapat mencoblos 1 kali.
  - `ballots`: Mencatat `(id [UUID], election_id, candidate_pair_id)`. **TIDAK memiliki voter ID dan TIDAK memiliki timestamps**, memutus korelasi forensik antara waktu kedatangan suara dengan identitas pemilih demi kerahasiaan hak suara (Luber Jurdil).
  - Eksekusi atomic di dalam `DB::transaction()` dengan proteksi `UniqueConstraintViolationException`.

---

## 3. Performance Findings

### [PERF-01] N+1 Query pada Perhitungan Partisipasi Jurusan di Dashboard Admin
- **Severity:** HIGH
- **Location:** `app/Services/Dashboard/DashboardDataProvider.php:229-254`
- **Impact:** Jika terdapat 15 program studi, kode menjalankan 30 query individual ke database saat merender dashboard admin.
- **Recommended Solution:** Jalankan 2 agregasi grup SQL (`GROUP BY study_program_id`) dengan `pluck('aggregate', 'study_program_id')` di luar loop, kemudian petakan ke memori array dalam O(1).
- **Risk of Change:** LOW.

### [PERF-02] N+1 Query pada Perhitungan Partisipasi Jurusan di Quick Count / Hasil Pemilihan
- **Severity:** HIGH
- **Location:** `app/Services/Election/ElectionResultService.php:174-198`
- **Impact:** Halaman hasil pemira (quick count) diakses berkali-kali oleh publik, kandidat, dan admin. Menjalankan 20–40 query berulang per request membebani connection pool database secara tidak perlu.
- **Recommended Solution:** Gunakan grouped aggregation yang sama seperti `PERF-01`.
- **Risk of Change:** LOW.

### [PERF-03] Eksekusi Berulang `Election::current()` Tanpa In-Request Memoization
- **Severity:** MEDIUM
- **Location:** `app/Models/Election.php:62-99`
- **Impact:** Terjadi 4–12 query SQL identik yang redundan dalam satu HTTP request yang sama.
- **Recommended Solution:** Tambahkan in-request attribute memoization (`request()->attributes`) yang hanya berlaku selama HTTP request aktif.
- **Risk of Change:** LOW.

### [PERF-04] Query Database `isMaintenanceMode()` pada Setiap Request HTTP Tanpa Caching
- **Severity:** HIGH
- **Location:** `app/Http/Middleware/CheckMaintenanceMode.php:15` & `app/Models/SystemSetting.php:29-32`
- **Impact:** Pada 500 request/detik, 500 query SQL identik membebani database server.
- **Recommended Solution:** Implementasikan caching via `Cache::rememberForever("system_setting:{$key}", ...)` pada `SystemSetting::get()`, dan secara atomik bersihkan cache `Cache::forget("system_setting:{$key}")` di dalam `SystemSetting::set()`.
- **Risk of Change:** LOW.

### [PERF-05] Duplikasi Query Log pada Komponen `SpecialActions`
- **Severity:** LOW
- **Location:** `app/Livewire/Admin/SpecialActions/Index.php:33-51`
- **Impact:** 2 query dieksekusi padahal `$lastMaintenanceLog` adalah elemen pertama (`->first()`) dari koleksi `$recentMaintenanceLogs`.
- **Recommended Solution:** Gunakan `$this->recentMaintenanceLogs->first()` untuk properti `lastMaintenanceLog`.
- **Risk of Change:** NONE.

---

## 4. Database Findings

### [DB-01] Composite Covering Index untuk Turnout & Eligibility Mahasiswa
- **Severity:** MEDIUM
- **Location:** `database/migrations/2026_09_14_131927_create_eligible_voters_table.php`
- **Impact:** Pada tabel puluhan ribu mahasiswa, query aggregate melakukan scan ekstra pada row data.
- **Recommended Solution:** Tambahkan composite index `['study_program_id', 'is_eligible']` via migration baru non-destruktif.
- **Risk of Change:** LOW.

### [DB-02] Index Status pada `schedule_change_requests`
- **Severity:** LOW
- **Location:** `database/migrations/2026_09_14_140216_create_schedule_change_requests_table.php`
- **Impact:** Filter permohonan tertunda belum memiliki index komposit.
- **Recommended Solution:** Tambahkan index pada kolom `['election_id', 'status']` via migration baru.
- **Risk of Change:** LOW.

---

## 5. Security Findings

### [SEC-01] Session Security & HTTPS Cookie Flags
- **Severity:** INFO / VERIFIED SECURE
- **Location:** `config/session.php`
- **Audit Result:** HTTPS secure cookie aktif otomatis saat `APP_ENV=production`, `http_only => true`, `same_site => lax`, `serialization => json`.

### [SEC-02] HTTP Security Headers
- **Severity:** INFO / VERIFIED SECURE
- **Location:** `app/Http/Middleware/SecurityHeaders.php`
- **Audit Result:** Menyuntikkan `X-Content-Type-Options`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy`, `Permissions-Policy`, dan HSTS pada koneksi HTTPS.

### [SEC-03] Rate Limiting & Proteksi Brute-Force
- **Severity:** INFO / VERIFIED SECURE
- **Location:** Login (5 attempts/300s), AcceptInvitation (10 attempts/60s), Resend invitation (60s cooldown), Resend OTP (60s cooldown, max 5 attempts).

---

## 6. Dependency Findings
- `composer audit`: **0 security vulnerability advisories**.
- `npm audit`: **0 vulnerabilities**.

---

## 7. Dead Code Findings & Categorization

### SAFE TO REMOVE (Dihapus)
1. `resources/views/components/public/navbar.blade.php`: Terbukti tidak direferensikan (halaman publik memakai `partials.home.navbar`).
2. `resources/views/components/public/footer.blade.php`: Terbukti tidak direferensikan (halaman publik memakai `partials.home.footer`).

### MUST KEEP (Dipertahankan)
1. `resources/views/components/action-button.blade.php`: Digunakan secara luas di tabel CRUD admin.
2. Indonesian route aliases (`/paslon`, `/tindakan-khusus`, dll): Menyediakan 301 Permanent Redirect untuk keramahan URL dan bookmark admin/panitia.
