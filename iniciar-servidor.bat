@echo off
title Servidor Laravel - Red local
cd /d "%~dp0"

set PUERTO=8000

rem Detecta la IP de la red activa (la que tiene puerta de enlace)
for /f "usebackq delims=" %%i in (`powershell -NoProfile -Command "(Get-NetIPConfiguration | Where-Object { $_.IPv4DefaultGateway -ne $null -and $_.NetAdapter.Status -eq 'Up' } | Select-Object -First 1).IPv4Address.IPAddress"`) do set IP=%%i

if "%IP%"=="" (
    echo No se encontro una conexion de red activa. Revisa que estes conectado al WiFi.
    pause
    exit /b 1
)

rem Avisa si el puerto ya esta ocupado (por ejemplo, otro servidor abierto)
netstat -ano | findstr /r /c:":%PUERTO% .*LISTENING" >nul
if %errorlevel%==0 (
    echo El puerto %PUERTO% ya esta en uso. Cierra el otro servidor antes de iniciar este.
    pause
    exit /b 1
)

echo Compilando estilos y scripts...
call npm run build >nul 2>&1
if errorlevel 1 echo Aviso: no se pudo compilar con npm, se usaran los archivos de la ultima compilacion.

echo.
echo ==============================================
echo   En esta compu:   http://localhost:%PUERTO%
echo   Desde el cel:    http://%IP%:%PUERTO%
echo ==============================================
echo   Para detener el servidor presiona Ctrl + C
echo.

php artisan serve --host=0.0.0.0 --port=%PUERTO%
pause
