@echo off
title Auto Updater - Aplikasi Keuangan
color 0A
echo ====================================================================
echo             AUTO-UPDATER: APLIKASI KEUANGAN SEKOLAH
echo ====================================================================
echo.
echo Script ini akan menarik kode terbaru dari GitHub, menyelaraskan database,
echo serta membangun ulang aset visual aplikasi secara otomatis.
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

:: Step 3: NPM Install
echo [3/5] Menyelaraskan dependensi Javascript (npm install)...
call npm install
if %errorlevel% neq 0 (
    color 0C
    echo [ERROR] Gagal mengupdate dependensi Javascript.
    goto end
)
echo [OK] Dependensi Javascript berhasil diselaraskan!
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

:: Step 4.5: Storage Link
echo [*] Menghubungkan penyimpanan media (storage:link)...
call php artisan storage:link
echo.

:: Step 5: Build Assets
echo [5/5] Mengompilasi ulang tampilan visual (npm run build)...
call npm run build
if %errorlevel% neq 0 (
    color 0C
    echo [ERROR] Gagal melakukan build assets.
    goto end
)
echo.
echo ====================================================================
echo         CONGRATULATIONS! APLIKASI KINI SUDAH 100% TERUPDATE!
echo ====================================================================
echo Aplikasi Anda siap digunakan kembali dengan fitur-fitur terbaru.
echo.

:end
pause
