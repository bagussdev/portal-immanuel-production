#!/bin/sh
set -eu

mkdir -p storage/app/private storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

if [ "$(id -u)" = "0" ]; then
    chown -R www-data:www-data storage bootstrap/cache
fi

if [ -z "${APP_KEY:-}" ]; then
    if [ "${1:-}" = "php" ] && [ "${2:-}" = "artisan" ] && [ "${3:-}" = "key:generate" ]; then
        if [ "$(id -u)" = "0" ]; then
            exec su-exec www-data "$@"
        fi
        exec "$@"
    fi
    echo "APP_KEY belum diisi. Jalankan: docker compose run --rm app php artisan key:generate --show"
    exit 1
fi

if [ "$(id -u)" = "0" ]; then
    su-exec www-data php artisan package:discover --ansi >/dev/null
    su-exec www-data php artisan storage:link --quiet 2>/dev/null || true
else
    php artisan package:discover --ansi >/dev/null
    php artisan storage:link --quiet 2>/dev/null || true
fi

if [ "$(id -u)" = "0" ]; then
    # PHP-FPM harus memulai master process sebagai root agar dapat menyiapkan
    # log Docker dan menurunkan worker ke user www-data sesuai konfigurasi pool.
    # Perintah aplikasi lainnya tetap dijalankan sebagai user non-root.
    case "${1:-}" in
        php-fpm|php-fpm[0-9]*)
            exec "$@"
            ;;
    esac

    exec su-exec www-data "$@"
fi

exec "$@"
