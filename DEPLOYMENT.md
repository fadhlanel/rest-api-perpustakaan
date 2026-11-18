# Deployment Laravel API ke Railway (Docker + MySQL)

Backend di-deploy dari repository GitHub ke Railway. Database menggunakan Railway MySQL plugin.

## 1. GitHub

- Commit source code tanpa `.env`.
- Pastikan secret tidak pernah masuk repository atau Git history.
- Push repository ke GitHub: `fadhlanel/rest-api-perpustakaan`, branch `master`.

## 2. Railway Project

1. Railway Dashboard → **New Project** → **Deploy from GitHub repo**
2. Pilih repo `fadhlanel/rest-api-perpustakaan`, branch `master`
3. Railway mendeteksi `Dockerfile` → build image Docker multi-stage
4. **Add Plugin** → **MySQL** (bukan PostgreSQL; migrasi pakai `enum()` dan `->after()`)
5. MySQL plugin otomatis inject environment variables: `${{MYSQLHOST}}`, `${{MYSQLPORT}}`, `${{MYSQLDATABASE}}`, `${{MYSQLUSER}}`, `${{MYSQLPASSWORD}}`

## 3. Environment Variables (Railway Service → Variables)

```env
APP_NAME="Perpustakaan API"
APP_ENV=production
APP_KEY=<generate lokal: php artisan key:generate --show>
APP_DEBUG=false
APP_URL=https://rest-api-perpustakaan-production.up.railway.app

DB_CONNECTION=mariadb
DB_HOST=${{MYSQLHOST}}
DB_PORT=${{MYSQLPORT}}
DB_DATABASE=${{MYSQLDATABASE}}
DB_USERNAME=${{MYSQLUSER}}
DB_PASSWORD=${{MYSQLPASSWORD}}

SESSION_DRIVER=cookie
SESSION_SECURE_COOKIE=true
CACHE_STORE=array
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
LOAN_DURATION_DAYS=3
LOAN_EXTENSION_DAYS=1
ADMIN_NAME=Administrator
ADMIN_EMAIL=<email admin>
ADMIN_PASSWORD=<password admin>
```

- `APP_KEY` **wajib diisi** (generate sekali, simpan di Railway Variables).
- `DB_CONNECTION=mariadb` menggunakan driver `pdo_mysql` (sudah di-install di Dockerfile).
- Railway inject `PORT` ke container; entrypoint pakai `php artisan serve --host=0.0.0.0 --port=$PORT`.

## 4. Deploy

- Push ke `master` → Railway auto-deploy (Docker build + entrypoint)
- Entrypoint (`docker-entrypoint.sh`) otomatis:
  1. `config:cache`, `route:cache`, `view:cache`
  2. `storage:link --force`
  3. `migrate --force` (retry 5x dengan jeda 10s)
  4. `db:seed --class=AdminUserSeeder --force` (jika `ADMIN_EMAIL` & `ADMIN_PASSWORD` diset)
  5. `php artisan serve --host=0.0.0.0 --port=$PORT`

## 5. Health check

Path: `/up` (Laravel built-in) atau `/api`

```bash
curl -i https://rest-api-perpustakaan-production.up.railway.app/up
curl -i https://rest-api-perpustakaan-production.up.railway.app/api
```

Respons sehat: `{"status":"ok","database":"ok"}`.

## 6. Uji endpoint API

```bash
curl -i https://rest-api-perpustakaan-production.up.railway.app/api/v1/buku
curl -i "https://rest-api-perpustakaan-production.up.railway.app/api/v1/buku?per_page=5&search=flutter"
curl -i https://rest-api-perpustakaan-production.up.railway.app/api/v1/kategori
curl -i https://rest-api-perpustakaan-production.up.railway.app/api/v1/config
```

## 7. Hubungkan Flutter (Android)

Default base URL Android (`lib/core/api_client.dart`) sudah fallback ke Railway:

```dart
static String get baseUrl {
  if (_envBaseUrl.isNotEmpty) return _envBaseUrl;
  return 'https://rest-api-perpustakaan-production.up.railway.app/api/v1';
}
```

Build tanpa parameter:

```powershell
flutter build apk --release
```

Override bila perlu (mis. staging):

```powershell
flutter build apk --release `
  --dart-define="API_BASE_URL=https://rest-api-perpustakaan-production.up.railway.app/api/v1"
```

## 8. Migrasi data dari Cloudways (opsional)

Schema dibuat otomatis oleh `migrate` saat deploy pertama. Impor **hanya data** agar tidak bentrok:

```bash
# Dari mesin yang punya akses DB Cloudways
mysqldump -h <cloudways-host> -u pkwgrvstke -p --no-create-info \
  --skip-add-locks --skip-disable-keys pkwgrvstke > data.sql

# Railway → MySQL plugin → Connect → buka tunnel, lalu:
mysql -h <railway-host> -P <port> -u <user> -p${{MYSQLPASSWORD}} \
  ${{MYSQLDATABASE}} < data.sql
```

Urutan: **deploy dulu** (migrate bikin tabel), **baru import data**. Data `pengaturan` (WhatsApp, nama sekolah) ikut terbawa.

## 9. Verifikasi pasca-deploy

1. Login admin di `https://rest-api-perpustakaan-production.up.railway.app/admin`
2. **Pengaturan Sistem** → pastikan `Nama Sekolah` = `SMK Negeri 6 Jember`, `Nomor WhatsApp` & `Template` terisi
3. Cek `GET /api/v1/config` → JSON mengandung `nama_sekolah`, `whatsapp_nomor`, `whatsapp_template`
4. Install APK baru di Android → login → Profil → tombol **Hubungi via WhatsApp** → buka WA dengan nomor & template dari panel admin

## 10. Troubleshooting

| Gejala | Penyebab umum |
|---|---|
| 500 di semua halaman | `APP_KEY` kosong / `vendor` belum diinstall (Docker build gagal) |
| 500 setelah deploy | `DB_CONNECTION` tidak `mariadb` / variabel MySQL tidak inject |
| `/api` 503 / DB error | kredensial MySQL salah / plugin MySQL belum ditambahkan |
| Login admin gagal | akses via `http://` tanpa SSL; pastikan HTTPS (Railway auto-HTTPS) |
| Migrasi stuck | `enum()` / `->after()` butuh MySQL; pastikan **bukan** PostgreSQL |
| Log error | Railway Dashboard → Deployments → klik deploy → **View Logs** |

## 11. Catatan domain

- Railway domain bawaan `*.up.railway.app` sudah HTTPS + HSTS.
- Untuk domain custom: **Settings → Domains** → Add Custom Domain → verifikasi DNS (CNAME ke `up.railway.app` atau A/AAAA ke IP Railway). SSL auto-provision via Let's Encrypt.

## 12. Biaya estimasi (Railway Hobby/Pro)

| Resource | Estimasi bulanan |
|---|---|
| Web service (512 MB RAM, shared CPU) | $5 |
| MySQL (1 GB storage) | $5 |
| **Total** | **~$10/bulan** |

Free trial $5 credit untuk project baru. Perlu kartu kredit untuk verifikasi.