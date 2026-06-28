@echo off
cd /d "%~dp0"
powershell -ExecutionPolicy Bypass -File sync.ps1
pause
