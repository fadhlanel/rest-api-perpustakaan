# Deployment Laravel API ke Render + Neon

## 1. GitHub

- Commit source code tanpa `.env`.
- Pastikan secret tidak pernah masuk repository atau Git history.
- Push repository ke GitHub.

## 2. Neon PostgreSQL

Buat project PostgreSQL di Neon. Salin connection string sebagai `DB_URL`, atau isi variabel terpisah:

```env
DB_CONNECTION=pgsql
DB_HOST=...
DB_PORT=5432
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
DB_SSLMODE=require
```

Jangan menyimpan nilai tersebut di repository.

## 3. Render

Buat **Web Service** dari repository GitHub:

- Runtime: Docker
- Region: pilih yang dekat pengguna
- Health Check Path: `/health`
- Auto Deploy: sesuai kebutuhan

Environment variables minimum:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...
APP_URL=https://nama-service.onrender.com
LOG_CHANNEL=stderr
LOG_STACK=stderr
LOG_LEVEL=warning
DB_CONNECTION=pgsql
DB_URL=postgresql://...
DB_SSLMODE=require
SESSION_DRIVER=array
CACHE_STORE=array
QUEUE_CONNECTION=sync
CORS_ALLOWED_ORIGINS=https://domain-flutter-web.example
```

Generate key secara lokal dengan `php artisan key:generate --show`, lalu masukkan hasilnya ke Render. Jangan menjalankan key generation setiap container start.

## 4. Migration

Container menjalankan migration otomatis saat start:

```bash
php artisan migrate --force
```

Untuk database baru, pastikan backup dan verifikasi environment sebelum deployment. Tidak memakai `migrate:fresh` atau `migrate:reset` pada production.

## 5. Health check

Uji:

```bash
curl https://nama-service.onrender.com/health
```

Respons sehat:

```json
{"status":"ok","database":"ok"}
```

## 6. Flutter

Build Flutter dengan URL production:

```bash
flutter build apk --dart-define=BASE_URL=https://nama-service.onrender.com/api/v1
```

Untuk Flutter Web, isi `CORS_ALLOWED_ORIGINS` dengan origin frontend yang tepat. Untuk Android/iOS, CORS browser tidak berlaku.

## 7. Troubleshooting

- **503 health check**: cek `DB_URL`, SSL Neon, serta log Render.
- **Migration gagal**: cek koneksi, privilege database, dan migration terakhir; jangan menghapus tabel.
- **500 saat boot**: cek `APP_KEY`, ekstensi PHP, dan log Render.
- **CORS error**: pastikan origin frontend sama persis, tanpa path tambahan.
- **Data upload hilang**: filesystem Render ephemeral; gunakan object storage.
- **Container gagal listen**: Render memakai `PORT`; Dockerfile sudah memakai `${PORT:-10000}`.
