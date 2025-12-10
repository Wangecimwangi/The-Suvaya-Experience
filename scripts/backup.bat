@echo off
REM Backup Script for Suvaya Bakery System (Windows)

echo ==========================================
echo Suvaya Bakery System - Complete Backup
echo ==========================================
echo.

REM Configuration
set BACKUP_DIR=complete-backups
set DATE_STR=%date:~-4%%date:~4,2%%date:~7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set DATE_STR=%DATE_STR: =0%
set BACKUP_NAME=suvaya_complete_%DATE_STR%

REM Create backup directory
if not exist %BACKUP_DIR% mkdir %BACKUP_DIR%
mkdir %BACKUP_DIR%\%BACKUP_NAME%

echo Creating complete backup: %BACKUP_NAME%
echo.

REM 1. Export database (using XAMPP MySQL)
echo 1. Exporting database...
"C:\xampp\mysql\bin\mysqldump.exe" -u root suvaya_db > %BACKUP_DIR%\%BACKUP_NAME%\database.sql
if %ERRORLEVEL%==0 (
    echo    Database exported
) else (
    echo    ERROR: Failed to export database
    echo    Make sure XAMPP MySQL is running
)

REM 2. Copy configuration files
echo 2. Backing up configuration...
if not exist %BACKUP_DIR%\%BACKUP_NAME%\config mkdir %BACKUP_DIR%\%BACKUP_NAME%\config
copy backend\config\database.php %BACKUP_DIR%\%BACKUP_NAME%\config\ >nul 2>&1
copy frontend\src\services\api.js %BACKUP_DIR%\%BACKUP_NAME%\config\ >nul 2>&1
echo    Configuration backed up

REM 3. Copy uploads
echo 3. Backing up uploads...
if exist backend\uploads (
    xcopy backend\uploads %BACKUP_DIR%\%BACKUP_NAME%\uploads\ /E /I /Q
    echo    Uploads backed up
) else (
    echo    No uploads directory found
)

REM 4. Copy images
echo 4. Backing up images...
if not exist %BACKUP_DIR%\%BACKUP_NAME%\images mkdir %BACKUP_DIR%\%BACKUP_NAME%\images
xcopy frontend\public\Cakes %BACKUP_DIR%\%BACKUP_NAME%\images\Cakes\ /E /I /Q >nul 2>&1
xcopy frontend\public\Menu %BACKUP_DIR%\%BACKUP_NAME%\images\Menu\ /E /I /Q >nul 2>&1
echo    Images backed up

REM 5. Create README
echo 5. Creating backup info...
(
echo Suvaya Bakery System - Backup
echo Created: %date% %time%
echo Backup Name: %BACKUP_NAME%
echo.
echo Contents:
echo - database.sql: Complete database dump
echo - config/: Configuration files
echo - uploads/: User uploaded files
echo - images/: Menu and product images
echo.
echo To restore:
echo 1. Import database in phpMyAdmin or:
echo    mysql -u root -p suvaya_db ^< database.sql
echo 2. Copy config files back to their locations
echo 3. Copy uploads and images back
echo 4. Update database credentials in config/database.php
echo 5. Update API URL in config/api.js
echo.
echo Admin credentials:
echo Email: admin@suvaya.com
echo Password: admin123
) > %BACKUP_DIR%\%BACKUP_NAME%\README.txt

REM 6. Create ZIP
echo 6. Compressing backup...
powershell Compress-Archive -Path %BACKUP_DIR%\%BACKUP_NAME% -DestinationPath %BACKUP_DIR%\%BACKUP_NAME%.zip -Force
if %ERRORLEVEL%==0 (
    rmdir /S /Q %BACKUP_DIR%\%BACKUP_NAME%
    echo    Backup compressed
)

echo.
echo ==========================================
echo Backup Complete!
echo ==========================================
echo.
echo Backup file: %BACKUP_DIR%\%BACKUP_NAME%.zip
echo.
echo To transfer to another machine:
echo 1. Copy the ZIP file to new machine
echo 2. Extract it
echo 3. Follow instructions in README.txt
echo.
pause
