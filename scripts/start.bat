@echo off
REM Start Script for Suvaya Bakery System (Windows)
REM This script starts both backend and frontend servers

echo ==========================================
echo Starting Suvaya Bakery System
echo ==========================================
echo.

REM Check if XAMPP is running
tasklist /FI "IMAGENAME eq httpd.exe" 2>NUL | find /I /N "httpd.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo XAMPP Apache is running
    echo Backend will be available at: http://localhost/suvaya/backend
) else (
    echo Starting PHP built-in server...
    start "Suvaya Backend" /MIN cmd /c "cd backend && php -S localhost:8000"
    echo Backend started on http://localhost:8000
)

echo.
echo Starting Vue frontend...
start "Suvaya Frontend" cmd /c "cd frontend && npm run dev"

timeout /t 3 >nul

echo.
echo ==========================================
echo Servers Started!
echo ==========================================
echo.
echo Frontend: http://localhost:5173
echo Backend:  http://localhost:8000
echo Admin:    http://localhost:5173/admin
echo.
echo Admin credentials:
echo   Email:    admin@suvaya.com
echo   Password: admin123
echo.
echo Press any key to exit this window...
pause >nul
