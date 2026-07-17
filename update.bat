@echo off
title Auto Updater - Aplikasi Keuangan
color 0A
echo ====================================================================
echo             AUTO-UPDATER: APLIKASI SLIP GAJI SEKOLAH
echo ====================================================================
echo.
echo Script ini akan menarik kode terbaru dari GitHub dan menyelaraskan database.
echo.
echo --------------------------------------------------------------------
echo.

:: Step 1: Git Pull
echo [1/5] Menarik kode terbaru dari GitHub (git fetch ^& reset)...
git fetch origin main
git reset --hard origin/main
if %errorlevel% neq 0 (
    color 0C
    echo [ERROR] Gagal menarik kode dari GitHub. Pastikan Git terinstall dan internet aktif.
    goto end
)
echo [OK] Kode berhasil diperbarui!
echo.

:: Step 2: Composer Install
echo [2/5] Menyelaraskan dependensi PHP (composer install)...
call composer install --no-interaction --prefer-dist --optimize-autoloader
if %errorlevel% neq 0 (
    color 0C
    echo [ERROR] Gagal mengupdate dependensi PHP.
    goto end
)
echo [OK] Dependensi PHP berhasil diselaraskan!
echo.

:: Step 3: NPM Install & Build
echo [3/5] Membangun asset frontend (npm install ^& npm run build)...
call npm install
if %errorlevel% neq 0 (
    color 0C
    echo [ERROR] Gagal menjalankan npm install.
    goto end
)
call npm run build
if %errorlevel% neq 0 (
    color 0C
    echo [ERROR] Gagal menjalankan npm run build.
    goto end
)
echo [OK] Asset frontend berhasil dibangun!
echo.

:: Step 4: Run Migrations
echo [4/5] Menjalankan migrasi perubahan database (migrate)...
call php artisan migrate --force
if %errorlevel% neq 0 (
    color 0C
    echo [ERROR] Gagal menjalankan migrasi database.
    goto end
)
echo [OK] Struktur database berhasil diperbarui!
echo.

:: Storage Link
echo [*] Menghubungkan penyimpanan media (storage:link)...
call php artisan storage:link
echo.

:: Step 5: Clear All Cache
echo [5/5] Membersihkan cache Laravel (config, route, view, app)...
call php artisan optimize:clear
call php artisan config:clear
call php artisan route:clear
call php artisan view:clear
call php artisan cache:clear
echo [OK] Cache berhasil dibersihkan!
echo.

echo ====================================================================
echo         CONGRATULATIONS! APLIKASI KINI SUDAH 100% TERUPDATE!
echo ====================================================================
echo Aplikasi Anda siap digunakan kembali dengan fitur-fitur terbaru.
echo CATATAN: Tekan CTRL+F5 di browser untuk melihat tampilan terbaru!
echo.

:end
pause
