@echo off
COLOR 0A
TITLE POS & ERP System Server (Developed by: Mohammed Zahir)
echo ==========================================================
echo           POS and Inventory Management System (ERP)
echo ==========================================================
echo.

echo [1/2] Starting the system server...
:: Starting Laravel server minimized
start /MIN php artisan serve

:: Waiting for 3 seconds to ensure the server is up
timeout /t 3 /nobreak > NUL

echo [2/2] Opening the system interface...
:: Opening Google Chrome
start chrome "http://127.0.0.1:8000" --start-maximized

exit