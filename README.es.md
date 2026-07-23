<p align="center">
  <img src="https://raw.githubusercontent.com/stevenrojas888/phphone-cli/main/setup/icon.png" width="150" alt="Phphone Logo">
</p>

<div align="center">
  🌍 Languages: <a href="README.md">🇺🇸 English</a> | <strong>🇪🇸 Español</strong>
</div>

<h1 align="center">🐘 PHPHONE</h1>

<p align="center">
  <strong>El Manifiesto Vanilla Mobile: Devolviendo la Libertad al Código.</strong><br>
  Construye aplicaciones móviles híbridas de alto rendimiento para Android e iOS usando únicamente <b>PHP 8.4 Vanilla, HTML, CSS y JavaScript</b>. Sin Flutter, sin React Native, sin Electron y sin dependencias pesadas.
</p>

<p align="center">
  <a href="#why-phphone">Por qué Phphone</a> •
  <a href="#features">Características</a> •
  <a href="#installation">Instalación</a> •
  <a href="#apis">APIs Nativas</a> •
  <a href="#support">💖 Apoya el Proyecto</a>
</p>

---

## ⚡ El Problema del Desarrollo Móvil Actual

El desarrollo móvil moderno está roto. Ha sido secuestrado por la complejidad absurda de entornos sobredimensionados, dependencias frágiles (NPM hell) y arquitecturas pesadas que te obligan a aprender lenguajes nuevos o compilar binarios masivos de Electron/React Native solo para mostrar una simple interfaz.

Durante años se escribió un relato donde PHP pertenecía únicamente a los servidores oscuros y lejanos. Se dijo que su ciclo había concluido frente al brillo de las arquitecturas modernas, y que su destino jamás estaría en la palma de nuestras manos. 

**Pero el código verdadero es atemporal.** Phphone es la hermosa poesía de ver a nuestro viejo amigo despertar en un ecosistema nuevo. Es la demostración técnica de que la simplicidad, la elegancia y la madurez absoluta de PHP pueden latir —con una velocidad y ligereza asombrosas— directamente en el corazón del hardware nativo. 

El lenguaje que construyó la web, ahora barrita en tu bolsillo.

## 🐘 Por qué PHP sigue gobernando la Web (Y ahora el Móvil)

Llevamos 20 años escuchando el mismo chiste: *"PHP está muerto"*. Y sin embargo, hoy sostiene a más del 70% de la web mundial (WordPress, Wikipedia, los cimientos de Facebook, Laravel). Mientras los frameworks de JavaScript nacen y mueren cada 6 meses dejando un rastro de código obsoleto, PHP ha madurado en silencio.

Con PHP 8+, tipado estricto, compilación JIT (Just In Time) y una velocidad de ejecución brutal, el elefante es más rápido y seguro que nunca. 

**¿Por qué usarlo en móvil?** 
Porque es ridículamente fácil de aprender para un principiante, pero tiene una potencia de nivel empresarial (Enterprise-grade) en manos de un Senior. No necesitas inventar la rueda: el ecosistema de Composer tiene décadas de código probado en batalla, libre de las vulnerabilidades diarias del *"NPM Hell"*. Ahora, toda esa estabilidad indestructible sale de los servidores en la nube y se instala directamente en el bolsillo de tus usuarios.

## 💥 Si lo puedes construir para la Web, lo puedes tener en Móvil.
**Sin cambiar de tecnología. Sin aprender frameworks pesados.**

Despídete de los emuladores saturados y de la absurda complejidad del desarrollo móvil actual. **Flutter** te obliga a aprender Dart y a memorizar un árbol infinito de Widgets. **React Native** te ahoga en configuraciones NPM frágiles que se rompen con cada actualización. Son arquitecturas grasosas, pesadas y sobre-diseñadas. 

**La Web, en cambio, es eterna.** El HTML de 1995 sigue renderizando hoy, mientras que una app de Flutter de hace 3 años ya no compila por cambios abruptos. Con Phphone, tu capa visual nunca caduca. Solo actualizas el motor a la última versión de PHP y tu código viejo sigue corriendo tan impecable como el primer día.

