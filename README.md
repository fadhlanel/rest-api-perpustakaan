# REST API Perpustakaan Sekolah — Tugas A (Website)

Dokumen rancangan: `../PLAN-Langkah-1-REST-API.md`

REST API sederhana untuk digitalisasi sistem perpustakaan sekolah. API ini menjadi backend untuk aplikasi **Website (Admin)** dan nantinya dikonsumsi oleh aplikasi **Android (User)** pada Tugas B.

- **Framework:** Laravel 13 (PHP 8.3)
- **Database lokal:** MySQL/MariaDB 10.x (`db_perpustakaan`)
- **Database production:** MariaDB Cloudways (`pkwgrvstke`)
- **Deployment production:** Cloudways (PHP + MariaDB)
- **Environment lokal:** Laragon (PHP + MySQL), Postman untuk pengujian
- **Dokumentasi API:** `docs/Perpustakaan-API.postman_collection.json` (import ke Postman)

---

## Cara Menjalankan Project

### 1. Persiapan
- Laragon Full terinstall (berisi PHP 8.3 + MySQL 8.4), klik **Start All**
- Git sudah terinstall

### 2. Clone & Install
```bash
git clone <url-repo-github-anda>
cd rest-api-perpustakaan
composer install
```

### 3. Konfigurasi Database
Buat database MySQL/MariaDB dengan nama `db_perpustakaan`, atau jalankan SQL berikut di HeidiSQL (Laragon akan membukanya bila database sudah dijalankan):

```sql
CREATE DATABASE IF NOT EXISTS db_perpustakaan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Sesuaikan koneksi di file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_perpustakaan
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Migrasi + Data Dummy
```bash
php artisan migrate --seed
```
Perintah ini membuat tabel (`kategori`, `penulis`, `penerbit`, `buku`, `peminjaman`, `pengembalian`, `anggota`, `pengumuman`, `users`, `profile_edit_requests`, `pengaturan`) dan mengisi data dummy: kategori, penulis, penerbit, buku, transaksi peminjaman, akun admin.

### 5. Jalankan Server
```bash
php artisan serve
```
API tersedia di `http://127.0.0.1:8000/api/v1`

### 6. Uji API
1. Buka **Postman**
2. **Import** → pilih `docs/Perpustakaan-API.postman_collection.json`
3. Jalankan request — variable `base_url` sudah diatur otomatis

### Deployment Production (Cloudways)

Panduan lengkap Cloudways + MariaDB tersedia di [`DEPLOYMENT.md`](DEPLOYMENT.md).

Minimum environment production:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...          # php artisan key:generate di server
APP_URL=https://phplaravel-1688054-6782384.cloudwaysapps.com
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pkwgrvstke
DB_USERNAME=pkwgrvstke
DB_PASSWORD=                # dari panel Cloudways
LOG_CHANNEL=stderr
CACHE_STORE=array
SESSION_DRIVER=cookie
SESSION_SECURE_COOKIE=true
QUEUE_CONNECTION=sync
ADMIN_EMAIL=                # untuk AdminUserSeeder
ADMIN_PASSWORD=             # untuk AdminUserSeeder
```

Health check production: `GET https://phplaravel-1688054-6782384.cloudwaysapps.com/api`.

## 7. Jalankan Test Otomatis
```bash
php artisan test tests/Feature/ApiPerpustakaanTest.php
```
Feature test mencakup: health check, pagination, searching, CRUD master proteksi hapus, validasi 422, pengurangan stok saat peminjaman, dan pengembalian stok saat pengembalian.

---

## Daftar Endpoint

Base URL: `http://127.0.0.1:8000/api/v1` (lokal) atau `https://phplaravel-1688054-6782384.cloudwaysapps.com/api/v1` (produksi)

| Method | Endpoint | Fungsi |
|--------|----------------|----------------------------------|
| GET | `/buku` | Daftar buku (pagination + searching) |
| POST | `/buku` | Tambah buku |
| GET | `/buku/{id}` | Detail buku |
| PUT | `/buku/{id}` | Ubah buku |
| DELETE | `/buku/{id}` | Hapus buku |
| GET | `/kategori` | Daftar kategori |
| POST | `/kategori` | Tambah kategori |
| GET | `/kategori/{id}` | Detail kategori |
| PUT | `/kategori/{id}` | Ubah kategori |
| DELETE | `/kategori/{id}` | Hapus kategori |
| GET | `/penulis` | Daftar penulis |
| POST | `/penulis` | Tambah penulis |
| GET | `/penulis/{id}` | Detail penulis |
| PUT | `/penulis/{id}` | Ubah penulis |
| DELETE | `/penulis/{id}` | Hapus penulis |
| GET | `/penerbit` | Daftar penerbit |
| POST | `/penerbit` | Tambah penerbit |
| GET | `/penerbit/{id}` | Detail penerbit |
| PUT | `/penerbit/{id}` | Ubah penerbit |
| DELETE | `/penerbit/{id}` | Hapus penerbit |
| GET | `/peminjaman` | Daftar peminjaman (admin) |
| POST | `/peminjaman` | Buat peminjaman (siswa) |
| GET | `/peminjaman/{id}` | Detail peminjaman |
| POST | `/peminjaman/{id}/perpanjang` | Perpanjang peminjaman |
| GET | `/pengembalian` | Daftar pengembalian |
| POST | `/pengembalian` | Buat pengembalian |
| GET | `/pengumuman` | Daftar pengumuman |
| GET | `/pengumuman/{id}` | Detail pengumuman |
| POST | `/pengumuman` | Tambah pengumuman |
| PUT | `/pengumuman/{id}` | Ubah pengumuman |
| DELETE | `/pengumuman/{id}` | Hapus pengumuman |
| GET | `/config` | Konfigurasi publik (nama perpustakaan, status layanan) |
| POST | `/login` | Login siswa (NIS + PIN) |
| GET | `/profile` | Profil siswa login |
| PUT | `/profile` | Ajukan perubahan profil (pending admin) |
| GET | `/profile/edits` | Riwayat permintaan profil |
| PUT | `/profile/pin` | Ganti PIN (langsung, butuh PIN lama) |

---

## Autentikasi

- **Admin (web):** session/cookie via `/admin/login`
- **Siswa (mobile/API):** Bearer token dari `POST /api/v1/login` (NIS + PIN)

---

## Struktur Database

| Tabel | Deskripsi |
|-------|-----------|
| `users` | Admin website |
| `kategori` | Kategori buku (Dewey) |
| `penulis` | Penulis buku |
| `penerbit` | Penerbit buku |
| `buku` | Katalog buku + stok |
| `anggota` | Siswa / anggota (NIS, PIN, token API) |
| `peminjaman` | Transaksi peminjaman |
| `pengembalian` | Transaksi pengembalian |
| `pengumuman` | Pengumuman perpustakaan |
| `pengaturan` | Key-value settings |
| `profile_edit_requests` | Permintaan edit profil siswa (pending admin) |

---

## Catatan Development

- Migration idempoten; `migrate:fresh --seed` aman di lokal.
- Stok buku: `stok` (total), `stok_tersedia` (tersedia dipinjam) — turunan dari logika peminjaman/pengembalian.
- Status peminjaman: `dipinjam`, `dikembalikan`.
- Enum, JSON, FK semua standar Laravel Blueprint — kompatibel MySQL/MariaDB.