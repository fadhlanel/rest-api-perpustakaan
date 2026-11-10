# Deployment Laravel API ke Google Cloud Run

Target: container stateless di Cloud Run, database PostgreSQL eksternal (Neon).

Ganti placeholder berikut sebelum menjalankan perintah:

| Placeholder | Nilai |
|---|---|
| `PROJECT_ID` | ID project Google Cloud Anda |
| `REGION` | `asia-southeast2` (Jakarta) |
| `SERVICE` | `perpustakaan-api` |
| `REPO` | `perpustakaan` (Artifact Registry) |

---

## 1. Persiapan project

```bash
gcloud auth login
gcloud config set project PROJECT_ID
gcloud config set run/region REGION
```

Aktifkan API:

```bash
gcloud services enable \
  run.googleapis.com \
  artifactregistry.googleapis.com \
  cloudbuild.googleapis.com \
  secretmanager.googleapis.com
```

## 2. Artifact Registry

```bash
gcloud artifacts repositories create REPO \
  --repository-format=docker \
  --location=REGION \
  --description="Image Laravel perpustakaan"
```

Konfigurasi Docker lokal agar bisa push:

```bash
gcloud auth configure-docker REGION-docker.pkg.dev
```

## 3. Build image

Dari root repository (lokasi `Dockerfile`):

```bash
gcloud builds submit \
  --tag REGION-docker.pkg.dev/PROJECT_ID/REPO/perpustakaan-api:latest
```

Build lokal alternatif:

```bash
docker build -t REGION-docker.pkg.dev/PROJECT_ID/REPO/perpustakaan-api:latest .
docker push REGION-docker.pkg.dev/PROJECT_ID/REPO/perpustakaan-api:latest
```

## 4. Secret Manager

Simpan nilai sensitif sebagai secret, jangan sebagai env biasa.

```bash
printf '%s' 'base64:APP_KEY_ANDA' | gcloud secrets create laravel-app-key --data-file=-
printf '%s' 'postgresql://USER:PASSWORD@HOST/neondb?sslmode=require' | gcloud secrets create laravel-db-url --data-file=-
```

Perbarui nilai di kemudian hari:

```bash
printf '%s' 'NILAI_BARU' | gcloud secrets versions add laravel-app-key --data-file=-
```

Beri akses ke service account Cloud Run (default compute service account):

```bash
PROJECT_NUMBER=$(gcloud projects describe PROJECT_ID --format='value(projectNumber)')
gcloud secrets add-iam-policy-binding laravel-app-key \
  --member="serviceAccount:${PROJECT_NUMBER}-compute@developer.gserviceaccount.com" \
  --role="roles/secretmanager.secretAccessor"
gcloud secrets add-iam-policy-binding laravel-db-url \
  --member="serviceAccount:${PROJECT_NUMBER}-compute@developer.gserviceaccount.com" \
  --role="roles/secretmanager.secretAccessor"
```

## 5. Database eksternal

Cloud Run dapat menghubungi PostgreSQL publik bertanda SSL tanpa VPC connector. Neon cocok karena sudah menyediakan `sslmode=require`.

Gunakan host **direct** (tanpa `-pooler`) untuk migration, dan boleh pooled untuk runtime biasa. Bila ragu, pakai direct untuk keduanya.

Alternatif di dalam GCP: Cloud SQL, tetapi memerlukan Serverless VPC Connector dan biaya instance. Tidak dipakai di proyek ini.

## 6. Deploy ke Cloud Run

```bash
gcloud run deploy SERVICE \
  --image REGION-docker.pkg.dev/PROJECT_ID/REPO/perpustakaan-api:latest \
  --region REGION \
  --platform managed \
  --allow-unauthenticated \
  --port 8080 \
  --memory 512Mi \
  --cpu 1 \
  --concurrency 8 \
  --min-instances 0 \
  --max-instances 4 \
  --set-env-vars "APP_ENV=production,APP_DEBUG=false,APP_NAME=Perpustakaan API,LOG_CHANNEL=stderr,LOG_LEVEL=warning,DB_CONNECTION=pgsql,DB_SSLMODE=require,SESSION_DRIVER=array,CACHE_STORE=array,QUEUE_CONNECTION=sync,FILESYSTEM_DISK=local" \
  --set-secrets "APP_KEY=laravel-app-key:latest,DB_URL=laravel-db-url:latest"
```

Service URL akan muncul di akhir perintah, formatnya:

```text
https://SERVICE-xxxxx-REGION_CODE.a.run.app
```

Setelah URL diketahui, set `APP_URL`:

```bash
gcloud run services update SERVICE \
  --region REGION \
  --update-env-vars "APP_URL=https://SERVICE-xxxxx-REGION_CODE.a.run.app"
```