Al abrirte las puertas del ecosistema infinito de JavaScript y CSS, Phphone hace obsoletos a los gigantes actuales:

* **¿Ese videojuego 2D que sabes programar en HTML5 con Pixi.js?** 
  Ya no necesitas descargar los 10 GB de Unity ni aprender C# para publicarlo en la Play Store. Phphone lo convierte en una App nativa en segundos.
* **¿Esa escena 3D espectacular que hiciste con Three.js?** 
  Renderízala a 60 FPS directo en el hardware del celular, sin sufrir los cuellos de botella del *Bridge* de React Native.
* **¿Ese sistema corporativo que armas en 10 minutos con Bootstrap, Tailwind o jQuery?** 
  Llévalo al bolsillo de tus clientes hoy mismo. Se acabó el estrés de maquetar peleando con los *Stateless Widgets* de Flutter.
* **¿Esa interfaz fluida que ya tienes lista en Vue o Web Components puros?** 
  Empaquétala nativamente sin reescribir ni una sola coma de tu código visual.

<a id="why-phphone"></a>
### ¿Por qué NO es "solo una web encapsulada"?
Muchos desarrolladores asumen que si usas un *WebView* para la interfaz gráfica (HTML/CSS), tu app es solo un navegador web disfrazado de APK. **Esto es falso en Phphone.**
Mientras que una app encapsulada tradicional requiere que la lógica compleja suceda en la nube, **Phphone inyecta el backend en tu bolsillo**. El WebView es puramente el "cristal del monitor"; la verdadera magia ocurre detrás de escena donde un motor nativo en **C/C++** corre un intérprete real de **PHP 8.4** y una base de datos **SQLite**, todo ejecutándose directamente en los núcleos del procesador de tu teléfono con acceso profundo al hardware nativo.

<a id="features"></a>
## 🚀 Características Principales

1. **El Teléfono como Servidor Físico:** Phphone inyecta el intérprete completo de **PHP 8.4** compilado en C++ directamente en el bolsillo del usuario. El celular levanta su propio servidor local (enrutado internamente a través de `http://127.0.0.1:8081`). Tu app no necesita internet para funcionar. 
2. **Cero Configuración:** Sin Webpack, sin Babel, sin Node_modules. Pones tu archivo `index.php` en la carpeta `src/` y mágicamente tienes una app en tu teléfono.
3. **Seguridad Zero-Hardcode (Nivel Bancario):** PHP utiliza OpenSSL directamente en la memoria RAM para cifrar tu código. Tu código `.php` se inyecta en el instalador `.apk` o `.ipa` encriptado con **AES-256**. Jamás verán tu código fuente, credenciales o llaves de API haciendo ingeniería inversa.
4. **SQLite3 Blindado Integrado:** Bases de datos locales encriptadas. Ni siquiera con acceso Root al teléfono podrán robar los datos de tus usuarios.
5. **Peso Pluma:** El motor base pesa menos de 15 MB. Aplasta el consumo desproporcionado de memoria RAM de otras soluciones híbridas.
6. **🎁 App de Demostración Incluida (Menos de 20 MB):** A diferencia de la aburrida app del "contador" de otros ecosistemas, tu plantilla inicial incluye un **Dashboard de Pruebas de Hardware** (`index.php`) listo para testear la linterna, GPS y cámara, y un generador visual de gradientes (`newgradient.php`). ¿Lo mejor? El intérprete PHP, SQLite y esta app de prueba gráfica pesan combinados **menos de 20 MB**. Como contexto: una app vacía "Hola Mundo" en React Native pesa ~35 MB y en Flutter ~25 MB. Phphone te da todo un ecosistema backend pesando mucho menos.

---

## 🧩 Ecosistema y Dependencias (Aviso Técnico)

> [!NOTE]
> **El Manifiesto Vanilla (Aviso de Responsabilidad)**
> *He construido el núcleo de Phphone basándome en mi propia filosofía de programación: Vanilla, ligera y sin dependencias. Aunque la meta arquitectónica es que cualquier tecnología Web funcione al 100% (gracias a los motores Chromium/WebKit embebidos), la inmensidad de la web es inabarcable para un solo desarrollador.*
> *Es altamente probable que algunas librerías pesadas, características experimentales de JS, o frameworks con un enrutado muy agresivo tengan fricciones. Phphone es un proyecto "Open Core", lo que significa que si encuentras una librería de JS que no se renderiza bien, te invito a investigar el motor, adaptar tu código y compartir la solución con la comunidad.*

