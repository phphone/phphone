@echo off
REM Phphone CLI Windows Wrapper
REM Este script redirige la llamada al ejecutable de PHP

setlocal DISABLEDELAYEDEXPANSION

REM Obtenemos el directorio donde reside este script .bat
set BIN_TARGET=%~dp0\phphone

REM Pasamos todos los argumentos (%*) al binario de PHP
php "%BIN_TARGET%" %*
