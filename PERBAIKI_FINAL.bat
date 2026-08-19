@echo off
if exist public\hot del /F /Q public\hot
php artisan optimize:clear
echo.
echo Selesai. Jangan jalankan npm run build untuk perbaikan ini.
echo Tutup browser tab portal lalu buka kembali.
pause
