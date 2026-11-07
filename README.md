# PEMIRA PNB — Sistem E-Voting Pemilihan Raya Mahasiswa

Sistem pemungutan suara elektronik (e-voting) berbasis web untuk Pemilihan Raya Mahasiswa (PEMIRA) Politeknik Negeri Bali (PNB), dibangun menggunakan Laravel, Livewire, dan Tailwind CSS.

---

## 1. Kebutuhan Sistem

Pastikan environment Anda memenuhi spesifikasi berikut:

* **PHP**: `>= 8.3` (ekstensi: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `curl`, `fileinfo`)
* **Composer**: `>= 2.x`
* **Node.js**: `>= 20.x` & **NPM**
* **Database**: MySQL/MariaDB (disarankan untuk produksi) atau SQLite (untuk pengujian lokal)

---

## 2. Panduan Setup Lokal (Development)

Jalankan langkah-langkah berikut secara berurutan di terminal:

```bash
# 1. Clone repository
git clone https://github.com/niro1n/pemira.git
cd pemira

# 2. Install dependensi PHP
composer install

# 3. Salin berkas environment
cp .env.example .env
# Windows PowerShell: copy .env.example .env

# 4. Generate Application Key
php artisan key:generate

# 5. Konfigurasi Database pada berkas .env
# Contoh untuk MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=pemira_db
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Jalankan migrasi dan seeder awal
php artisan migrate --seed

# 7. Buat symbolic link ke storage (wajib untuk foto paslon & lampiran)
php artisan storage:link

# 8. Install dependensi frontend & build asset
npm install
npm run build

# 9. Jalankan server lokal
php artisan serve
```

Akses aplikasi di browser melalui: `http://localhost:8000`

---

## 3. Kredensial Default (Hasil Seeder)

Setelah menjalankan `php artisan migrate --seed`, akun administrator berikut langsung aktif:

| Role | Email | Password | Kegunaan |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `superadmin@pemira.test` | `password123` | Akses penuh, fitur Tindakan Khusus, bypass OTP, aktivasi voter manual, kontrol live count & maintenance |
| **Admin** | `admin@pemira.test` | `password123` | Akses verifikasi DPT, kelola pasangan calon, kelola agenda pemilihan |

> **PENTING**: Segera ubah email dan password akun di atas pada environment produksi melalui database atau profil admin.

---

## 4. Panduan Deployment Produksi (Hosting / cPanel / VPS)

### A. Konfigurasi Web Server (Document Root)
Arahkan Document Root domain/subdomain ke folder **`public/`** (bukan root folder proyek).
* **cPanel**: Ubah Document Root domain ke `pemira/public` atau buat symlink: `ln -s /home/username/pemira/public /home/username/public_html`.
* **Nginx/Apache**: Set root path ke `/path-to/pemira/public`.

### B. Izin Folder (Permissions)
Pastikan folder `storage` dan `bootstrap/cache` dapat ditulis oleh web server:
```bash
chmod -R 775 storage bootstrap/cache
```

### C. Alur Update / Tarik Perubahan Baru (`git pull`)
Setiap kali menarik commit baru di server produksi:

```bash
# 1. Tarik perubahan terbaru
git pull origin main

# 2. Jalankan migrasi jika ada pembaruan database
php artisan migrate --force

# 3. Bersihkan cache aplikasi agar template & config terbaru aktif
php artisan optimize:clear

# 4. (Opsional) Cache untuk performa produksi maksimal
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

> **Catatan Asset**: Folder `public/build/` sudah disertakan di repositori. Anda tidak wajib menjalankan `npm run build` di server jika sudah melakukan build sebelum push ke Git.

---

## 5. Panduan Penggunaan & Fungsi Tiap Halaman Website

Aplikasi dibagi ke dalam 4 kelompok pengguna dan antarmuka:

### A. Halaman Publik (Tamu & Seluruh Mahasiswa)

Halaman yang dapat diakses publik tanpa login:

* **Beranda / Landing Page (`/`)**:
  * Menampilkan fase pemilu berjalan (Pendaftaran, Pemungutan Suara, atau Selesai) lengkap dengan hitung mundur (*countdown*).
  * Showcase kartu Pasangan Calon (Paslon) Ketua & Wakil Ketua BEM dengan nomor urut resmi.
  * Ringkasan alur e-voting 3 langkah untuk mengedukasi mahasiswa pemilih.
  * Menampilkan daftar sponsor & mitra organisasi pendukung PEMIRA.
* **Detail Paslon (`/candidates/{id}`)**:
  * Menampilkan profil lengkap calon ketua dan wakil ketua (nama, foto resmi, NIM, dan program studi).
  * Menjabarkan visi, butir-butir misi, serta program kerja unggulan paslon.
* **Pendaftaran Akun Pemilih (`/register`)**:
  * Menggunakan mekanisme 3 tahap (*wizard*) untuk menjamin keaslian identitas:
    1. **Tahap 1 (Cek DPT)**: Mahasiswa menginput NIM dan Tanggal Lahir untuk dicocokkan dengan master DPT.
    2. **Tahap 2 (Kredensial)**: Jika valid, mahasiswa memasukkan email aktif dan kata sandi baru.
    3. **Tahap 3 (Verifikasi OTP)**: Sistem mengirimkan 6 digit kode OTP ke email mahasiswa untuk aktivasi.
* **Masuk / Login (`/login`)**:
  * Pintu masuk tunggal: mahasiswa dapat masuk menggunakan NIM atau Email + Password. Admin/Super Admin masuk menggunakan Email + Password. Sistem otomatis mengarahkan ke dashboard masing-masing role.
* **Lupa & Reset Password (`/forgot-password`, `/reset-password/{token}`)**:
  * Fasilitas pemulihan kata sandi mandiri via token email jika pengguna lupa password.
* **Halaman Maintenance (`/maintenance`)**:
  * Tampilan ramah pengguna saat sistem sedang dikunci oleh Super Admin untuk pemeliharaan teknis.

---

### B. Portal Mahasiswa / Pemilih (`/voter`)

Area khusus bagi mahasiswa terdaftar yang memiliki hak suara sah:

* **Dashboard Pemilih (`/voter/dashboard`)**:
  * Menampilkan profil mahasiswa, jurusan, dan status kelayakan (`DPT ELIGIBLE`).
  * Informasi status fase pemilihan (kapan bilik suara buka/tutup).
  * Tombol **"Masuk Bilik Suara"** yang aktif selama masa pencoblosan.
  * **Widget Quick Count / Live Count Real-time** (jika diaktifkan oleh Super Admin): Menampilkan perolehan suara sementara, persentase kehadiran DPT, grafik suara masing-masing paslon, dan pembaruan otomatis setiap 15 detik.
  * Setelah memilih, dashboard menampilkan indikator hijau bahwa hak suara telah digunakan secara sah dan anonim.
* **Bilik Suara Elektronik (`/voter/voting`)**:
  * Tempat pencoblosan digital yang dilindungi aturan ketat: hanya bisa dibuka pada fase pemilihan aktif dan hanya bagi mahasiswa yang belum pernah memilih (1 mahasiswa = 1 suara sah).
  * Kartu paslon interaktif dengan tombol "Pilih Paslon Ini" dan jendela modal konfirmasi akhir.
  * **Kerahasiaan Suara (Secret Ballot)**: Suara disimpan secara anonim ke tabel surat suara terpisah dari identitas akun pemilih. Siapapun (termasuk admin dan developer) tidak dapat melacak siapa memilih siapa.
* **Bukti Partisipasi / Tanda Terima (`/voter/success`)**:
  * Surat/kartu bukti digital resmi bahwa mahasiswa telah menggunakan hak pilihnya pada PEMIRA.
  * Formulir survei/feedback kepuasan untuk evaluasi jalannya e-voting.
* **Profil Pemilih (`/voter/profile`)**:
  * Menampilkan rangkuman identitas DPT, email akun, riwayat partisipasi, serta formulir ganti kata sandi akun.

---

### C. Panel Administrasi Panitia (`/admin`)

Area operasional bagi Panitia KPR untuk memantau dan mengelola pemilu:

* **Admin Dashboard (`/admin`)**:
  * Ringkasan statistik real-time: total DPT, akun pemilih terdaftar, total suara masuk, dan grafik persentase partisipasi per jurusan di PNB.
* **Master DPT / Pemilih Tetap (`/admin/eligible-voters`)**:
  * Menampilkan data seluruh mahasiswa PNB yang berhak memilih.
  * Pencarian instan berdasarkan NIM atau Nama, filter per jurusan, dan filter status kelayakan.
  * Impor massal DPT dari file Excel/CSV.
  * Indikator status apakah mahasiswa sudah membuat akun dan apakah sudah menggunakan hak suaranya.
* **Manajemen Pasangan Calon (`/admin/candidate-pairs`)**:
  * Menambah dan mengedit nomor urut paslon, nama ketua dan wakil ketua (terkoneksi langsung ke data DPT).
  * Mengunggah foto resmi kandidat, memasukkan visi, misi, dan program kerja.
* **Agenda Pemilihan (`/admin/elections`)**:
  * Menetapkan periode tahun, nama agenda, jadwal awal/akhir pendaftaran akun, serta jadwal buka/tutup pemungutan suara.
* **Hasil Perhitungan & Laporan (`/admin/results`)**:
  * Rekapitulasi suara resmi per paslon, total suara sah, persentase keikutsertaan DPT, dan ekspor data berita acara.
* **Pengajuan Perubahan Jadwal (`/admin/schedule-requests`)**:
  * Mekanisme tata kelola: panitia dapat mengajukan perpanjangan waktu pemungutan suara secara transparan dengan alasan tertulis, yang membutuhkan persetujuan Super Admin sebelum aktif.
* **Masukan Pemilih (`/admin/feedbacks`)**:
  * Membaca umpan balik, rating, dan saran mahasiswa terkait jalannya pemilu.
* **Kelola Sponsor (`/admin/sponsors`)**:
  * Mengatur logo sponsor dan mitra yang tampil di halaman landing page.
* **Log Audit Aktivitas (`/admin/audit-logs`)**:
  * Perekam jejak audit digital (*digital audit trail*): mencatat setiap tindakan penting (siapa yang login, siapa yang mengedit data paslon, aktivasi manual, alamat IP, dan waktu eksekusi) guna mencegah kecurangan internal.

---

### D. Modul Khusus Super Admin

Fasilitas tingkat tertinggi untuk penanganan darurat dan kendali penuh sistem:

* **Manajemen Administrator (`/admin/admins`)**:
  * Mengundang admin panitia baru melalui tautan undangan email serta menonaktifkan akun admin.
* **Tindakan Khusus (`/admin/special-actions`)**:
  1. **Bypass & Pembuatan OTP Manual**: Mengatasi kendala jika email kampus mahasiswa tidak menerima OTP otomatis. Super Admin cukup menginput email mahasiswa untuk menampilkan kode OTP valid dan menyampaikannya via WhatsApp Helpdesk.
  2. **Aktivasi Akun Pemilih Manual**: Jika mahasiswa terkendala registrasi online, Super Admin dapat memverifikasi fisik (KTM) dan mengaktifkan akun pemilihnya langsung dari panel admin tanpa menyentuh database GUI.
  3. **Kendali Quick Count / Live Count**: Sakelar On/Off instan untuk mengaktifkan atau menyembunyikan hasil perhitungan suara real-time di dashboard mahasiswa.
  4. **Mode Pemeliharaan (Maintenance Mode)**: Sakelar darurat untuk mengunci sistem dari publik jika ada kendala server tanpa memutus akses Super Admin.

---

## 6. Pengujian & Quality Assurance

Sistem telah dilengkapi rangkaian pengujian otomatis (*automated test suite*) berbasis PHPUnit:

```bash
# Menjalankan seluruh pengujian (fitur & unit)
php artisan test

# Menjalankan pengujian alur portal pemilih (Voter)
php artisan test tests/Feature/Voter

# Menjalankan pengujian alur administrasi (Admin & Super Admin)
php artisan test tests/Feature/Admin
```

---

## 7. Struktur Direktori Utama

```
├── app/
│   ├── Enums/            # Enum status pemilihan & role pengguna
│   ├── Livewire/         # Komponen reaktif (Admin, Voter, Auth)
│   ├── Models/           # Model data (Election, Ballot, EligibleVoter, User, dll.)
│   └── Services/         # Logika bisnis (ElectionResultService, BallotService, dll.)
├── database/
│   ├── migrations/       # Skema tabel basis data
│   └── seeders/          # Data inisialisasi awal (Super Admin, Admin, Jurusan)
├── public/
│   └── build/            # Berkas asset CSS & JS hasil kompilasi produksi
├── resources/
│   └── views/            # Template antarmuka Blade & Livewire
├── routes/
│   └── web.php           # Definisi seluruh rute halaman & proteksi middleware
└── tests/                # Berkas automated test suite
```
