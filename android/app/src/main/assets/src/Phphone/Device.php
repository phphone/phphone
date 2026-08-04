<?php
namespace Phphone;

/**
 * Phphone Device API
 * Este es el "Super Controlador" que conecta PHP puro con el hardware
 * nativo del teléfono (Android/iOS) de manera síncrona usando un puente HTTP ultraligero.
 */
class Device {
    
    private const BRIDGE_URL = 'http://127.0.0.1:8081';

    /**
     * Hace vibrar el dispositivo físico.
     * @param int $milliseconds Duración de la vibración en milisegundos.
     */
    public static function vibrate(int $ms = 500): bool {
        $response = @file_get_contents(self::BRIDGE_URL . '/api/vibrate?ms=' . $ms, false, stream_context_create(['http' => ['timeout' => 2]]));
        return $response !== false;
    }

    /**
     * Muestra un mensaje flotante nativo del sistema
     */
    public static function toast(string $message): bool {
        $response = @file_get_contents(self::BRIDGE_URL . '/api/toast?msg=' . urlencode($message), false, stream_context_create(['http' => ['timeout' => 2]]));
        return $response !== false;
    }

    /**
     * Envía una notificación Push Local del sistema
     */
    public static function notification(string $title, string $message): bool {
        $url = self::BRIDGE_URL . '/api/notification?title=' . urlencode($title) . '&msg=' . urlencode($message);
        $response = @file_get_contents($url, false, stream_context_create(['http' => ['timeout' => 2]]));
        return $response !== false;
    }

    /**
     * Obtiene las coordenadas GPS actuales
     * Pide permisos dinámicos automáticamente.
     * @return array|false Retorna ['lat' => float, 'lng' => float] o false si el usuario denegó permisos
     */
    public static function gps() {
        // Aumentar el timeout porque el usuario puede tardar en aceptar el popup de permisos
        $context = stream_context_create(['http' => ['timeout' => 60]]);
        $response = @file_get_contents(self::BRIDGE_URL . '/api/gps', false, $context);
        if ($response === false) return false;
        
        $data = json_decode($response, true);
        if (isset($data['error'])) return false;
        return $data;
    }

    /**
     * Abre la cámara nativa y devuelve la foto en Base64
     * Pide permisos dinámicos automáticamente.
     * @return string|false Base64 o false si se canceló/denegó
     */
    public static function camera() {
        $context = stream_context_create(['http' => ['timeout' => 120]]);
        $response = @file_get_contents(self::BRIDGE_URL . '/api/camera', false, $context);
        if ($response === false) return false;
        
        $data = json_decode($response, true);
        if (isset($data['error'])) return false;
        return $data['base64'] ?? false;
    }

    /**
     * Obtiene métricas de hardware del sistema (RAM, CPU)
     * @return array|false Retorna ['success' => true, 'ram_used' => int, 'ram_total' => int, 'cpu' => float]
     */
    public static function metrics() {
        $context = stream_context_create(['http' => ['timeout' => 2]]);
        $response = @file_get_contents(self::BRIDGE_URL . '/api/metrics', false, $context);
        if ($response === false) return ['success' => false, 'error' => 'Puente no disponible'];
        
        $data = json_decode($response, true);
        if (isset($data['error'])) return ['success' => false, 'error' => $data['error']];
        return $data;
    }

    /**
     * Inicia la lectura continua del giroscopio.
     * @return bool True si se inició correctamente
     */
    public static function startGyroscope(): bool {
        $context = stream_context_create(['http' => ['timeout' => 2]]);
        $response = @file_get_contents(self::BRIDGE_URL . '/api/gyroscope/start', false, $context);
        return $response !== false;
    }

    /**
     * Detiene la lectura continua del giroscopio.
     */
    public static function stopGyroscope(): bool {
        $context = stream_context_create(['http' => ['timeout' => 2]]);
        $response = @file_get_contents(self::BRIDGE_URL . '/api/gyroscope/stop', false, $context);
        return $response !== false;
    }

    /**
     * Obtiene los datos actuales del giroscopio de la memoria RAM.
     * Requiere haber llamado a startGyroscope() previamente.
     * @return array|false Retorna ['x' => float, 'y' => float, 'z' => float] o false si no está disponible
     */
    public static function gyroscope() {
        $context = stream_context_create(['http' => ['timeout' => 1]]); // Timeout ultra rápido
        $response = @file_get_contents(self::BRIDGE_URL . '/api/gyroscope', false, $context);
        if ($response === false) return false;
        
        $data = json_decode($response, true);
        if (isset($data['error'])) return false;
        return $data;
    }

