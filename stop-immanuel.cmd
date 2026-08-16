@echo off
setlocal
cd /d "%~dp0"

set "DOCKER_EXE=%LOCALAPPDATA%\Programs\DockerDesktop\resources\bin\docker.exe"
if not exist "%DOCKER_EXE%" (
    for %%D in (docker.exe) do set "DOCKER_EXE=%%~$PATH:D"
)

if not exist "%DOCKER_EXE%" (
    echo Docker Desktop tidak ditemukan.
    pause
    exit /b 1
)

"%DOCKER_EXE%" compose stop
if errorlevel 1 (
    echo Aplikasi gagal dihentikan.
    pause
    exit /b 1
)

echo Immanuel Production sudah dihentikan. Database dan file tetap aman.
endlocal
