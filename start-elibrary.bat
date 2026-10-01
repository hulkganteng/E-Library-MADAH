@echo off
setlocal enabledelayedexpansion
title E-Library Assaadah Launcher
color 0A

echo ===================================================
echo        MEMULAI APLIKASI E-LIBRARY ASSAADAH         
echo ===================================================
echo.

cd /d "%~dp0"

:: 1. Jalankan Service XAMPP / Laragon
echo [*] Memeriksa web server dan database...

if exist "C:\xampp\xampp_start.exe" (
    echo [+] Menjalankan Apache dan MySQL via XAMPP...
    call "C:\xampp\xampp_start.exe" >nul 2>&1
) else if exist "C:\laragon\laragon.exe" (
    echo [+] Menjalankan Apache dan MySQL via Laragon...
    start "" "C:\laragon\laragon.exe"
)

:: Tunggu 3 detik agar MySQL siap menerima koneksi
timeout /t 3 /nobreak >nul 2>&1

:: 2. Jalankan Migrasi Database
echo [*] Memeriksa migrasi database...
call php artisan migrate --force

:: 3. Buka Browser
echo [*] Membuka browser ke http://127.0.0.1:8000 ...
start http://127.0.0.1:8000

:: 4. Jalankan Laravel Serve
echo.
echo ===================================================
echo   Server E-Library aktif di http://127.0.0.1:8000
echo   Jangan tutup jendela ini selama aplikasi dipakai
echo ===================================================
echo.

call php artisan serve --port=8000 --host=127.0.0.1

echo.
echo Server dihentikan.
pause