Nuestra filosofía principal es **Vanilla PHP / HTML / JS / CSS**. Sin embargo, la flexibilidad de Phphone permite mucho más:

- **Librerías Frontend y Web Components:** El contenedor nativo carga un motor de navegador moderno, por lo que cualquier framework visual (Vue, React, Tailwind, Bootstrap) funcionará perfectamente. Hacemos una **mención especial a Lit.js y los Web Components** nativos, ya que son la pareja perfecta para la filosofía "Vanilla" y ultraligera de Phphone, permitiéndote crear interfaces complejas sin dependencias masivas. Eres libre de usar la arquitectura con la que te sientas más cómodo. *(Pro-Tip: Si quieres usar funciones ultra-modernas de JS y asegurar soporte en teléfonos Android antiguos, simplemente inyecta un Polyfill en tu HTML, exactamente igual a como lo harías en un sitio web tradicional).*
- **Enrutado y Rutas Relativas (Dato Técnico):** Aunque el servidor corre en `http://127.0.0.1:8081`, **no necesitas hardcodear esta IP en tu código**. Escribe HTML/PHP usando rutas relativas tradicionales (`<a href="/newgradient.php">`). El contenedor nativo las resuelve automáticamente, asegurando que tu código sea 100% portable y a prueba de fallos si el puerto cambia.
- **Librerías Backend (Composer) y Frameworks Pesados:** Eres libre de usar `composer` para importar paquetes de terceros. Gracias a la brutal eficiencia del motor C++, **puedes correr frameworks completos como Laravel o Symfony directamente en el celular del usuario**. Sí, Laravel embebido y procesado localmente, sin servidores externos. Esto cambia las reglas del juego en el ecosistema móvil. **IMPORTANTE:** Solo puedes usar paquetes que estén escritos en **PHP puro**. Si una dependencia requiere compilar extensiones de C en el sistema operativo, **no será compatible** con el motor integrado.

---

## 🎨 Guía de Diseño y Configuración (UI/UX)

Para que tus aplicaciones en Phphone se sientan verdaderamente nativas y no como "páginas web envueltas", sigue estas recomendaciones clave:

### 1. Configuración del Dispositivo (Vía CLI)
Puedes ajustar el comportamiento nativo de tu app directamente desde la terminal sin tocar código Swift o Kotlin:

*   **Orientación de Pantalla:**
    ```bash
    phphone config orientation portrait  # Bloquea en modo vertical (Ej: Redes Sociales)
    phphone config orientation landscape # Bloquea en modo horizontal (Ej: Juegos)
    phphone config orientation auto      # Permite rotación (Por defecto)
    ```
*   **Pellizcar para hacer Zoom:**
    Para una sensación 100% nativa, normalmente querrás evitar que el usuario haga zoom en la interfaz gráfica.
    ```bash
    phphone config zoom off  # Deshabilita el zoom táctil (Recomendado)
    phphone config zoom on   # Permite zoom (Para accesibilidad)
    ```

### 2. Manejo del "Notch" y Áreas Seguras (HTML/CSS)
Dado que Phphone abarca el 100% de la pantalla (edge-to-edge), debes asegurarte de que tu interfaz no quede oculta detrás de la muesca de la cámara frontal (Notch) o la barra de gestos inferior.

**En el `<head>` de tu HTML:**
Usa `viewport-fit=cover` para permitir a CSS calcular las áreas seguras.
```html
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
```

**En tu archivo CSS (`style.css`):**
Usa las variables de entorno inyectadas por el motor nativo para dar márgenes a tu Header y NavBar.
```css
.header {
    /* Padding dinámico para evitar la cámara/notch superior */
    padding-top: env(safe-area-inset-top, 20px);
}

.bottom-navbar {
    /* Espacio inferior para evitar la barra de gestos del sistema */
    padding-bottom: env(safe-area-inset-bottom, 20px);
}
```

