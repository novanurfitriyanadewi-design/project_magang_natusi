@echo off
cd /d "%~dp0"
if exist public\hot del /f /q public\hot
php artisan optimize:clear
if errorlevel 1 (
  echo.
  echo Gagal menjalankan artisan. Pastikan file BAT ini berada di root project Laravel.
  pause
  exit /b 1
)
echo.
echo Selesai. Tutup tab portal, buka lagi, lalu refresh sekali.
pause
