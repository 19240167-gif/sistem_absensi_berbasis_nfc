# Aplikasi Siswa (Flutter)

Aplikasi ini dipakai siswa untuk absensi NFC/HCE. Token NFC dibuat dari HP siswa dan dipakai sebagai pengganti kartu.

## Konsep Utama

- HP siswa membuat token NFC unik (disimpan di secure storage).
- HP mengemulasikan kartu NFC lewat HCE.
- Admin TU menempelkan HP siswa ke NFC reader admin untuk membaca token.
- Token disimpan di backend Laravel sebagai identitas NFC siswa.
- Token bisa di-remap jika siswa ganti HP.

## Struktur Flow

1. Siswa membuka tab **Mode NFC** dan mengaktifkan HCE.
2. Admin TU membuka fitur mapping di web (profil siswa / mapping NFC).
3. Admin menempelkan HP siswa ke NFC reader admin.
4. Reader menerima token, lalu backend menyimpan token ke siswa terkait.
5. Setelah terdaftar, HP siswa bisa dipakai tap absensi normal.

## Login Mobile

- **Siswa** login dengan **NIS + Tanggal Lahir**.
- **Guru** login dengan **email + password** (dibuat Admin TU di web).
- Setelah login, aplikasi menampilkan menu sesuai role:
  - Siswa: Beranda, Daftar HP, Mode NFC.
  - Guru: daftar siswa yang tidak hadir.

## Ringkasan Kehadiran Siswa

Aplikasi siswa menampilkan ringkasan kehadiran per bulan dari endpoint:

```
GET /api/mobile/student/summary
Authorization: Bearer <token>
```

Respons berisi jumlah hadir/izin/sakit/alfa dalam periode aktif.

## Konfigurasi

Ubah file `mobile/.env`:

```
API_BASE_URL=http://10.0.2.2:8000/api
API_TIMEOUT_SECONDS=10
```

Gunakan IP LAN server Laravel jika memakai HP fisik.

## Token & Remap

- Token disimpan di secure storage, jadi tidak hilang saat app ditutup.
- Jika token sudah dipakai siswa lain, backend harus mengembalikan 409.
- Siswa bisa menekan **Ganti Token** untuk membuat token baru.
- Admin TU melakukan mapping ulang token baru.

## Endpoint Laravel yang Diperlukan (opsional)

Jika ingin siswa mendaftarkan token langsung dari aplikasi:

```
POST /api/mobile/register
{
  "student_code": "0012345678",
  "token": "<token-hp>",
  "device_label": "Samsung A52",
  "platform": "android"
}
```

Backend harus:
- Validasi siswa
- Pastikan token unik
- Kembalikan 409 jika token dipakai siswa lain

## Menjalankan

```
cd mobile
flutter pub get
flutter run
```
