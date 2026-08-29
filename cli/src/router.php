<?php
/**
 * Phphone Local Web Router
 * Este script espeja EXACTAMENTE el comportamiento del interceptor JNI nativo
 * en Android (MainActivity.kt), incluyendo la auto-detección del modo de arranque.
 *
 * Modo SPA Web:  Si existe src/index.html, se sirve directamente (Frontend puro).
 * Modo PHP:      Si no existe, se arranca el motor PHP con src/index.php.
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$docRoot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? getcwd(), '/\\');
$path = $docRoot . $uri;

// 1. Si la petición es para un archivo estático (HTML, CSS, JS, IMG), lo servimos crudo.
// Imitamos la lógica de Kotlin: si tiene punto y no es .php
if (preg_match('/\.[a-zA-Z0-9]+$/', $uri) && !preg_match('/\.php$/', $uri)) {
    if (file_exists($path)) {
        return false; // El servidor interno lo entrega directo como archivo
    }
}

// 2. Auto-detección del punto de entrada (espeja la lógica de MainActivity.kt)
// Si existe index.html → Modo SPA Web (Frontend puro con HTML/CSS/JS)
// Si no existe → Modo PHP nativo (Front Controller index.php)
$indexHtml = $docRoot . '/index.html';
$indexFile  = $docRoot . '/index.php';

if (file_exists($indexHtml) && $uri === '/') {
    // Modo SPA Web: servir el index.html directamente
    header('Content-Type: text/html; charset=UTF-8');
    return false; // El servidor de PHP sirve el index.html como archivo estático
}

// Modo PHP: Inyectar el Super Controlador exactamente igual que el motor nativo
$deviceFile = $docRoot . '/Phphone/Device.php';
if (file_exists($deviceFile)) {
    require_once $deviceFile;
}

$targetFile = (file_exists($path) && is_file($path)) ? $path : $indexFile;

if (file_exists($targetFile)) {
    $_SERVER['SCRIPT_NAME'] = '/' . ltrim($uri, '/');
    require $targetFile;
} else {
    echo "Error crítico: No se encontró el archivo solicitado ni el punto de entrada src/index.php / src/index.html.";
}