### 3. Tips CSS para Sensación Nativa
Añade este bloque a tu archivo CSS principal para pulir la interacción del usuario:
```css
body {
    /* Evita que el usuario seleccione texto UI accidentalmente al mantener presionado */
    -webkit-user-select: none;
    user-select: none;
    
    /* Elimina el recuadro gris feo que aparece al tocar botones en móviles */
    -webkit-tap-highlight-color: transparent;
    
    /* Previene el rebote elástico "overscroll" al llegar al final de la app */
    overscroll-behavior-y: none; 
}
```

<a id="installation"></a>
## ⚙️ Requisitos Previos (Entorno de Desarrollo)

Dado que Phphone compila verdaderas aplicaciones nativas, **es indispensable** tener las herramientas de los sistemas operativos instaladas en tu computadora. Sin ellas, el CLI no podrá compilar tu código.

- **PHP 8.0+** instalado en tu consola (necesario para correr el CLI).
- **Para Android:** [Android Studio](https://developer.android.com/studio) instalado (con el Android SDK y un Emulador configurado).
- **Para iOS:** Una Mac con [Xcode](https://developer.apple.com/xcode/) instalado (y las Command Line Tools activas).

---

## 🛠️ Instalación y Uso (CLI)

Phphone incluye una herramienta de línea de comandos (CLI) global e inteligente.

### 1. Instalación del CLI (Requiere PHP local)

**Opción A: Instalación Automática Global (Recomendada)**
Nuestros scripts de instalación hacen todo el trabajo duro por ti (descargar, dar permisos y agregarlo al PATH).

- **Mac/Linux:**
  ```bash
  curl -sS https://phphone.org/install.sh | bash
  ```
- **Windows (PowerShell):**
  Abre PowerShell como administrador y ejecuta:
  ```powershell
  irm https://phphone.org/install.ps1 | iex
  ```

**Opción B: Instalación Local (Vía Composer)**
Si prefieres no instalar nada globalmente o aislar tu entorno, puedes clonar el repositorio e instalar las dependencias localmente:
```bash
git clone https://github.com/stevenrojas888/phphone.git mi-tienda
cd mi-tienda/cli
composer install
cd ..
# En vez de usar el comando global 'phphone', usarás la ruta local:
php cli/bin/phphone run
```

### 2. Crear un Proyecto
```bash
phphone create "Mi Tienda" com.mitienda.app
```
Esto descargará la plantilla limpia y configurará automáticamente el proyecto aislando el entorno.

### 3. Ejecutar y Probar (Hot Reload)
```bash
cd mi-tienda
phphone run
```
Detecta automáticamente si tienes un emulador de Android o iOS abierto, inyecta la app y recarga los cambios de PHP/JS/CSS en tiempo real cada vez que guardas un archivo (Hot Reload instantáneo).

### 4. Personalizar Marca (Ícono y Splash)
Coloca el ícono deseado para tu app (`icon.png`) y la imagen de carga (`splash.png`) dentro de la carpeta `setup/`. Luego ejecuta:
```bash
phphone setup
```
Esto redimensiona e inyecta automáticamente todas las resoluciones nativas requeridas para iOS y Android.

### 5. Compilar para Producción
```bash
phphone build apk --release
```
Genera el paquete final (APK/AAB/IPA) totalmente encriptado.

### 6. Firma de Código (Tiendas)
```bash
phphone sign --keystore mi-llave.jks
```
Firma criptográficamente tu paquete de producción (release) para que esté listo para ser subido a Google Play o la App Store.

### 🧰 Referencia Completa de Comandos CLI
El CLI de Phphone provee una suite completa para todo tu ciclo de desarrollo:

| Comando | Descripción |
| :--- | :--- |
| `create <name> <pkg>` | Crea la plantilla base de un nuevo proyecto Phphone |
| `run` | Compila y lanza la app con Hot Reload instantáneo |
| `setup` | Inyecta tu ícono personalizado y pantalla de carga (Splash) |
| `build <target>` | Compila el proyecto a binario (APK, AAB, IPA) |
| `sign` | Firma criptográficamente los paquetes para producción |
| `rename` | Cambia de forma segura el nombre de la app y el package ID |
| `doctor` | Diagnostica dependencias faltantes o errores de entorno |
| `logs` | Muestra en tiempo real los logs nativos (Logcat/Console) |
| `devices` | Lista los dispositivos físicos y emuladores conectados |
| `screenshot` | Captura la pantalla del dispositivo conectado |
| `clean` | Limpia la caché de construcción nativa |
| `stop` | Detiene la ejecución de la app en el dispositivo |
| `uninstall` | Desinstala la app del dispositivo conectado |

---

<a id="apis"></a>
## 🔌 APIs Nativas Disponibles (Fase 1.0)

Phphone viene con "las baterías incluidas". Hemos superado la paridad básica con Flutter. Puedes acceder al Hardware físico del dispositivo directamente desde tu código PHP importando nuestra clase `Device::`:

```php
<?php
use Phphone\Device;

// ¡No necesitas hacer require_once! El motor C++ inyecta esta clase globalmente.

// Tomar una foto con la cámara nativa
$base64Image = Device::takeCameraPicture();

// Obtener GPS preciso
$location = Device::getGpsLocation();
echo $location['lat'] . ", " . $location['lng'];

// Guardar un token en el llavero seguro nativo (Keychain / Keystore)
Device::secureWrite("api_token", "super_secret_token_123");
```

*(💡 **Pro Tip:** También puedes crear un endpoint en PHP que ejecute estos métodos de hardware y llamarlo de forma asíncrona desde tu JavaScript usando `fetch()` o AJAX para crear interfaces ultra dinámicas sin recargar la página).*

**APIs Soportadas de caja (Sin instalar librerías de terceros):**
- 📸 Cámara y Galería de Fotos.
- 📍 Geolocalización GPS.
- 📇 **Contactos Dinámicos:** Lectura asíncrona de la agenda con soporte de paginación (lazy loading) para miles de contactos.
- 📲 **Sensores en Tiempo Real:** Streaming de Giroscopio y Acelerómetro a frecuencia nativa.
- ☁️ **Notificaciones Push Remotas:** Código base latente para Firebase Cloud Messaging (FCM).
- 💳 **Compras Integradas (IAP):** Pasarela de monetización nativa (StoreKit / Google Play Billing) incluida de forma latente.
- 🎤 Grabación de Audio (Micrófono) y Reproducción de Sonido.
- 📂 Selector de Archivos Nativo (iCloud / Android Storage).
- 🔔 Notificaciones Push Locales.
- 🔐 Llavero Seguro (Keychain / Keystore) y Biometría (FaceID / Huella).
- 🌐 In-App Browser (SafariViewController / Chrome Custom Tabs).
- 📤 Share Nativo (Compartir contenido a WhatsApp, Redes Sociales, etc).
- 🔋 Estado de Batería, Red, y Portapapeles.
- 🔦 Linterna y Vibración (Haptics).

### ⚠️ Peculiaridades del Motor Embebido (Evitando el Zend Bailout)
Dado que Phphone mantiene el núcleo de PHP (C++) vivo en un estado de memoria compartida persistente (vía NanoHTTPD/GCDWebServer) en lugar de destruirlo en cada petición como hace Apache, **los errores fatales se comportan diferente**.

Si el Motor Zend encuentra un estado irrecuperable, activa un **"Zend Bailout"** (un `longjmp` a nivel de C), lo cual apagará instantáneamente el hilo de la aplicación nativa y la app se cerrará de golpe (crash).

> [!CAUTION]
> **Las Reglas de Oro del Backend en Phphone:**
> 1. **NUNCA uses `exit;`, `exit();`, o `die();`**: Estas funciones fuerzan un Zend Bailout. Usa siempre `return` o lanza excepciones para detener el flujo de tu código.
> 2. **Envuelve tus APIs en `try/catch`**: Evita que excepciones no atrapadas o Errores Fatales (como llamar funciones inexistentes o errores de tipado) lleguen a la cima del intérprete. Atrápalos y devuelve un JSON de error estándar a tu frontend.
> 3. **Memoria y Timeouts**: Si procesas volúmenes masivos de datos, usa `set_time_limit(0);` para evitar que el temporizador interno de Zend te cierre la app por "Timeout Bailout".

### 💾 Almacenamiento Persistente y SQLite (Entornos de Solo Lectura)
Al compilar para Android, todos los archivos dentro de la carpeta de tu proyecto (y el `__DIR__` de PHP) quedan empaquetados como **Solo Lectura** dentro del APK por razones de seguridad.

Si intentas que el driver de SQLite abra o cree una base de datos directamente en el `__DIR__`, el motor fallará catastróficamente. Para evitar esto y asegurar persistencia tanto en emulador como en dispositivos físicos, **NUNCA** uses `__DIR__` para guardar datos SQLite. Usa la siguiente validación oficial para enrutar el almacenamiento al directorio `/files` nativo:

```php
// Práctica Oficial Recomendada para SQLite en Phphone
function getDB() {
    // 1. Intentar usar la ruta local del proyecto si tenemos permisos (Ej: Modo Dev / Emulador)
    $dataDir = __DIR__ . '/../../data';
    
    // 2. Si es Solo Lectura (Ej: APK Producción o iOS IPA), buscar la ruta nativa
    if (!is_writable(__DIR__)) {
        $temp = rtrim(sys_get_temp_dir(), '/\\');
        if (strpos($temp, 'cache') !== false) {
            $dataDir = dirname($temp) . '/files/app_data'; // Android Producción
        } else {
            $dataDir = dirname($temp) . '/Documents/app_data'; // iOS Producción o Fallback
        }
    }
    
    if (!is_dir($dataDir)) {
        @mkdir($dataDir, 0777, true);
    }
    
    $dbPath = $dataDir . '/database.sqlite';
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    return $pdo;
}
```

### ⚠️ Limitaciones de Red en Producción (El Problema del POST)
Cuando compilas tu aplicación en modo **Release** (APK de producción con encriptación nativa), Phphone enruta el tráfico usando `http://kie.local`. Por una estricta restricción de seguridad de la arquitectura interna del **WebView de Android**, las intercepciones de red destruyen automáticamente los cuerpos (`body`) de las peticiones HTTP **POST**.

**Como resultado:** Peticiones AJAX o `fetch()` con método POST llegarán al intérprete PHP con `php://input` y `$_POST` completamente vacíos.

**La Solución Oficial (Workaround):**
* Para enviar datos desde JavaScript a PHP, **usa peticiones GET**, empaquetando el payload como una cadena JSON codificada en la URL.
* Ejemplo: `fetch('api.php?data=' + encodeURIComponent(JSON.stringify(payload)))`

---

<a id="support"></a>
## 💖 Apoya el Proyecto (GitHub Sponsors)

Phphone es un proyecto titánico construido de forma independiente (Open-Core con Licencia MIT). 

Actualmente, **necesito tu apoyo urgente para conseguir un equipo Mac**. Todo el puente de iOS (Swift) ha sido diseñado casi "a ciegas", y para poder garantizar actualizaciones, compilar, testear en Xcode y mantener Phphone vivo en el ecosistema de Apple, **la comunidad es vital**.

Si crees en la visión de revivir PHP para la era móvil y quieres que esta herramienta siga creciendo:
👉 **[Apóyame en GitHub Sponsors / Patreon] (AQUÍ IRÁ TU ENLACE)**

Tu aporte (por más pequeño que sea) me ayudará a mantener este motor gratuito, ligero y brutalmente eficiente para todos nosotros.

---

## 🌍 El Futuro: Phphone.org

Pronto lanzaremos nuestro portal oficial **phphone.org**, donde encontrarás:
- Documentación extensiva y tutoriales.
- **Tienda de Plantillas Premium:** Clones de WhatsApp, E-commerce, y CRMs listos para usar, hechos puramente con HTML/PHP/CSS nativizados.
- Un muro de la fama (Showcase) de aplicaciones publicadas en las tiendas hechas con Phphone.

***

<p align="center">
  Hecho con 🐘 + ❤️ para la comunidad Web.
</p>
