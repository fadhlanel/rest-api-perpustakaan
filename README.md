# REST API Perpustakaan Sekolah — Tugas A (Website)

Dokumen rancangan: `../PLAN-Langkah-1-REST-API.md`

REST API sederhana untuk digitalisasi sistem perpustakaan sekolah. API ini menjadi backend untuk aplikasi **Website (Admin)** dan aplikasi **Android (User)** pada Tugas B.

- **Framework:** Laravel 12 (PHP 8.3)
- **Database lokal:** MySQL/MariaDB 10.x (`db_perpustakaan`)
- **Database production:** MariaDB Railway (managed)
- **Deployment production:** Railway (`https://rest-api-perpustakaan-production.up.railway.app`)
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
Perintah ini membuat tabel (`kategori`, `penulis`, `penerbit`, `buku`, `peminjaman`, `pengembalian`, `anggota`, `pengumuman`, `users`, `profile_edit_requests`, `pengaturan`, `role`) dan mengisi data dummy: kategori, penulis, penerbit, buku, transaksi peminjaman, akun admin.

### 5. Jalankan Server
```bash
php artisan serve
```
API tersedia di `http://127.0.0.1:8000/api/v1`

### 6. Uji API
1. Buka **Postman**
2. **Import** → pilih `docs/Perpustakaan-API.postman_collection.json`
3. Jalankan request — variable `base_url` sudah diatur otomatis

### Deployment Production (Railway)

Panduan lengkap Railway deployment tersedia di [`DEPLOYMENT.md`](DEPLOYMENT.md).

**Production URL:** `https://rest-api-perpustakaan-production.up.railway.app`

Minimum environment production:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...                  # php artisan key:generate di server
APP_URL=https://rest-api-perpustakaan-production.up.railway.app
DB_CONNECTION=mysql
DB_HOST=<railway-db-host>
DB_PORT=3306
DB_DATABASE=<railway-db-name>
DB_USERNAME=<railway-db-user>
DB_PASSWORD=<railway-db-password>
LOG_CHANNEL=stderr
CACHE_STORE=redis
SESSION_DRIVER=cookie
SESSION_SECURE_COOKIE=true
QUEUE_CONNECTION=sync
ADMIN_EMAIL=<admin-email>
ADMIN_PASSWORD=<admin-password>
```

Health check production: `GET https://rest-api-perpustakaan-production.up.railway.app/api`

### 7. Jalankan Test Otomatis
```bash
php artisan test
```
Feature test mencakup: health check, pagination, searching, CRUD master data, validasi, stok management, dan reassignment buku lainnya.

---

## Daftar Endpoint

Base URL: `http://127.0.0.1:8000/api/v1` (lokal) atau `https://rest-api-perpustakaan-production.up.railway.app/api/v1` (produksi)

### Buku & Master Data

| Method | Endpoint | Fungsi |
|--------|---------|--------|
| GET | `/buku` | Daftar buku (pagination + filtering) |
| POST | `/buku` | Tambah buku (dengan upload sampul auto-crop 2:3) |
| GET | `/buku/{id}` | Detail buku |
| PUT | `/buku/{id}` | Ubah buku (update sampul atau data) |
| DELETE | `/buku/{id}` | Hapus buku |
| GET | `/buku/{id}/sampul` | Download sampul buku (image/jpeg) |
| POST | `/buku/import/preview` | Preview CSV import (validasi baris, deteksi kolom) |
| POST | `/buku/import/process` | Proses import CSV ke database |
| GET | `/buku/export` | Export buku ke CSV |
| GET | `/buku/export-pdf` | Export label buku ke PDF |
| GET | `/kategori` | Daftar kategori |
| POST | `/kategori` | Tambah kategori |
| GET | `/kategori/{id}` | Detail kategori |
| PUT | `/kategori/{id}` | Ubah kategori |
| DELETE | `/kategori/{id}` | Hapus kategori (buku lainnya jadi null FK, redirect reassign) |
| GET | `/penulis` | Daftar penulis |
| POST | `/penulis` | Tambah penulis |
| GET | `/penulis/{id}` | Detail penulis |
| PUT | `/penulis/{id}` | Ubah penulis |
| DELETE | `/penulis/{id}` | Hapus penulis (buku lainnya jadi null FK, redirect reassign) |
| GET | `/penerbit` | Daftar penerbit |
| POST | `/penerbit` | Tambah penerbit |
| GET | `/penerbit/{id}` | Detail penerbit |
| PUT | `/penerbit/{id}` | Ubah penerbit |
| DELETE | `/penerbit/{id}` | Hapus penerbit (buku lainnya jadi null FK, redirect reassign) |

