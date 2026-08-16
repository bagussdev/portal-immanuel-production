@echo off
setlocal
cd /d "%~dp0"

set "DOCKER_EXE=%LOCALAPPDATA%\Programs\DockerDesktop\resources\bin\docker.exe"
set "DOCKER_DESKTOP=%LOCALAPPDATA%\Programs\DockerDesktop\Docker Desktop.exe"

if not exist "%DOCKER_EXE%" (
    for %%D in (docker.exe) do set "DOCKER_EXE=%%~$PATH:D"
)

if not exist "%DOCKER_EXE%" (
    echo Docker Desktop tidak ditemukan.
    echo Instal atau perbaiki Docker Desktop terlebih dahulu.
    pause
    exit /b 1
)

set /a ATTEMPTS=0
:WAIT_DOCKER
"%DOCKER_EXE%" info >nul 2>&1
if not errorlevel 1 goto DOCKER_READY

if %ATTEMPTS% EQU 0 (
    echo Menyalakan Docker Desktop...
    if exist "%DOCKER_DESKTOP%" start "" "%DOCKER_DESKTOP%"
)

set /a ATTEMPTS+=1
if %ATTEMPTS% GEQ 60 (
    echo Docker belum siap setelah dua menit.
    echo Buka Docker Desktop, tunggu sampai aktif, lalu jalankan file ini lagi.
    pause
    exit /b 1
)

ping 127.0.0.1 -n 3 >nul
goto WAIT_DOCKER

:DOCKER_READY
echo Menjalankan Portal Immanuel Production dari %CD%...
"%DOCKER_EXE%" compose up -d --build
if errorlevel 1 (
    echo Aplikasi gagal dijalankan. Periksa Docker Desktop lalu coba lagi.
    pause
    exit /b 1
)

echo Menjalankan pembaruan database...
"%DOCKER_EXE%" compose exec -T app php artisan migrate --force
if errorlevel 1 (
    echo Container aktif, tetapi pembaruan database gagal.
    echo Jangan lanjut menginput data sebelum pesan ini diperiksa.
    pause
    exit /b 1
)

echo Menyinkronkan Jadwal Event...
"%DOCKER_EXE%" compose exec -T app php artisan field-jobs:sync
if errorlevel 1 (
    echo Database sudah diperbarui, tetapi Jadwal Event gagal disinkronkan.
    pause
    exit /b 1
)

echo Mengamankan lampiran pengeluaran lama...
"%DOCKER_EXE%" compose exec -T app php artisan expenses:secure-attachments
if errorlevel 1 (
    echo Database sudah diperbarui, tetapi pengamanan lampiran gagal.
    pause
    exit /b 1
)

echo Membersihkan cache aplikasi...
"%DOCKER_EXE%" compose exec -T app php artisan optimize:clear
if errorlevel 1 (
    echo Container aktif, tetapi cache aplikasi gagal dibersihkan.
    echo Jalankan file ini sekali lagi setelah semua container siap.
    pause
    exit /b 1
)

echo.
echo Portal Immanuel Production aktif di http://127.0.0.1:8000
if /I not "%~1"=="--no-open" start "" "http://127.0.0.1:8000"
endlocal
