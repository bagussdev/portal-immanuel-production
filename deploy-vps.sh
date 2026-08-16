#!/usr/bin/env sh
set -eu

cd "$(dirname "$0")"

if ! command -v docker >/dev/null 2>&1; then
    echo "Docker belum terpasang."
    exit 1
fi

if ! docker compose version >/dev/null 2>&1; then
    echo "Docker Compose plugin belum tersedia."
    exit 1
fi

if [ ! -f .env ]; then
    echo "File .env belum tersedia. Salin .env.example lalu isi konfigurasi production."
    exit 1
fi

if ! grep -Eq '^APP_ENV=production$' .env || ! grep -Eq '^APP_DEBUG=false$' .env; then
    echo "APP_ENV harus production dan APP_DEBUG harus false."
    exit 1
fi

if ! grep -Eq '^APP_URL=https://' .env; then
    echo "APP_URL harus menggunakan HTTPS."
    exit 1
fi

if ! grep -Eq '^APP_KEY=base64:.+' .env; then
    echo "APP_KEY production belum valid."
    exit 1
fi

if ! grep -Eq '^SESSION_ENCRYPT=true$' .env || ! grep -Eq '^SESSION_SECURE_COOKIE=true$' .env; then
    echo "SESSION_ENCRYPT dan SESSION_SECURE_COOKIE harus true."
    exit 1
fi

if grep -Eq 'change_this_|portal\.example\.com' .env; then
    echo "Masih ada nilai contoh di .env. Ganti seluruh password dan domain sebelum deploy."
    exit 1
fi

docker compose config --quiet
docker compose build --pull
docker compose up -d db

attempt=0
until docker compose exec -T db sh -c 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" mysqladmin ping -h 127.0.0.1 -uroot --silent' >/dev/null 2>&1; do
    attempt=$((attempt + 1))
    if [ "$attempt" -ge 60 ]; then
        echo "Database belum siap setelah dua menit."
        exit 1
    fi
    sleep 2
done

mkdir -p backups
backup_file="backups/predeploy-$(date +%Y%m%d-%H%M%S).sql"
docker compose exec -T db sh -c 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" mysqldump --single-transaction --routines --triggers -uroot "$MYSQL_DATABASE"' > "$backup_file"
echo "Backup database: $backup_file"

docker compose up -d app web queue scheduler phpmyadmin
docker compose exec -T app php artisan migrate --force
docker compose exec -T app php artisan field-jobs:sync
docker compose exec -T app php artisan expenses:secure-attachments
docker compose exec -T app php artisan optimize
docker compose restart queue scheduler

attempt=0
until docker compose exec -T web wget -qO- http://127.0.0.1/up >/dev/null 2>&1; do
    attempt=$((attempt + 1))
    if [ "$attempt" -ge 30 ]; then
        echo "Health check aplikasi gagal. Periksa: docker compose logs --tail=200"
        exit 1
    fi
    sleep 2
done

echo "Deploy selesai. Aplikasi sehat di port lokal 127.0.0.1:8000."