### Reassign Buku Lainnya (Admin Panel Only)

| Method | Endpoint | Fungsi |
|--------|---------|--------|
| GET | `/admin/master/{type}/reassign` | Tampilkan buku-buku lainnya (null FK) untuk reassign |
| POST | `/admin/master/{type}/reassign/bulk` | Pindahkan semua buku lainnya ke master baru (bulk) |
| POST | `/admin/master/{type}/reassign/item` | Pindahkan buku individual ke master baru |

### Peminjaman & Pengembalian

| Method | Endpoint | Fungsi |
|--------|---------|--------|
| GET | `/peminjaman` | Daftar peminjaman (admin) |
| POST | `/peminjaman` | Buat peminjaman (siswa) |
| GET | `/peminjaman/{id}` | Detail peminjaman |
| POST | `/peminjaman/{id}/perpanjang` | Perpanjang peminjaman (+7 hari) |
| GET | `/pengembalian` | Daftar pengembalian |
| POST | `/pengembalian` | Buat pengembalian |

### Pengumuman & Notifikasi

| Method | Endpoint | Fungsi |
|--------|---------|--------|
| GET | `/pengumuman` | Daftar pengumuman |
| GET | `/pengumuman/{id}` | Detail pengumuman |
| POST | `/pengumuman` | Tambah pengumuman (admin) |
| PUT | `/pengumuman/{id}` | Ubah pengumuman (admin) |
| DELETE | `/pengumuman/{id}` | Hapus pengumuman (admin) |

### Konfigurasi & Profil

| Method | Endpoint | Fungsi |
|--------|---------|--------|
| GET | `/config` | Konfigurasi publik (nama sekolah, WhatsApp admin, durasi pinjam, limit buku) |
| POST | `/login` | Login siswa (NIS + PIN) → Bearer token |
| GET | `/profile` | Profil siswa login |
| PUT | `/profile` | Ajukan perubahan profil (pending admin) |
| GET | `/profile/edits` | Riwayat permintaan perubahan profil |
| PUT | `/profile/pin` | Ganti PIN (butuh PIN lama) |

---

## Fitur Khusus

### 1. Auto-Crop Sampul Buku (Portrait 2:3)
- Saat upload sampul (POST/PUT `/buku`), otomatis di-crop ke **300×450px** (ratio 2:3 portrait)
- Proses: `Intervention\Image\ImageManager` dengan mode `cover` (center crop)
- Format output: JPEG quality 80
- Endpoint unduh: `GET /buku/{id}/sampul`

### 2. CSV Import dengan Preview
- `POST /buku/import/preview`: Upload CSV, terima list baris valid + error per row
- Fitur: Auto-detect `tahun_terbit`, select row mana saja untuk di-import
- `POST /buku/import/process`: Process selected rows ke database
- Output: Laporan success/failed count

### 3. Master Data Delete & Reassign Buku Lainnya
**Alur saat menghapus Kategori/Penulis/Penerbit:**
1. Master data langsung dihapus dari DB
2. Kolom FK pada buku terkait jadi `NULL` (constraint `nullOnDelete`)
3. Jika ada buku lainnya (FK = NULL):
   - Controller kembali ke index master dengan banner warning "X Buku data {type}nya kosong, perbaiki datanya segera!"
   - Banner bisa diklik menuju `/admin/master/{type}/reassign`
   - Ikon lonceng di header menampilkan jumlah buku yang perlu diperbaiki; klik item notifikasi langsung ke halaman reassign
4. Admin memilih 2 opsi:
   - **Bulk:** Pilih 1 master baru, pindahkan semua buku lainnya
   - **Per-item:** Pindahkan tiap buku satu per satu
5. Setelah reassign selesai, redirect kembali ke index master

