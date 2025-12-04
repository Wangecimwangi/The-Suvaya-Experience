@echo off
echo ========================================
echo Starting Suvaya Experience Backend Server
echo ========================================
echo.

REM Check if PHP is installed
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: PHP is not installed or not in PATH
    echo.
    echo Please install PHP first:
    echo 1. Download from https://windows.php.net/download/
    echo 2. Or install XAMPP from https://www.apachefriends.org/
    echo.
    pause
    exit /b 1
)

echo PHP is installed
php --version
echo.

echo Starting PHP server on http://localhost:8000
echo Press Ctrl+C to stop the server
echo.
echo Server logs will appear below:
echo ========================================
echo.

REM Start PHP server from backend directory
cd backend
php -S localhost:8000

pause
