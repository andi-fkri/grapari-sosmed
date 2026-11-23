#!/usr/bin/env bash
set -e

echo "===================================================="
echo " Deploying GraPARI Social Media Monitoring (Laravel)"
echo "===================================================="

# 1. Pull latest changes if already cloned
if [ -d ".git" ]; then
    echo "-> Menarik update terbaru dari Git..."
    git pull origin main || true
fi

# 2. Setup .env file if not exists
if [ ! -f ".env" ]; then
    echo "-> Membuat file .env dari .env.example..."
    cp .env.example .env
    sed -i 's/APP_ENV=local/APP_ENV=production/' .env
    sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env
fi

# 3. Ensure SQLite database file exists
if [ ! -f "database/database.sqlite" ]; then
    echo "-> Membuat file database SQLite (database/database.sqlite)..."
    touch database/database.sqlite
    IS_FIRST_INSTALL=1
else
    IS_FIRST_INSTALL=0
fi

# 4. Install PHP dependencies via Composer
echo "-> Menginstal dependensi Composer..."
composer install --no-dev --optimize-autoloader --no-interaction

# 5. Generate APP_KEY if not set
if ! grep -q "^APP_KEY=base64:" .env; then
    echo "-> Men-generate APP_KEY Laravel..."
    php artisan key:generate --force --no-interaction
fi

# 6. Run migrations (and seed 10 GraPARI branches on first install)
echo "-> Menjalankan migrasi database..."
php artisan migrate --force --no-interaction

if [ "$IS_FIRST_INSTALL" -eq 1 ]; then
    echo "-> Mengisi 10 daftar cabang GraPARI awal..."
    php artisan db:seed --force --no-interaction
fi

# 7. Optimize Laravel caches
echo "-> Mengoptimalkan cache konfigurasi & route..."
php artisan optimize

# 8. Fix permissions for web server (www-data)
echo "-> Mengatur permission folder storage, cache, dan database SQLite..."
chmod -R 775 storage bootstrap/cache database
chmod 664 database/database.sqlite
if id "www-data" &>/dev/null; then
    chown -R www-data:www-data storage bootstrap/cache database || true
fi

echo "===================================================="
echo " Deployment Selesai! Aplikasi GraPARI siap diakses."
echo "===================================================="
