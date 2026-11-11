# Deployment Laravel API ke Back4App Containers + Neon PostgreSQL

Backend di-deploy dari repository GitHub sebagai container Docker di Back4App. Database tetap di Neon (eksternal).

## 1. GitHub

- Commit source code tanpa `.env`.
- Pastikan secret tidak pernah masuk repository atau Git history.
- Push repository ke GitHub: `fadhlanel/rest-api-perpustakaan`, branch `master`.

## 2. Neon PostgreSQL

Buat project PostgreSQL di Neon. Salin connection string sebagai `DB_URL`:

```env
DB_CONNECTION=pgsql
DB_HOST=<host direct Neon>
DB_PORT=5432
DB_DATABASE=neondb
DB_USERNAME=<user>
DB_PASSWORD=<password>
DB_SSLMODE=require
```

Gunakan **host direct** (tanpa `-pooler`), terutama untuk migration. Pooler gagal pada DDL (`SQLSTATE[25P02]`).

Jangan menyimpan nilai tersebut di repository.

## 3. Back4App

1. Masuk ke Back4App → **Containers**.
2. Buat aplikasi dari repository GitHub:

   - Repository: `fadhlanel/rest-api-perpustakaan`
   - Branch: `master`
   - Lokasi `Dockerfile`: root repository
   - Runtime: Docker

3. **Deployments → Action → Redeploy** (transient image push error bisa diulang).
4. Domain otomatis Back4App, contoh:

   ```text
   https://restapiperpustakaan-cqtsulgh.back4.app
   ```

## 4. Environment variables (Back4App Settings → Environment)

| Variabel | Nilai |
|---|---|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | `base64:...` (rotate di Back4App) |
| `APP_URL` | `https://restapiperpustakaan-cqtsulgh.back4.app` |
| `APP_NAME` | `Perpustakaan API` |
| `LOG_CHANNEL` | `stderr` |
| `LOG_STACK` | `stderr` |
| `LOG_LEVEL` | `warning` |
| `DB_CONNECTION` | `pgsql` |
| `DB_URL` | `postgresql://user:pass@host-direct/neondb?sslmode=require` |
| `DB_SSLMODE` | `require` |
| `SESSION_DRIVER` | `array` |
| `SESSION_LIFETIME` | `120` |
| `CACHE_STORE` | `array` |
| `QUEUE_CONNECTION` | `sync` |
| `LOAN_DURATION_DAYS` | `3` |
| `LOAN_EXTENSION_DAYS` | `1` |
| `FILESYSTEM_DISK` | `local` |
| `CORS_ALLOWED_ORIGINS` | (kosong — Flutter mobile saja) |

Jangan masukkan `db:wipe` atau `DB_RESET_ON_DEPLOY` — fitur itu sudah dihapus dari Dockerfile agar tidak merusak data production.

## 5. Health check

Path: `/api`

```bash
curl -i https://restapiperpustakaan-zwnd33xl.back4.app/api
```

Respons sehat:

```json
{"status":"ok","database":"ok"}
```

Respons database bermasalah:

```json
{"status":"error","database":"unavailable"}
```

Periksa log di Back4App bila `503`.

## 6. Migration

Migration dijalankan otomatis di `docker-entrypoint.sh` saat container mulai, dengan retry 5× jika Neon free tier suspend saat idle. Data production **tidak** dihapus.

Jangan menjalankan `migrate:fresh`, `migrate:refresh`, atau `db:wipe` di production tanpa konfirmasi bahwa data disposable.

## 7. Uji endpoint API

```bash
curl -i https://restapiperpustakaan-cqtsulgh.back4.app/api/v1/buku
curl -i "https://restapiperpustakaan-cqtsulgh.back4.app/api/v1/buku?per_page=5&search=flutter"
curl -i https://restapiperpustakaan-cqtsulgh.back4.app/api/v1/kategori
```

## 8. Hubungkan Flutter

Build APK production:

```powershell
flutter build apk --release `
  --dart-define="API_BASE_URL=https://restapiperpustakaan-cqtsulgh.back4.app/api/v1"
```

Development lokal tetap:

```powershell
flutter run --dart-define="API_BASE_URL=http://10.0.2.2:8000/api/v1"
```

Tanpa `--dart-define`, default emulator Android: `http://10.0.2.2:8000/api/v1`, web: `http://127.0.0.1:8000/api/v1`.

## 9. Troubleshooting

| Gejala | Penyebab umum |
|---|---|
| Container gagal start | `APP_KEY` kosong / `DB_URL` salah / migration error |
| `/api` 503 | `DB_URL` salah, password Neon, host `-pooler` dipakai, atau Neon suspended |
| Migration gagal | `-pooler` pada DB_URL, kredensial salah, Neon suspended |
| Data hilang | `db:wipe` / `migrate:fresh` — jangan lakukan di production |
| Request lambat pertama kali | Free tier 256 MB, cold start |

## 10. Biaya

- Back4App free tier: 256 MB RAM, domain temporary.
- Neon free tier cukup untuk proyek ini.