### 4. WhatsApp Admin Configuration
- Endpoint: `GET /config` mengembalikan nomor WhatsApp admin + template pesan
- Admin edit via panel: `/admin/pengaturan/whatsapp`
- Kolom: nomor WhatsApp (string), template pesan dengan placeholder `{nama}`, `{nis}`, `{kelas}`
- Android: Buka WhatsApp dengan `url_launcher` ke `https://wa.me/<nomor>?text=<encoded>`

---

## Autentikasi

- **Admin (web):** Session/cookie via `/admin/login`
- **Siswa (API):** Bearer token dari `POST /api/v1/login` (NIS + PIN)

---

## Struktur Database

| Tabel | Deskripsi |
|-------|-----------|
| `users` | Admin website (email, password) |
| `kategori` | Kategori buku (Dewey classification) |
| `penulis` | Penulis/author buku |
| `penerbit` | Penerbit buku |
| `buku` | Katalog buku + stok (kategori_id, penulis_id, penerbit_id nullable) |
| `anggota` | Siswa/member (NIS, PIN, token API, profile) |
| `peminjaman` | Transaksi peminjaman (buku, anggota, tgl pinjam, tgl kembali) |
| `pengembalian` | Transaksi pengembalian (peminjaman, tgl kembali, status) |
| `pengumuman` | Pengumuman perpustakaan (title, content, created_by) |
| `pengaturan` | Key-value settings (nama_sekolah, nama_perpustakaan, whatsapp_admin, template_wa, durasi_peminjaman, batas_buku, durasi_perpanjangan) |
| `profile_edit_requests` | Permintaan edit profil siswa (pending approval dari admin) |
`users.role` | Role akses admin website

### Nullable Foreign Keys (Null Safety)
```sql
ALTER TABLE buku 
  MODIFY kategori_id BIGINT UNSIGNED NULL,
  MODIFY penulis_id BIGINT UNSIGNED NULL,
  MODIFY penerbit_id BIGINT UNSIGNED NULL,
  ADD CONSTRAINT fk_kategori FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL,
  ADD CONSTRAINT fk_penulis FOREIGN KEY (penulis_id) REFERENCES penulis(id) ON DELETE SET NULL,
  ADD CONSTRAINT fk_penerbit FOREIGN KEY (penerbit_id) REFERENCES penerbit(id) ON DELETE SET NULL;
```

---

## Catatan Development

- Migration idempoten; `migrate:fresh --seed` aman di lokal.
- Stok buku: `stok` (total), `stok_tersedia` (tersedia dipinjam) — turunan dari logika peminjaman/pengembalian.
- Status peminjaman: `dipinjam`, `dikembalikan`.
- Foreign keys: `nullOnDelete` untuk kategori/penulis/penerbit (safe delete tanpa data loss).
- Sampul: Auto-crop 300×450 (2:3 portrait), disimpan di `storage/app/public/sampul/`.
- Enum, JSON, FK semua standar Laravel Blueprint — kompatibel MySQL/MariaDB/Railway.

---

## Testing

### Unit & Feature Tests
```bash
php artisan test
php artisan test --filter=SampulBukuTest
php artisan test --filter=MasterControllerTest
```

### Code Quality
```bash
vendor/bin/pint --dirty --format agent
php artisan test
```

---

## Troubleshooting

### Error: "SQLSTATE[HY000] [2002] No connection could be made"
- Pastikan MySQL running (Laragon Start All)
- Check `.env` DB_HOST, DB_PORT, DB_USERNAME

### Error: "Call to a member function on null"
- Terjadi saat kategori/penulis/penerbit dihapus tapi view tidak handle null
- Fix: Gunakan optional chaining `$item->kategori?->nama ?? 'Tanpa Kategori'`

### Error: "Route [admin.master.reassign] not found"
- Pastikan route prefix `name('admin.')` di web.php (group)
- Use: `route('admin.master.reassign', $type)` bukan `route('master.reassign', $type)`

### Sampul tidak muncul di Android
- Pastikan endpoint `/buku/{id}/sampul` accessible di production
- Check: Railway storage symlink (`storage/app/public` → `public/storage`)
- Fallback di Android: placeholder image jika URL 404
