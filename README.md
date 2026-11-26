# 📋 SIMONEV Non-ASN — Sistem Monitoring & Presensi Pegawai Non-ASN

> Sistem informasi berbasis web untuk monitoring aktivitas, presensi harian, dan manajemen izin/cuti bagi Pegawai Non-ASN (Satpam, Driver, Petugas Kebersihan) di lingkungan Kantor Wilayah Kementerian Hukum RI.

---

## 🧭 Daftar Isi

- [Tentang Sistem](#-tentang-sistem)
- [Fitur Utama](#-fitur-utama)
- [Hak Akses per Role](#-hak-akses-per-role)
- [Tech Stack](#-tech-stack)
- [Struktur Proyek](#-struktur-proyek)
- [Panduan Instalasi](#-panduan-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Akun Default](#-akun-default)
- [Alur Penggunaan](#-alur-penggunaan)
- [Skema Database](#-skema-database)
- [Perintah Artisan Berguna](#-perintah-artisan-berguna)

---

## 📌 Tentang Sistem

**SIMONEV Non-ASN** adalah aplikasi manajemen kehadiran dan aktivitas harian yang dirancang khusus untuk:

- Memantau kehadiran (check-in/check-out) pegawai Non-ASN secara real-time berbasis **GPS lokasi kantor**
- Melacak laporan kegiatan harian (**Monitoring & Evaluasi / Monev**) oleh petugas lapangan
- Mengelola pengajuan dan persetujuan **izin & cuti** beserta bukti dokumen digital
- Menyediakan **dashboard admin** dengan statistik dan filter instan per kategori kehadiran
- Mencetak **rekap absensi & surat cuti** ber-QR Code sebagai bukti resmi digital

---

## ✨ Fitur Utama

### 🏠 Dashboard Admin & Supervisor
- Statistik real-time: Total Pegawai, Masuk, Terlambat, Sakit, Izin/Cuti, Alpha
- Filter chip berwarna: klik langsung untuk menyaring tabel pegawai berdasarkan status
- SweetAlert otomatis saat ada pengajuan cuti baru yang menunggu persetujuan
- Cetak rekap harian dalam format PDF ber-QR Code digital
- Monitoring laporan Monev seluruh pegawai dalam satu tampilan

### 📍 Presensi / Absensi Berbasis GPS
- Check-in & check-out dengan **validasi jarak GPS** dari titik lokasi kantor
- Status otomatis: **Tepat Waktu** atau **Terlambat** berdasarkan jam masuk yang dikonfigurasi
- Upload foto wajah saat absen sebagai bukti kehadiran
- Radius jarak kantor dapat dikonfigurasi oleh admin (meter)
- Kalibrasi ulang titik koordinat kantor langsung dari browser

### 📊 Rekapan Absensi Bulanan
- Tabel rekap per bulan dengan filter berdasarkan pegawai (admin/supervisor) atau diri sendiri (pegawai)
- Rincian status per hari: Hadir, Terlambat, Izin, Sakit, Alpha
- Statistik ringkasan bulan: total hadir, total terlambat, total izin, total sakit
- Cetak rekap absensi dengan **QR Code** tanda tangan digital

### 📝 Monitoring & Evaluasi (Monev) Harian
- Pegawai dapat mencatat hingga **10 kegiatan** per hari
- Setiap kegiatan berisi: uraian aktivitas, kendala/hambatan, dan foto dokumentasi
- Admin & supervisor dapat memantau seluruh laporan monev dari dashboard
- Riwayat monev terintegrasi dalam halaman presensi harian

### 📄 Administrasi Izin & Cuti
- Pegawai mengajukan permohonan izin/cuti dengan keterangan dan lampiran dokumen
- Alur persetujuan: **Pending → Disetujui / Ditolak** oleh supervisor/admin
- Notifikasi SweetAlert untuk admin ketika ada pengajuan baru
- Cetak **Surat Cuti** ber-QR Code sebagai bukti resmi yang dapat diunduh pegawai
- Batalkan pengajuan sebelum diproses

### 👥 Manajemen SDM (Admin only)
- CRUD data pengguna: nama, email, NIK, jabatan, divisi, nomor HP, alamat
- Assign jabatan: **Satpam, Driver, Petugas Kebersihan, Kabag (Kepala Bagian)**
- Reset/generate ulang password dengan fitur salin ke clipboard
- Konfirmasi hapus data dengan SweetAlert

### ⚙️ Pengaturan Lokasi Kantor (Admin only)
- Atur nama kantor, koordinat GPS (latitude & longitude), dan radius toleransi absen
- Kalibrasi otomatis koordinat berdasarkan posisi admin saat ini
- Atur jam masuk & jam keluar kantor

---

## 🔐 Hak Akses per Role

| Fitur | Admin | Supervisor (Kabag) | Pegawai Non-ASN |
|---|:---:|:---:|:---:|
| Dashboard Monitoring | ✅ | ✅ | ❌ |
| Presensi (Check-in/out) | ✅ | ✅ | ✅ |
| Monev Harian | ✅ | ✅ | ✅ |
| Rekap Absensi — Diri Sendiri | ✅ | ✅ | ✅ |
| Rekap Absensi — Semua Pegawai | ✅ | ✅ | ❌ |
| Cetak Rekap Absensi | ✅ | ✅ | ✅ |
| Pengajuan Izin/Cuti | ✅ | ✅ | ✅ |
| Setujui / Tolak Izin/Cuti | ✅ | ✅ | ❌ |
| Cetak Surat Cuti ber-QR | ✅ | ✅ | ✅ |
| Manajemen SDM (CRUD User) | ✅ | ❌ | ❌ |
| Pengaturan Lokasi Kantor | ✅ | ❌ | ❌ |

> **Catatan:** Role `supervisor` di database menggunakan nilai `system_role = 'supervisor'`. Role ini setara Kepala Bagian (Kabag) yang mengawasi pegawai Non-ASN.

---

## 🛠 Tech Stack

| Layer | Teknologi |
|---|---|
| **Backend Framework** | Laravel 11.x (PHP 8.2+) |
| **Frontend Styling** | Tailwind CSS 3.4 |
| **Build Tool** | Vite 5.x |
| **Database** | MySQL 8.x / MariaDB |
| **Alert & Konfirmasi** | SweetAlert2 11.x |
| **HTTP Client** | Axios 1.x |
| **QR Code** | Custom SVG QR Generator (`App\Helpers\QrCodeHelper`) |
| **Autentikasi** | Laravel Auth (Session-based) |
| **Template Engine** | Blade (Laravel) |
| **Dev Server** | Laragon / XAMPP |

---

## 📁 Struktur Proyek

```
project-management/
├── app/
│   ├── Helpers/
│   │   └── QrCodeHelper.php          # Generator QR Code SVG custom
│   ├── Http/Controllers/
│   │   ├── AttendanceController.php  # Presensi, Monev, Rekap, Settings
│   │   ├── DashboardController.php   # Dashboard monitoring + cetak harian
│   │   ├── AdministrationController.php  # Izin & Cuti (pengajuan, approve, cetak)
│   │   ├── AdminController.php       # Manajemen SDM (CRUD user)
│   │   └── AuthController.php        # Login & Logout
│   └── Models/
│       ├── User.php                  # Auth user + system_role
│       ├── Employee.php              # Data kepegawaian Non-ASN
│       ├── Attendance.php            # Record absensi harian
│       ├── MonevActivity.php         # Laporan kegiatan monev
│       ├── Administration.php        # Pengajuan izin/cuti
│       ├── OfficeSetting.php         # Konfigurasi kantor & GPS
│       └── Role.php                  # Jabatan (Satpam, Driver, Kebersihan)
├── resources/views/
│   ├── dashboard.blade.php           # Dashboard utama admin/supervisor
│   ├── attendance/
│   │   ├── index.blade.php           # Halaman presensi & monev harian
│   │   ├── recap.blade.php           # Rekap absensi bulanan
│   │   ├── settings.blade.php        # Pengaturan lokasi kantor
│   │   ├── print_today.blade.php     # Cetak rekap harian (PDF-ready)
│   │   └── print_recap.blade.php     # Cetak rekap bulanan (PDF-ready)
│   ├── administration/
│   │   ├── index.blade.php           # Daftar pengajuan izin/cuti
│   │   ├── create.blade.php          # Form pengajuan baru
│   │   ├── waiting.blade.php         # Status pengajuan (menunggu)
│   │   └── print_leave.blade.php     # Surat cuti ber-QR Code
│   ├── admin/
│   │   ├── index.blade.php           # Daftar pegawai Non-ASN
│   │   ├── create.blade.php          # Tambah SDM baru
│   │   └── edit.blade.php            # Edit & manajemen password
│   └── auth/
│       └── login.blade.php           # Halaman login
├── database/
│   ├── migrations/                   # Skema tabel lengkap
│   └── seeders/
│       ├── UserSeeder.php            # Akun admin default
│       ├── RoleSeeder.php            # Data jabatan Non-ASN
│       └── NonAsnDummySeeder.php     # 10 user dummy (Satpam/Driver/Kebersihan)
├── routes/web.php                    # Definisi seluruh route
├── public/
│   ├── kemenkum.png                  # Logo Kemenkum RI
│   └── build/                        # Asset hasil vite build
├── package.json                      # Dependencies frontend
├── tailwind.config.js
└── vite.config.js
```

---

## 🚀 Panduan Instalasi

### Prasyarat

Pastikan sudah terinstal:
- **PHP** ≥ 8.2
- **Composer** ≥ 2.x
- **Node.js** ≥ 18.x + **npm**
- **MySQL** 8.x / MariaDB
- **Laragon** (direkomendasikan untuk Windows) atau XAMPP

---

### Langkah Instalasi

**1. Clone repositori**
```bash
git clone https://github.com/username/simonev-nonasn.git
cd simonev-nonasn
```

**2. Install dependensi PHP**
```bash
composer install
```

**3. Install dependensi frontend**
```bash
npm install
```

**4. Salin dan konfigurasi file environment**
```bash
cp .env.example .env
php artisan key:generate
```

**5. Konfigurasi database** di `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simonev_nonasn
DB_USERNAME=root
DB_PASSWORD=
```

**6. Jalankan migrasi & seeder**
```bash
php artisan migrate
php artisan db:seed
```

> Perintah ini akan membuat semua tabel dan mengisi data awal termasuk akun admin, jabatan Non-ASN, dan 10 user dummy pegawai.

**7. Buat symbolic link storage**
```bash
php artisan storage:link
```

**8. Build asset frontend**
```bash
npm run build
```

**9. Jalankan server lokal**
```bash
php artisan serve
```

Akses aplikasi di: **http://localhost:8000**

---

### Untuk Development (Hot Reload)

Jalankan dua terminal secara bersamaan:

```bash
# Terminal 1 — Backend
php artisan serve

# Terminal 2 — Frontend dev server (HMR)
npm run dev
```

---

## ⚙️ Konfigurasi

### Lokasi Kantor (via UI Admin)

Setelah login sebagai admin, pergi ke **Pengaturan** (ikon gear di sidebar) untuk mengatur:

| Parameter | Keterangan |
|---|---|
| Nama Kantor | Nama tampilan kantor |
| Latitude & Longitude | Koordinat GPS titik kantor |
| Radius Toleransi | Jarak maksimum absen dari kantor (meter) |
| Jam Masuk | Batas jam masuk tepat waktu (cth: `08:00`) |
| Jam Keluar | Jam pulang kantor (cth: `17:00`) |

> **Tips:** Gunakan tombol **"Gunakan Lokasi Saya Sekarang"** untuk kalibrasi otomatis koordinat berdasarkan GPS browser admin.

### Environment Variables Penting

```env
APP_NAME="SIMONEV Non-ASN"
APP_URL=http://localhost:8000
ASSET_URL=http://localhost:8000

# Filesystem untuk foto absensi & lampiran
FILESYSTEM_DISK=public
```

---

## 🔑 Akun Default

Setelah menjalankan `php artisan db:seed`, akun berikut tersedia:

### Akun Admin
| Field | Value |
|---|---|
| **Email** | `admin@example.com` |
| **Password** | `admin` |
| **Role** | Admin |

> ⚠️ **Segera ganti password** setelah login pertama kali di lingkungan produksi!

### Akun Pegawai Non-ASN Dummy (10 user)

| Nama | Email | Jabatan | Password |
|---|---|---|---|
| Joko Susilo | `joko.satpam@nonasn.id` | Satpam | `password` |
| Bambang Prasetyo | `bambang.satpam@nonasn.id` | Satpam | `password` |
| Hendra Gunawan | `hendra.satpam@nonasn.id` | Satpam | `password` |
| Supriyadi | `supriyadi.satpam@nonasn.id` | Satpam | `password` |
| Ahmad Fauzi | `ahmad.driver@nonasn.id` | Driver | `password` |
| Budi Santoso | `budi.driver@nonasn.id` | Driver | `password` |
| Wawan Kurniawan | `wawan.driver@nonasn.id` | Driver | `password` |
| Siti Rahayu | `siti.kebersihan@nonasn.id` | Petugas Kebersihan | `password` |
| Dewi Lestari | `dewi.kebersihan@nonasn.id` | Petugas Kebersihan | `password` |
| Rini Susanti | `rini.kebersihan@nonasn.id` | Petugas Kebersihan | `password` |

---

## 🔄 Alur Penggunaan

### Alur Harian Pegawai Non-ASN

```
Login → Halaman Presensi
  ↓
Check-in (GPS + Foto Wajah)
  ↓
Input Laporan Monev Kegiatan (opsional, maks 10)
  ↓
Check-out (GPS + Foto Pulang)
  ↓
Lihat Rekap Absensi Pribadi
```

### Alur Pengajuan Izin/Cuti

```
Pegawai: Buat Pengajuan → Upload Lampiran Dokumen
  ↓
Status: PENDING
  ↓
Admin/Supervisor: Tinjau Pengajuan (SweetAlert Notif)
  ↓
DISETUJUI ──→ Pegawai: Download Surat Cuti ber-QR Code
  ↓
DITOLAK  ──→ Pegawai: Bisa Ajukan Ulang
```

### Alur Monitoring Admin

```
Login Admin → Dashboard
  ↓
Lihat Statistik Real-time (Total, Masuk, Telat, Sakit, Alpha)
  ↓
Klik Filter Chip → Tabel Pegawai Tersaring Otomatis
  ↓
Klik Nama / Foto → Lihat Detail Monev Kegiatan
  ↓
Download PDF Rekap Harian (ber-QR Code)
```

---

## 🗄 Skema Database

### Tabel Utama

```
users                    — Data akun login (email, password, system_role)
employees                — Data kepegawaian (NIK, jabatan, HP, alamat)
roles                    — Master jabatan Non-ASN (Satpam, Driver, Kebersihan, dll)
attendances              — Record absensi harian (check-in/out, foto, status GPS)
monev_activities         — Laporan kegiatan harian per pegawai
administrations          — Pengajuan izin/cuti (status, lampiran, periode)
office_settings          — Konfigurasi kantor (GPS, jam kerja, radius)
```

### Relasi Utama

```
User (1) ──── (1) Employee ──── (N) jabatan via Role
User (1) ──── (N) Attendance
User (1) ──── (N) MonevActivity
User (1) ──── (N) Administration
OfficeSetting (singleton) ──── konfigurasi global
```

---

## 📡 Daftar Route

| Method | URL | Fungsi |
|---|---|---|
| `GET` | `/login` | Halaman login |
| `POST` | `/login` | Proses autentikasi |
| `GET` | `/dashboard` | Dashboard monitoring admin/supervisor |
| `GET` | `/dashboard/print-today` | Cetak rekap harian PDF |
| `GET` | `/attendance` | Halaman presensi harian |
| `POST` | `/attendance/checkin` | Proses check-in |
| `POST` | `/attendance/checkout` | Proses check-out |
| `POST` | `/attendance/monev` | Simpan laporan monev |
| `GET` | `/attendance/recap` | Rekap absensi bulanan |
| `GET` | `/attendance/print` | Cetak rekap bulanan PDF |
| `GET` | `/attendance/settings` | Pengaturan lokasi kantor |
| `POST` | `/attendance/settings` | Simpan pengaturan |
| `GET` | `/administration` | Daftar pengajuan izin/cuti |
| `POST` | `/administration` | Buat pengajuan baru |
| `POST` | `/administration/{id}/approve` | Setujui pengajuan |
| `POST` | `/administration/{id}/reject` | Tolak pengajuan |
| `GET` | `/administration/{id}/print` | Cetak surat cuti ber-QR |
| `GET` | `/admin` | Daftar SDM Non-ASN |
| `POST` | `/admin` | Tambah SDM baru |
| `PUT` | `/admin/{id}` | Edit data SDM |
| `DELETE` | `/admin/{id}` | Hapus SDM |

---

## 🛠 Perintah Artisan Berguna

```bash
# Jalankan semua migrasi ulang dan seed ulang (HATI-HATI: hapus semua data!)
php artisan migrate:fresh --seed

# Hanya jalankan seeder tertentu
php artisan db:seed --class=NonAsnDummySeeder

# Bersihkan cache view setelah perubahan blade
php artisan view:clear

# Bersihkan semua cache
php artisan optimize:clear

# Build ulang asset frontend (wajib setelah edit CSS/JS)
npm run build

# Lihat semua route terdaftar
php artisan route:list

# Cek status aplikasi
php artisan about
```

---

## ⚠️ Catatan Penting untuk Produksi

**Sebelum deploy ke production, pastikan:**

1. Set `APP_ENV=production` dan `APP_DEBUG=false` di `.env`
2. Jalankan `php artisan config:cache` dan `php artisan route:cache`
3. Gunakan **HTTPS** — fitur GPS browser **hanya berfungsi pada koneksi HTTPS**
4. Ganti semua password default sebelum go-live
5. Konfigurasi `FILESYSTEM_DISK=public` dan pastikan `storage:link` sudah berjalan
6. Set permission folder `storage/` dan `bootstrap/cache/` ke `775`

> **Peringatan GPS:** Fitur check-in berbasis GPS memerlukan izin lokasi dari browser. Jika diakses via HTTP (bukan HTTPS), browser modern akan **memblokir akses geolokasi** secara default.

---

## 👨‍💻 Dikembangkan Untuk

**Kantor Wilayah Kementerian Hukum RI Kepulauan Bangka Belitung**

Sistem ini dibangun untuk menggantikan pencatatan kehadiran manual dan meningkatkan akuntabilitas aktivitas harian pegawai Non-ASN (Satpam, Driver, dan Petugas Kebersihan) di lingkungan Kanwil Kemenkumham Kepulauan Bangka Belitung.

---

*Dibuat dengan ❤️ menggunakan Laravel 11 + Tailwind CSS*
