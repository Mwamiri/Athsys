@echo off
echo ========================================
echo   AthSys GitHub Upload Helper
echo ========================================
echo.

REM Check if in correct directory
if not exist "installer.php" (
    echo ERROR: Please run this from c:\wamp64\www\athsys directory
    echo.
    pause
    exit /b
)

echo Starting PowerShell script...
echo.

powershell -ExecutionPolicy Bypass -File ".\upload-to-github.ps1"

pause
