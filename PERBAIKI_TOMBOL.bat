@echo off
cd /d "%~dp0"
if exist public\hot (
    del /f /q public\hot
    echo [OK] public\hot dihapus.
) else (
    echo [INFO] public\hot tidak ada.
)
php artisan optimize:clear
echo.
echo Selesai. Tutup browser lalu buka kembali portal, atau tekan Ctrl+F5.
pause
