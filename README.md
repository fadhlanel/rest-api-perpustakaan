# REST API Perpustakaan Sekolah — Tugas A (Website)

Dokumen rancangan: `../PLAN-Langkah-1-REST-API.md`

REST API sederhana untuk digitalisasi sistem perpustakaan sekolah. API ini menjadi backend untuk aplikasi **Website (Admin)** dan nantinya dikonsumsi oleh aplikasi **Android (User)** pada Tugas B.

- **Framework:** Laravel 13 (PHP 8.3)
- **Database lokal:** MySQL 8.4 (`db_perpustakaan`)
- **Database production:** PostgreSQL Neon
- **Deployment production:** Docker + Render
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
Buat database MySQL dengan nama `db_perpustakaan`, atau jalankan SQL berikut di HeidiSQL (Laragon akan membukanya bila database sudah dijalankan):

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
Perintah ini membuat 6 tabel (`kategori`, `penulis`, `penerbit`, `buku`, `peminjaman`, `pengembalian`) dan mengisi data dummy: 5 kategori, 8 penulis, 5 penerbit, 15 buku, 3 transaksi peminjaman.

### 5. Jalankan Server
```bash
php artisan serve
```
API tersedia di `http://127.0.0.1:8000/api/v1`

### 6. Uji API
1. Buka **Postman**
2. **Import** → pilih `docs/Perpustakaan-API.postman_collection.json`
3. Jalankan request — variable `base_url` sudah diatur otomatis

### Deployment Production

Panduan lengkap Render + Neon + Docker tersedia di [`DEPLOYMENT.md`](DEPLOYMENT.md).

Minimum environment production:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...
APP_URL=https://nama-service.onrender.com
DB_CONNECTION=pgsql
DB_URL=postgresql://...
DB_SSLMODE=require
LOG_CHANNEL=stderr
CACHE_STORE=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
```

Health check production: `GET https://restapiperpustakaan-oyzv47sh.back4.app/api`.

## 7. Jalankan Test Otomatis
```bash
php artisan test tests/Feature/ApiPerpustakaanTest.php
```
Feature test mencakup: health check, pagination, searching, CRUD master proteksi hapus, validasi 422, pengurangan stok saat peminjaman, dan pengembalian stok saat pengembalian.

---

## Daftar Endpoint

Base URL: `http://127.0.0.1:8000/api/v1`

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
| GET | `/peminjaman` | Daftar peminjaman (filter status) |
| POST | `/peminjaman` | Catat peminjaman buku |
| GET | `/peminjaman/{id}` | Detail peminjaman |
| GET | `/pengembalian` | Daftar pengembalian |
| POST | `/pengembalian` | Proses pengembalian buku |

## Parameter Wajib PDF

| Fitur | Implementasi |
|-------|--------------|
| **Pagination** | `?per_page=10&page=1` (maksimum 100 per halaman) dengan meta `current_page`, `total`, `last_page`, dst. |
| **Searching** | `?search=...` — mencari judul/ISBN/nama penulis pada buku; nama/NIS pada peminjaman; nama pada kategori, penulis, penerbit |
| **Validasi Input** | FormRequest terpisah per operasi; gagal validasi mengembalikan `422` berisi detail error per field |

## Format Respons Standar

```json
{
  "success": true,
  "message": "Daftar buku perpustakaan.",
  "data": [ ... ],
  "meta": { "current_page": 1, "per_page": 10, "total": 15, "last_page": 2 }
}
```

Gagal:

```json
{
  "success": false,
  "message": "Data tidak ditemukan."
}
```

## Struktur Database (6 Tabel)

```
kategori (id, nama)
penulis (id, nama, biografi)
penerbit (id, nama, alamat)
buku (id, judul, deskripsi, tahun_terbit, isbn, sampul, stok, stok_tersedia,
      kategori_id FK -> kategori, penulis_id FK -> penulis, penerbit_id FK -> penerbit)
peminjaman (id, nama_peminjam, nis, buku_id FK -> buku,
            tanggal_pinjam, tanggal_jatuh_tempo, status [dipinjam|dikembalikan])
pengembalian (id, peminjaman_id FK -> peminjaman, tanggal_kembali)
```

## Aturan Bisnis Transaksi

1. **Peminjaman:** stok tersedia dicek & dikurangi 1 di dalam database transaction (`lockForUpdate`); `tanggal_jatuh_tempo` = tanggal pinjam + 7 hari; stok habis ditolak dengan pesan jelas.
2. **Pengembalian:** hanya peminjaman berstatus `dipinjam` yang bisa diproses (sekali saja); stok tersedia ditambah kembali 1.
3. **Integritas hapus:** kategori/penulis/penerbit yang masih dipakai buku tidak boleh dihapus; buku dengan riwayat peminjaman tidak boleh dihapus.
4. **Update stok:** mengubah `stok` otomatis menyesuaikan `stok_tersedia` selisihnya dan ditolak bila membuat stok tersedia negatif.

## Struktur Folder Penting

```
rest-api-perpustakaan/
├── app/Http/Controllers/Api/   # Buku, Kategori, Penulis, Penerbit, Peminjaman, Pengembalian
├── app/Http/Requests/          # FormRequest validasi (10 kelas)
├── app/Http/Resources/         # API Resource (BukuResource, dll.)
├── app/Models/                 # Model + relasi Eloquent
├── database/migrations/        # Migrasi 6 tabel
├── database/seeders/           # Seeder data dummy
├── docs/                       # Koleksi Postman (.json)
├── routes/api.php              # Endpoint v1
└── tests/Feature/              # Feature test API
```

## Pemanfaatan AI (Tugas E)

Bagian project yang dikerjakan dengan bantuan AI (asisten AI dalam IDE):

| Bagian | Bantuan AI | Contoh prompt |
|--------|-----------------------|----------------------------------|
| Perancangan rencana & skema database | Menyusun rencana kerja dari ketentuan PDF, menentukan kolom & relasi tabel | "buatkan plannya ini sesuai seperti file pdf yang saya kirim tugasnya, jika ada yang salah pada pendeskripsiannya maka rubahlah" |
| Membuat migration, model, controller, FormRequest, seeder | Membuat file-file scaffolding dan isi kode PHP sesuai konvensi Laravel | "tulis migration 6 tabel perpustakaan dengan foreign key dan seeder data dummy" |
| Perbaikan bug | Menemukan ketidakcocokan nama tabel (kategoris vs kategori) dan singularize rute `{penuli}` | "no such table: kategoris — perbaiki rule exists di semua FormRequest" |
| Testing | Membuat 6 feature test PHPUnit otomatis (51 assertion lulus) | "buat feature test untuk pagination, searching, validasi, peminjaman, dan pengembalian" |
| Dokumentasi | Menyusun README, dokumentasi cara menjalankan, dan koleksi Postman | "buat README detail dan file koleksi Postman untuk semua endpoint" |

Bagian yang tetap dikerjakan/ditinjau secara manual: keputusan struktur data (dwi-kolom `stok` vs `stok_tersedia`), aturan bisnis transaksi, pengaturan nama tabela Indonesia, dan peninjauan setiap file yang dibuat AI.

## Catatan Tugas Terkait (Test Case PDF)

- **Tugas B (Flutter):** API ini dirancang konsisten (`success`/`message`/`data`/`meta`) agar mudah dikonsumsi aplikasi Android.
- **Tugas D (GitHub):** repo berisi source code + README + dokumentasi cara menjalankan ini. Lengkapi screenshot setelah selesai diuji.
- **Batas pengumpulan:** 21 November 2026, 12.00 WIB.
