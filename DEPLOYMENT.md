# Deployment Laravel API ke Cloudways (PHP + MariaDB)

Backend di-deploy dari repository GitHub ke aplikasi Laravel di Cloudways. Database menggunakan MariaDB bawaan Cloudways (DB `pkwgrvstke`).

## 1. GitHub

- Commit source code tanpa `.env`.
- Pastikan secret tidak pernah masuk repository atau Git history.
- Push repository ke GitHub: `fadhlanel/rest-api-perpustakaan`, branch `master`.

## 2. Database MariaDB (Cloudways)

Kredensial diambil dari panel Cloudways → **Access Details**:

```env
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pkwgrvstke
DB_USERNAME=pkwgrvstke
DB_PASSWORD=<dari panel Cloudways>
```

App dan DB berada di server yang sama, jadi `DB_HOST=127.0.0.1`. Jangan menyimpan password di repository.

## 3. Deploy via Git (Cloudways)

1. Cloudways → aplikasi → **Deployment via GIT** → **Generate SSH Keys**.
2. Tambahkan public key ke GitHub → repo → **Settings → Deploy keys**.
3. Masukkan repo `git@github.com:fadhlanel/rest-api-perpustakaan.git`, branch `master` → **Deploy**.
4. Application URL otomatis Cloudways:

   ```text
   https://phplaravel-1688054-6782384.cloudwaysapps.com
   ```

## 4. Setup server (SSH)

```bash
cd applications/<app>/public_html
cp .env.example .env        # jika .env belum ada
nano .env                   # isi DB_PASSWORD, ADMIN_EMAIL, ADMIN_PASSWORD
composer install --no-dev --optimize-autoloader
php artisan key:generate
chmod -R 775 storage bootstrap/cache
```

## 5. Environment (isi di .env server)

| Variabel | Nilai |
|---|---|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | `base64:...` (generate di server) |
| `APP_URL` | `https://phplaravel-1688054-6782384.cloudwaysapps.com` |
| `APP_NAME` | `Perpustakaan API` |
| `DB_CONNECTION` | `mariadb` |
| `DB_HOST` | `127.0.0.1` |
| `DB_PORT` | `3306` |
| `DB_DATABASE` | `pkwgrvstke` |
| `DB_USERNAME` | `pkwgrvstke` |
| `DB_PASSWORD` | dari panel Cloudways |
| `SESSION_DRIVER` | `cookie` |
| `SESSION_SECURE_COOKIE` | `true` (HTTPS temp URL sudah aktif) |
| `CACHE_STORE` | `array` |
| `QUEUE_CONNECTION` | `sync` |
| `LOAN_DURATION_DAYS` | `3` |
| `LOAN_EXTENSION_DAYS` | `1` |
| `ADMIN_EMAIL` / `ADMIN_PASSWORD` | untuk AdminUserSeeder |

## 6. Migration & seed

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

`db:seed` membuat data awal (kategori, penulis, penerbit, buku, peminjaman) dan akun admin bila `ADMIN_EMAIL`/`ADMIN_PASSWORD` tersedia. Data production **tidak** dihapus oleh migration.

Jangan menjalankan `migrate:fresh`, `migrate:refresh`, atau `db:wipe` di production tanpa konfirmasi data disposable.

## 7. Health check

Path: `/api`

```bash
curl -i https://phplaravel-1688054-6782384.cloudwaysapps.com/api
```

Respons sehat: `{"status":"ok","database":"ok"}`.

## 8. Uji endpoint API

```bash
curl -i https://phplaravel-1688054-6782384.cloudwaysapps.com/api/v1/buku
curl -i "https://phplaravel-1688054-6782384.cloudwaysapps.com/api/v1/buku?per_page=5&search=flutter"
curl -i https://phplaravel-1688054-6782384.cloudwaysapps.com/api/v1/kategori
```

## 9. Hubungkan Flutter

Default base URL Android sudah mengarah ke Cloudways. Build tanpa parameter:

```powershell
flutter build apk --release
```

Override bila perlu:

```powershell
flutter build apk --release `
  --dart-define="API_BASE_URL=https://phplaravel-1688054-6782384.cloudwaysapps.com/api/v1"
```

## 10. Troubleshooting

| Gejala | Penyebab umum |
|---|---|
| 500 di semua halaman | `.env` belum ada / `APP_KEY` kosong / `vendor` belum diinstall |
| 500 setelah deploy | `.env` masih `pgsql` atau `DB_URL` Neon lama |
| `/api` 503 | kredensial MariaDB salah |
| Login admin gagal | akses via `http://` tanpa SSL; pastikan HTTPS |
| Log error | `tail -n 50 storage/logs/laravel.log` |

## 11. Catatan domain

- Temp URL `*.cloudwaysapps.com` sudah HTTPS bawaan — tidak perlu Let's Encrypt.
- Untuk domain sendiri: **Domain Management** → tambahkan domain → **SSL Certificate → Let's Encrypt** isi domain tersebut.