    /**
     * Solicita autenticación biométrica (FaceID, TouchID, Huella)
     * @param string $reason El motivo que se mostrará al usuario
     * @return bool True si se autenticó correctamente
     */
    public static function authenticate(string $reason = 'Confirma tu identidad'): bool {
        $context = stream_context_create(['http' => ['timeout' => 60]]);
        $response = @file_get_contents(self::BRIDGE_URL . '/api/biometric?reason=' . urlencode($reason), false, $context);
        if ($response === false) return false;
        
        $data = json_decode($response, true);
        return isset($data['success']) && $data['success'] === true;
    }

    /**
     * Obtiene los contactos de la libreta de direcciones del dispositivo.
     * @return array|false Retorna un array de contactos o false si falla.
     */
    public static function getContacts() {
        $context = stream_context_create(['http' => ['timeout' => 60]]);
        $response = @file_get_contents(self::BRIDGE_URL . '/api/contacts', false, $context);
        if ($response === false) return false;
        
        $data = json_decode($response, true);
        if (isset($data['error'])) return false;
        return $data;
    }

    /**
     * Obtiene el token de registro de Firebase Cloud Messaging (FCM) para Notificaciones Push.
     * Requiere que el framework tenga activado Firebase en los binarios nativos (descomentado).
     * @return string|false Retorna el token FCM o false si Firebase no está activado.
     */
    public static function getPushToken() {
        $context = stream_context_create(['http' => ['timeout' => 5]]);
        $response = @file_get_contents(self::BRIDGE_URL . '/api/push_token', false, $context);
        if ($response === false) return false;
        
        $data = json_decode($response, true);
        if (isset($data['error']) || !isset($data['token'])) return false;
        return $data['token'];
    }

    /**
     * Inicia el flujo de compra integrada (In-App Purchase) para un producto específico.
     * Requiere que el framework tenga activadas las dependencias de facturación (StoreKit/Google Play Billing).
     * @param string $productId El ID del producto configurado en la tienda.
     * @return array|false Retorna el recibo de compra o false si falla/no está activado.
     */
    public static function purchaseProduct($productId) {
        $context = stream_context_create(['http' => ['timeout' => 120]]);
        $response = @file_get_contents(self::BRIDGE_URL . '/api/iap/purchase?productId=' . urlencode($productId), false, $context);
        if ($response === false) return false;
        
        $data = json_decode($response, true);
        if (isset($data['error'])) return false;
        return $data;
    }

    /**
     * Abre la galería nativa para seleccionar una imagen
     * @return string|false Base64 de la imagen o false si se canceló
     */
    public static function pickImage() {
        $context = stream_context_create(['http' => ['timeout' => 300]]); // 5 mins max
        $response = @file_get_contents(self::BRIDGE_URL . '/api/gallery', false, $context);
        if ($response === false) return false;
        
        $data = json_decode($response, true);
        if (isset($data['error'])) return false;
        return $data['base64'] ?? false;
    }

    /**
     * Abre el menú nativo de compartir
     * @param string $text Texto a compartir
     * @param string $url URL opcional a compartir
     */
    public static function share(string $text, string $url = ''): bool {
        $endpoint = '/api/share?text=' . urlencode($text) . '&url=' . urlencode($url);
        $response = @file_get_contents(self::BRIDGE_URL . $endpoint, false, stream_context_create(['http' => ['timeout' => 5]]));
        return $response !== false;
    }

    /**
     * Obtiene el estado de la batería
     * @return array|false ['level' => int (0-100), 'isCharging' => bool]
     */
    public static function battery() {
        $response = @file_get_contents(self::BRIDGE_URL . '/api/battery', false, stream_context_create(['http' => ['timeout' => 2]]));
        if ($response === false) return false;
        
        $data = json_decode($response, true);
        if (isset($data['error'])) return false;
        return $data;
    }

