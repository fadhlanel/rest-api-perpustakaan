#!/bin/sh
set -e

# Neon pooler tidak cocok untuk perintah DDL (migrate) dan bisa memicu
# SQLSTATE[25P02]. Paksa koneksi direct dengan membuang "-pooler".
if [ -n "$DB_URL" ]; then
  DB_URL=$(printf '%s' "$DB_URL" | sed 's/-pooler\././g')
  export DB_URL
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Simbolik link public/storage -> storage/app/public wajib ada agar
# URL gambar sampul (asset('storage/...')) tidak mengembalikan 404.
php artisan storage:link --force || true

# Neon free tier bisa suspend saat idle; beri beberapa kesempatan sebelum gagal.
attempt=1
until php artisan migrate --force; do
  if [ "$attempt" -ge 5 ]; then
    echo "Migration gagal setelah $attempt percobaan." >&2
    exit 1
  fi
  attempt=$((attempt + 1))
  echo "Migration gagal, mencoba lagi ($attempt/5)..." >&2
  sleep 10
done

if [ -n "$ADMIN_EMAIL" ] && [ -n "$ADMIN_PASSWORD" ]; then
  php artisan db:seed --class=AdminUserSeeder --force
fi

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
