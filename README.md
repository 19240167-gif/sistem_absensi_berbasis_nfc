# Sistem Informasi Absensi Sekolah Berbasis NFC (Laravel)

Starter project Laravel + Filament + Livewire untuk absensi sekolah berbasis NFC dengan 3 role utama:

- Admin TU: superadmin, kelola data master, mapping identifier NFC (UID kartu atau token HP), laporan + export absensi.
- Guru: monitor absensi kelas wali, validasi/perubahan status absensi.
- Siswa: lihat riwayat dan statistik kehadiran pribadi.

## Stack

- Laravel 10
- Filament 3 (panel admin dan panel siswa)
- Livewire 3 (kiosk mode realtime)

## Fitur Dasar yang Sudah Dibuat

- Multi-role authentication berbasis tabel roles.
- Filament Admin Panel (`/admin`) untuk Admin TU dan Guru.
- Filament Student Panel (`/student`) untuk Siswa.
- Data master:
  - users
  - roles
  - classrooms (rombongan belajar)
  - student_profiles
  - teacher_profiles
  - rfid_tags
  - attendances
- API NFC:
  - `POST /api/nfc/tap`
  - `POST /api/nfc/phone-tap` (alias khusus skenario tap HP)
  - menerima `uid` (kartu) atau `token` (HP/HCE) dari perangkat pembaca NFC (contoh ESP32)
  - mencocokkan identifier ke siswa aktif
  - mencatat/menimpa presensi harian
  - mengirim data ke cache untuk tampilan kiosk realtime
- Kiosk mode realtime:
  - `GET /kiosk-mode`
  - dibangun dengan Livewire polling
  - menampilkan nama/foto siswa saat kartu di-tap
- Export laporan absensi CSV (Admin TU):
  - `GET /reports/attendances/export`

## Struktur Inti Folder

```text
app/
  Filament/
    Resources/                     # Resource admin (roles, users, classrooms, dll)
    Student/Widgets/              # Widget dashboard siswa
  Http/
    Controllers/Api/NfcScanController.php
    Controllers/AttendanceReportExportController.php
    Middleware/EnsureRole.php
  Livewire/KioskMode.php
  Models/
    Role.php
    User.php
    Classroom.php
    StudentProfile.php
    TeacherProfile.php
    RfidTag.php
    Attendance.php

database/
  migrations/                     # skema database absensi NFC
  seeders/                        # RoleSeeder dan DemoUserSeeder

resources/views/livewire/kiosk-mode.blade.php
routes/web.php
routes/api.php
```

## Setup Cepat

1. Install dependency

```bash
composer install
```

2. Konfigurasi environment

- Sesuaikan `.env` untuk database.
- Tambahkan `NFC_DEVICE_KEY` jika ingin mengamankan endpoint scanner.

3. Generate key dan migrate

```bash
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

4. Jalankan server

```bash
php artisan serve
```

## Akun Demo Seeder

Setelah `php artisan migrate --seed`, akun berikut tersedia (password: `password`):

- admin.tu@sekolah.test (Admin TU)
- guru@sekolah.test (Guru)
- siswa@sekolah.test (Siswa)

## Contoh Request API NFC

```bash
curl -X POST http://127.0.0.1:8000/api/nfc/tap \
  -H "Content-Type: application/json" \
  -H "X-Device-Key: your-device-key" \
  -d '{"uid":"04A1B2C3D4"}'
```

Contoh tap dari HP (token HCE):

```bash
curl -X POST http://127.0.0.1:8000/api/nfc/phone-tap \
  -H "Content-Type: application/json" \
  -H "X-Device-Key: your-device-key" \
  -d '{"token":"SISWA-S001-HCE"}'
```

## Catatan Implementasi

- Guru hanya dapat melihat data absensi siswa pada kelas yang dia ampu sebagai wali kelas.
- Guru dapat mengubah status absensi (misalnya dari alfa menjadi sakit/izin), dan sistem otomatis mengisi metadata persetujuan.
- Admin TU memiliki akses penuh ke seluruh data dan export laporan.
- Siswa hanya dapat mengakses panel siswa untuk melihat statistik dan riwayat pribadi.
- Untuk skenario tap pakai HP, gunakan token NFC dari aplikasi HP (Android HCE/NDEF). Sebagian besar perangkat tidak membuka hardware UID NFC asli HP ke aplikasi.
