# Instruksi Perbaikan Lanjutan (Next Prompt)

Dokumen ini berisi daftar temuan bug dan perbaikan yang harus dikerjakan pada sesi berikutnya untuk fitur **2-Shift Satpam & Hari Kerja Berbasis Role** di branch `daffa`.

---

## 1. Bug: `Undefined array key "total_libur_terjadwal"` saat Login Superadmin (`/dashboard`)

### Gejala
Saat login sebagai Superadmin/Admin dan membuka `http://127.0.0.1:8000/dashboard`, muncul error:
```
ErrorException: Undefined array key "total_libur_terjadwal"
at resources/views/dashboard.blade.php:84
```

### Temuan Awal
- Dari `storage/logs/laravel.log`, array `$stats` yang diterima view `dashboard.blade.php` sempat berisi key `"total_libur"` alih-alih `"total_libur_terjadwal"`.
- Sementara di `app/Http/Controllers/DashboardController.php` baris 119 sudah tertulis `'total_libur_terjadwal' => count($listLiburTerjadwal)`.

### Langkah Perbaikan
1. Periksa `app/Http/Controllers/DashboardController.php` pada method `index()` dan `printToday()` untuk memastikan key `$stats['total_libur_terjadwal']` konsisten dengan `resources/views/dashboard.blade.php`.
2. Tambahkan fallback aman di `resources/views/dashboard.blade.php` (misalnya `$stats['total_libur_terjadwal'] ?? 0`) agar tidak mudah crash jika data kosong/tidak terdefinisi.
3. Jalankan `php artisan optimize:clear` dan `php artisan view:clear` untuk membersihkan cache view/route/config yang mungkin masih menyimpan versi lama.
4. Buat Feature Test untuk memastikan user `admin` / `superadmin` dapat mengakses `GET /dashboard` dengan status `200 OK` tanpa error.

---

## 2. Bug UX: Homepage Role Satpam Terlihat Memiliki "2 Tempat Absen"

### Gejala
Ketika login dengan role **Satpam**, di halaman utama (`/attendance`) terlihat seperti ada 2 tempat absen.

### Temuan Awal
Pada `resources/views/attendance/index.blade.php`:
- Grid status presensi menampilkan 2 card berdampingan:
  1. `1. Absen Masuk (Datang)` (berisi pemilihan shift, kamera, dan tombol submit)
  2. `2. Absen Pulang & Monev` (berisi placeholder *"Anda harus melakukan absen masuk terlebih dahulu..."*)
- Selain itu, ketika sudah absen masuk, di bawah grid tersebut muncul lagi section ke-3: `Form Monev Aktivitas Harian & Absen Pulang`.

### Langkah Perbaikan
1. Login sebagai akun **Satpam** di browser (atau cek rendered view `/attendance`) untuk melihat tampilan aktualnya secara visual.
2. Sederhanakan layout `resources/views/attendance/index.blade.php` agar tidak membingungkan user:
   - Jika belum absen masuk: fokuskan tampilan pada **Absen Masuk** (jangan tampilkan box kosong/placeholder yang terasa seperti form absen kedua).
   - Jika sudah absen masuk & belum absen pulang: fokuskan pada status masuk + **Form Monev & Absen Pulang** secara jelas dalam satu alur.
   - Pastikan juga tidak ada duplikasi menu/link absensi yang membingungkan di sidebar.

---

## 3. Bug Validasi: Satpam Bisa Absen Sebelum Memilih Shift

### Gejala
Saat ini Satpam bisa langsung mengambil foto dan melakukan Absen Masuk tanpa pernah memilih shift terlebih dahulu.

### Penyebab (di `resources/views/attendance/index.blade.php`)
- Radio button `Shift Pagi` memiliki atribut `checked` secara default (`<input type="radio" name="shift_select" value="pagi" ... checked>`).
- Hidden input `#shiftInput` memiliki `value="pagi"` secara default (`<input type="hidden" name="shift" id="shiftInput" value="pagi">`).

### Langkah Perbaikan
1. Hapus atribut `checked` dari kedua radio button `shift_select` (`pagi` dan `malam`), dan ubah default `#shiftInput` menjadi `value=""` (kosong).
2. Pastikan secara visual tidak ada card shift yang terpilih saat halaman pertama kali dibuka.
3. **Kunci aksi absen masuk sebelum shift dipilih:**
   - Nonaktifkan / sembunyikan atau kunci area kamera & tombol ambil foto / tombol `Kirim Absen Masuk` sampai Satpam secara eksplisit mengklik **Shift Pagi** atau **Shift Malam**.
   - Tampilkan pesan petunjuk yang jelas (misal: *"Silakan pilih Shift Pagi atau Shift Malam terlebih dahulu sebelum mengambil foto absen masuk."*).
   - Pada JavaScript submit handler `#formCheckIn`, tetap pertahankan validasi `#shiftInput` tidak boleh kosong.
4. Pastikan validasi backend di `AttendanceController::checkIn()` (`'shift' => 'required|in:pagi,malam'`) tetap aktif dan diuji dengan Feature Test.

---

## 4. Verifikasi Akhir Sebelum Commit & Push
1. Jalankan `npm run build` dan `php artisan test`.
2. Uji langsung di browser lokal (`http://127.0.0.1:8000`) untuk:
   - Login sebagai **Superadmin/Admin** $\rightarrow$ `/dashboard` terbuka normal tanpa error.
   - Login sebagai **Satpam** $\rightarrow$ `/attendance` tampil rapi (tidak ada 2 tempat absen membingungkan) dan tidak bisa absen sebelum memilih shift.
   - Login sebagai **Non-ASN lain** (Driver/Kebersihan) $\rightarrow$ tidak muncul pilihan shift.
3. Commit dan push ke branch `daffa`.
