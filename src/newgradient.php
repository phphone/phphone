<?php
// Enrutador Frontal Minimalista
$uri = $_SERVER['REQUEST_URI'] ?? '/';

if (strpos($uri, 'generate-gradient') !== false) {
    header('Content-Type: application/json');
    // Generar un gradiente aleatorio con Closure para persistencia en memoria
    $randomHex = function () {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    };

    echo json_encode([
        'success' => true,
        'gradient' => [
            'colors' => [$randomHex(), $randomHex()],
            'angle' => mt_rand(0, 360)
        ]
    ]);
    return; // Usamos return en vez de exit para no matar el motor C++ embebido
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'New Gradient Phphone') ?></title>
    <!-- Google Fonts for premium typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Outfit:wght@800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/app.css">
</head>

<body>
    <div class="phphone-viewport">
        <div id="gradient-bg" class="gradient-wrapper">
            <div class="glass-card">
                <h1>New Gradient</h1>
                <p class="subtitle">Probando rutas en Phphone</p>
                <button id="generate-btn" class="btn-premium">Generate Gradient</button>
                <a href="/index.php" class="btn-premium" style="margin-top: 15px; display: block; text-align: center; text-decoration: none; background: rgba(255,255,255,0.1);">⬅ Volver a Index</a>
            </div>
        </div>
    </div>

    <!-- Motor del Framework -->
    <script src="/js/kie.js"></script>
    <!-- Lógica de la Aplicación -->
    <script src="/js/app.js"></script>
</body>

</html>