Catatan:

- `--concurrency 8` wajar karena PHP built-in server dijalankan dengan `PHP_CLI_SERVER_WORKERS=4`.
- `--min-instances 0` hemat biaya, tetapi request pertama setelah idle lebih lambat (cold start).
- `--allow-unauthenticated` diperlukan agar Flutter dapat memanggil API tanpa token IAM.

## 7. Migration

Migration **tidak** dijalankan saat container start. Jalankan sekali sebagai job:

```bash
gcloud run jobs create SERVICE-migrate \
  --image REGION-docker.pkg.dev/PROJECT_ID/REPO/perpustakaan-api:latest \
  --region REGION \
  --command "php" \
  --args "artisan,migrate,--force" \
  --set-env-vars "APP_ENV=production,DB_CONNECTION=pgsql,DB_SSLMODE=require" \
  --set-secrets "APP_KEY=laravel-app-key:latest,DB_URL=laravel-db-url:latest" \
  --max-retries 0

gcloud run jobs execute SERVICE-migrate --region REGION --wait
```

Ulangi `jobs execute` setiap kali ada migration baru.

Jangan memakai `migrate:fresh`, `db:wipe`, atau `migrate:refresh` pada database production.

## 8. Health check

Cloud Run tidak mewajibkan health check, tetapi endpoint tersedia:

```bash
curl -i https://SERVICE-URL/health
```

Respons sehat:

```json
{"status":"ok","database":"ok"}
```

Bila `503`:

```json
{"status":"error","database":"unavailable"}
```

berarti `DB_URL`/kredensial/SSL bermasalah. Cek log:

```bash
gcloud run services logs read SERVICE --region REGION --limit 50
```

## 9. Uji endpoint API

```bash
curl -i https://SERVICE-URL/api/v1/buku
curl -i "https://SERVICE-URL/api/v1/buku?per_page=5&search=flutter"
curl -i https://SERVICE-URL/api/v1/kategori
curl -i https://SERVICE-URL/api/v1/peminjaman
curl -i -X POST https://SERVICE-URL/api/v1/peminjaman \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"buku_id":1,"nis":"2122.10.490","nama_peminjam":"Uji Coba"}'
```

Kalau data kosong, isi contoh data (opsional, jangan di production tanpa izin):

```bash
gcloud run jobs execute SERVICE-migrate --region REGION --wait   # migration dulu
```

Seeder dijalankan manual, bukan otomatis:

```bash
php artisan db:seed --force   # jalankan hanya ke database yang memang boleh diisi dummy
```

## 10. Domain dan HTTPS

Cloud Run sudah memberi HTTPS otomatis untuk `*.run.app`.

Domain kustom:

```bash
gcloud run domain-mappings create \
  --service SERVICE \
  --domain api.contoh.sch.id \
  --region REGION
```

Ikuti instruksi DNS (CNAME/records) yang dicetak perintah tersebut.

## 11. Hubungkan Flutter

Build dengan URL Cloud Run:

```bash
flutter build apk --release \
  --dart-define=API_BASE_URL=https://SERVICE-URL/api/v1
```

Development lokal tetap:

```bash
flutter run --dart-define=API_BASE_URL=http://192.168.1.10:8000/api/v1
```

Tanpa `--dart-define`, default otomatis: web `http://127.0.0.1:8000/api/v1`, emulator Android `http://10.0.2.2:8000/api/v1`.

CORS hanya relevan untuk Flutter Web. Bila dipakai, set:

```bash
gcloud run services update SERVICE --region REGION \
  --update-env-vars "CORS_ALLOWED_ORIGINS=https://domain-web-anda"
```

## 12. Troubleshooting

| Gejala | Penyebab umum |
|---|---|
| Container gagal start | `APP_KEY` kosong / secret tidak terbaca |
| 500 saat request | `APP_KEY` tidak valid, log di Cloud Logging |
| `/health` 503 | `DB_URL` salah, SSL Neon, atau Neon suspended |
| Request lambat pertama kali | cold start (`--min-instances 0`) |
| 404 pada semua route | `APP_URL`/prefix salah, cek `php artisan route:list` |
| Migration gagal | jalankan lewat job, cek host direct vs pooled |
| Data hilang setelah restart | aplikasi menulis ke filesystem lokal; gunakan object storage |

## 13. Biaya

- Cloud Run: bayar per request + waktu CPU; skala ke nol saat idle.
- Artifact Registry: biaya penyimpanan image kecil.
- Secret Manager: biaya per secret + akses.
- Neon: free tier cukup untuk proyek ini.

Pantau di **Billing → Budgets & alerts**.
