# SIMON-KUMBABEL

**Sistem Informasi Monitoring Aktivitas dan Kehadiran Pegawai Non-ASN Kanwil Kemenkum Babel**

Aplikasi internal untuk mencatat presensi dan kegiatan harian, mengelola pengajuan izin atau cuti, serta menyiapkan rekap kehadiran dan aktivitas pegawai.

## Fitur utama

| Fitur | Kegunaan |
| --- | --- |
| Presensi | Absen masuk dan pulang dengan validasi radius kantor dan foto langsung dari kamera browser. |
| Aktivitas Monev | Catat kegiatan harian, kendala, dan bukti foto. Aktivitas dapat direkap berdasarkan periode dan pegawai. |
| Izin dan cuti | Ajukan permohonan dengan lampiran bila diperlukan. Admin dan supervisor dapat meninjau pengajuan melalui notifikasi di navbar. |
| Dashboard dan laporan | Pantau grafik kehadiran yang diperbarui setiap 30 detik. Cetak atau simpan rekap harian, bulanan, dan tahunan sebagai PDF melalui browser. |
| Profil dan pengelolaan SDM | Pengguna dapat memperbarui foto dan biodata sendiri. Admin mengelola akun, divisi, hak akses, serta pengaturan lokasi dan jam presensi. |

## Hak akses

| Peran | Akses utama |
| --- | --- |
| Admin | Dashboard, laporan seluruh pegawai, persetujuan izin dan cuti, pengelolaan SDM, serta pengaturan presensi. |
| Supervisor | Dashboard, laporan seluruh pegawai, dan persetujuan izin dan cuti. |
| Pegawai | Presensi, aktivitas Monev, pengajuan izin atau cuti, dan rekapan sendiri. |

Divisi yang tersedia meliputi **Petugas Kebersihan, Satpam, Driver, dan Kabag (Kepala Bagian)**. Hak akses akun ditetapkan secara terpisah dari divisi.

## Menjalankan secara lokal

Proyek menggunakan Laravel 11, Blade, Tailwind CSS, dan Vite. Siapkan **PHP 8.2 atau lebih baru**, Composer, dan MySQL. Perintah berikut menggunakan PowerShell dan bisa dijalankan dari terminal Laragon.

1. Nyalakan MySQL, buat database kosong, lalu jalankan perintah berikut dari folder proyek.

   ```powershell
   Copy-Item .env.example .env
   New-Item -ItemType Directory -Force -Path bootstrap/cache,storage/framework/views,storage/framework/sessions,storage/framework/cache/data | Out-Null
   composer install
   php artisan key:generate
   ```

2. Sesuaikan `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` di `.env` dengan database lokal. Setel `APP_URL` ke `http://127.0.0.1:8000` jika memakai server Artisan, lalu jalankan migrasi, tautkan penyimpanan, dan mulai server.

   ```powershell
   php artisan migrate
   php artisan storage:link
   php artisan serve
   ```

3. Buka aplikasi dan masuk dengan akun yang tersedia. Untuk mengisi database lokal baru dengan akun contoh dan data uji, jalankan `php artisan db:seed` satu kali. Akun admin contoh didefinisikan di [UserSeeder.php](database/seeders/UserSeeder.php). [Buka SIMON-KUMBABEL di server lokal](http://127.0.0.1:8000)

### Pengembangan aset frontend

Jika mengubah aset Vite di `resources/css` atau `resources/js`, gunakan Node.js dan npm untuk membangun ulang aset.

```powershell
npm ci
npm run build
```

Gunakan `npm run dev` di terminal terpisah saat mengerjakan aset dengan Vite.

## Penggunaan

- **Presensi** — Izinkan akses kamera dan lokasi di browser. Akses dari HP memakai HTTPS agar kedua fitur tersedia.
- **Rekap bulanan** — Buka **Rekapan Absensi & Monev**, pilih bulan dan tahun, lalu klik **Tampilkan rekap**. Admin dan supervisor juga bisa memilih pegawai.
- **Cetak laporan** — Di dashboard, pilih bulan atau **Setahun penuh**, tentukan tahun, lalu klik **Buka laporan cetak**. Gunakan **Cetak / Simpan PDF** untuk menyimpan laporan lewat dialog cetak browser.
- **Foto dan lampiran** — Jika file sudah tersimpan tetapi gagal tampil pada instalasi baru, periksa tautan dari `public/storage` ke `storage/app/public`. Perintah `php artisan storage:link` membuat tautan tersebut.

---

© 2026 Fadhil Raihan · SIMON-KUMBABEL


## Jadwal dan shift presensi

Monev dapat dicatat terpisah hingga pukul 14.30 WIB. Jika belum ada kegiatan
untuk tanggal mulai shift, form absen pulang meminta uraian dan kendala.
Kegiatan tersebut direkap pada tanggal mulai shift, termasuk untuk shift malam.


Satpam bekerja Senin–Jumat dan memilih Shift Pagi atau Shift Malam sebelum
mengambil foto masuk. Shift malam yang belum selesai dapat ditutup keesokan
harinya. Driver, Petugas Kebersihan, dan pegawai Non-ASN lain bekerja Senin–Kamis.
Batas tepat waktu presensi reguler adalah 08.00 WIB secara default. Jam mulai
bersifat informatif dan jadwal dapat disesuaikan di pengaturan kantor.

Radius bernilai 0 menonaktifkan pembatasan jarak untuk pengujian.

Kode QR memuat ringkasan laporan yang dapat dipindai. Ekstensi PHP XMLWriter diperlukan untuk mencetak kode QR.
