@echo off
echo ========================================
echo KICH HOAT ZIP EXTENSION CHO PHP
echo ========================================
echo.

set PHP_INI=C:\xampp\php\php.ini

echo Dang backup file php.ini...
copy "%PHP_INI%" "%PHP_INI%.backup" >nul 2>&1

echo Dang kiem tra va kich hoat extension=zip...

powershell -Command "(Get-Content '%PHP_INI%') -replace ';extension=zip', 'extension=zip' | Set-Content '%PHP_INI%'"

echo.
echo ========================================
echo HOAN THANH!
echo ========================================
echo.
echo Da kich hoat extension zip trong php.ini
echo File backup: %PHP_INI%.backup
echo.
echo Vui long khoi dong lai Apache/server de ap dung thay doi.
echo.

pause