    /**
     * Obtiene el estado de conexión de red
     * @return string 'wifi', 'cellular', 'offline' o 'unknown'
     */
    public static function network(): string {
        $response = @file_get_contents(self::BRIDGE_URL . '/api/network', false, stream_context_create(['http' => ['timeout' => 2]]));
        if ($response === false) return 'unknown';
        
        $data = json_decode($response, true);
        return $data['status'] ?? 'unknown';
    }

    /**
     * Lee o escribe en el portapapeles del sistema
     * @param string|null $text Texto a copiar. Si es null, retorna el contenido actual.
     * @return string|bool Retorna el texto del portapapeles (si $text es null) o bool si se guardó.
     */
    public static function clipboard(?string $text = null) {
        if ($text !== null) {
            // Escribir
            $opts = [
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-Type: application/x-www-form-urlencoded',
                    'content' => http_build_query(['text' => $text]),
                    'timeout' => 2
                ]
            ];
            $response = @file_get_contents(self::BRIDGE_URL . '/api/clipboard', false, stream_context_create($opts));
            return $response !== false;
        } else {
            // Leer
            $response = @file_get_contents(self::BRIDGE_URL . '/api/clipboard', false, stream_context_create(['http' => ['timeout' => 2]]));
            if ($response === false) return false;
            $data = json_decode($response, true);
            return $data['text'] ?? false;
        }
    }

    /**
     * Enciende o apaga la linterna
     */
    public static function flashlight(bool $on): bool {
        $state = $on ? 'true' : 'false';
        $response = @file_get_contents(self::BRIDGE_URL . '/api/flashlight?on=' . $state, false, stream_context_create(['http' => ['timeout' => 2]]));
        return $response !== false;
    }

    /**
     * Obtiene información básica de telemetría del dispositivo
     * @return array|false ['model' => '...', 'os_version' => '...', 'uuid' => '...']
     */
    public static function info() {
        $response = @file_get_contents(self::BRIDGE_URL . '/api/info', false, stream_context_create(['http' => ['timeout' => 2]]));
        if ($response === false) return false;
        
        $data = json_decode($response, true);
        if (isset($data['error'])) return false;
        return $data;
    }

    /**
     * Reproduce audio nativo (ideal para música de fondo sin restricción de autoplay web)
     * @param string $path Ruta local del archivo de audio relativa a la carpeta public del proyecto (ej: 'assets/music.mp3') o URL
     * @param bool $loop Si el audio debe repetirse
     * @return bool True si el comando se envió exitosamente
     */
    public static function playAudio(string $path, bool $loop = false): bool
    {
        $url = self::BRIDGE_URL . '/api/audio/play?path=' . urlencode($path) . '&loop=' . ($loop ? 'true' : 'false');
        $response = @file_get_contents($url, false, stream_context_create(['http' => ['timeout' => 2]]));
        return $response !== false;
    }

    /**
     * Detiene la reproducción de audio nativo
     */
    public static function stopAudio(): bool
    {
        $response = @file_get_contents(self::BRIDGE_URL . '/api/audio/stop', false, stream_context_create(['http' => ['timeout' => 2]]));
        return $response !== false;
    }

    /**
     * Escribe datos en el almacenamiento seguro nativo (Keychain / Keystore).
     * @param string $key Clave
     * @param string $value Valor a encriptar y guardar
     * @return bool True si se guardó con éxito
     */
    public static function secureWrite(string $key, string $value): bool {
        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query(['key' => $key, 'value' => $value]),
                'timeout' => 2
            ]
        ];
        $response = @file_get_contents(self::BRIDGE_URL . '/api/secure/write', false, stream_context_create($opts));
        return $response !== false;
    }

    /**
     * Lee datos del almacenamiento seguro nativo.
     * @param string $key Clave
     * @return string|false El valor desencriptado o false si no existe
     */
    public static function secureRead(string $key) {
        $response = @file_get_contents(self::BRIDGE_URL . '/api/secure/read?key=' . urlencode($key), false, stream_context_create(['http' => ['timeout' => 2]]));
        if ($response === false) return false;
        
        $data = json_decode($response, true);
        return $data['value'] ?? false;
    }

    /**
     * Abre una URL externa en la app nativa correspondiente (ej. WhatsApp, Teléfono, Navegador).
     * @param string $url URL o esquema (ej. 'whatsapp://send?text=Hola', 'tel:+123456', 'https://google.com')
     * @return bool True si se pudo abrir
     */
    public static function openUrl(string $url): bool {
        $response = @file_get_contents(self::BRIDGE_URL . '/api/openurl?url=' . urlencode($url), false, stream_context_create(['http' => ['timeout' => 2]]));
        return $response !== false;
    }

    /**
     * Abre un In-App Browser nativo sobre la aplicación (ideal para pasarelas de pago o logins).
     * @param string $url URL web a abrir
     * @return bool True si se pudo abrir
     */
    public static function openBrowser(string $url): bool {
        $response = @file_get_contents(self::BRIDGE_URL . '/api/inappbrowser?url=' . urlencode($url), false, stream_context_create(['http' => ['timeout' => 2]]));
        return $response !== false;
    }

    /**
     * Inicia la grabación del micrófono en segundo plano.
     * @return bool True si empezó a grabar
     */
    public static function recordAudioStart(): bool {
        $response = @file_get_contents(self::BRIDGE_URL . '/api/mic/start', false, stream_context_create(['http' => ['timeout' => 5]]));
        return $response !== false;
    }

    /**
     * Detiene la grabación y devuelve el archivo de audio.
     * @return string|false El audio en Base64 o false si falló
     */
    public static function recordAudioStop() {
        $context = stream_context_create(['http' => ['timeout' => 15]]);
        $response = @file_get_contents(self::BRIDGE_URL . '/api/mic/stop', false, $context);
        
        if ($response === false) return false;
        
        $data = json_decode($response, true);
        if (isset($data['error'])) return false;
        
        return $data['base64'] ?? false;
    }

    /**
     * Abre el selector nativo para escoger cualquier tipo de archivo (Documentos, PDFs).
     * @return array|false ['filename' => 'doc.pdf', 'base64' => '...'] o false si se canceló
     */
    public static function pickFile() {
        $context = stream_context_create(['http' => ['timeout' => 300]]); // 5 mins max
        $response = @file_get_contents(self::BRIDGE_URL . '/api/filepicker', false, $context);
        if ($response === false) return false;
        
        $data = json_decode($response, true);
        if (isset($data['error'])) return false;
        return $data;
    }

    /**
     * Guarda un archivo en la carpeta pública del usuario (Ej. Descargas o Documentos).
     * @param string $filename Nombre del archivo (ej. 'recibo.pdf')
     * @param string $base64Data Contenido del archivo en base64
     * @return bool True si se guardó con éxito
     */
    public static function downloadFile(string $filename, string $base64Data): bool {
        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query(['filename' => $filename, 'base64' => $base64Data]),
                'timeout' => 10
            ]
        ];
        $response = @file_get_contents(self::BRIDGE_URL . '/api/file/download', false, stream_context_create($opts));
        return $response !== false;
    }

    /**
     * Comunicación interna con el servidor local (NanoHTTPD / GCDWebServer).
     */
    private static function callBridge(string $endpoint): array {
        // Ignoramos errores de timeout o si el servidor está apagado (ej: si se corre en web local)
        $context = stream_context_create([
            'http' => [
                'timeout' => 2, // 2 segundos máximo
                'ignore_errors' => true
            ]
        ]);
        
        $response = @file_get_contents(self::BRIDGE_URL . $endpoint, false, $context);
        
        if ($response === false) {
            return ['success' => false, 'error' => 'No se pudo conectar al puente de hardware. ¿Estás en el emulador/dispositivo real?'];
        }

        return json_decode($response, true) ?? ['success' => false, 'error' => 'Respuesta no válida del puente.'];
    }

    /**
     * Inicia un demonio en segundo plano de manera nativa (Foreground Service en Android / BGTask en iOS).
     * @param string $taskName El nombre de la tarea (ej: 'sync', 'daemon') que luego buscará el archivo PHP asociado.
     * @param int $interval Segundos recomendados entre ejecuciones (principalmente iOS).
     */
    public static function startDaemon(string $taskName, int $interval = 60): bool {
        // La implementación en JS en index.php envía el comando 'daemon' usando 'triggerHardware' que es
        // interceptado por Android/iOS y maneja el inicio del demonio.
        // Pero si llamamos a `startDaemon` vía la API `action=daemon` de `index.php` 
        // necesitamos devolver true o disparar el bridge nativamente si hubiese endpoint HTTP. 
        // Al devolver true aquí, el JS responderá correctamente e intentará inicializarlo nativamente.
        return true;
    }
}
