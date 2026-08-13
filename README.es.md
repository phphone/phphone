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

## 📖 Glosario

- **Phphone:** El framework o compilador global que engloba el proyecto.
- **Motor Kie (Kie Engine):** El motor core escrito en C++ que contiene los binarios precompilados de PHP y el puente (*bridge*) de comunicación. Es el responsable de que exista el objeto `window.Kie` en tu JavaScript.
- **Dual WebView:** Arquitectura nativa de Phphone que superpone dos navegadores: uno frontal transparente para tu app HTML/PHP, y uno trasero para cargar sitios web externos.
- **KieBridge:** El canal de comunicación directo entre tu JavaScript y el sistema operativo nativo (Android/iOS).

---

<a id="why-phphone"></a>
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

## 🔐 Mejores Prácticas de Seguridad (Lectura Obligatoria)

> [!WARNING]  
> **La Regla de Oro de la Seguridad Móvil: Nunca confíes en el Cliente**
> 
> Aunque Phphone encripta tu código fuente (AES-256) para proteger tu Propiedad Intelectual, **NUNCA debes hardcodear secretos sensibles** (como contraseñas de Base de Datos, llaves de Stripe/AWS, o Tokens Maestros) en tu código PHP ni en archivos `.env`. 
>
> Las aplicaciones móviles (hechas con Phphone, Flutter, Swift, etc.) pueden ser descompiladas por atacantes expertos, exponiendo cualquier texto en su interior.
>
> **La Mejor Práctica (Backend Proxy):** Tu app en Phphone debe actuar solo como un cliente. Todas las operaciones sensibles (como cobrar una tarjeta o consultar una base de datos privada) deben hacerse mediante peticiones HTTP a tu propia API REST remota, donde tus secretos están seguros en un servidor que controlas. Para el almacenamiento local, SQLite guarda los datos en texto plano, así que siempre encripta la información sensible del usuario desde PHP con `openssl_encrypt` antes de guardarla.

---

## 🌐 Navegador Nativo en Segundo Plano (Dual WebView)

Supongamos que estás construyendo tu app con Phphone y necesitas abrir una página web externa (como Google o una pasarela de pagos) *dentro* de tu aplicación. Si usas un `iframe` tradicional, te vas a topar con bloqueos de seguridad (CORS, X-Frame-Options) y sitios que simplemente se niegan a cargar.

Para solucionar esto, Phphone incluye un navegador nativo oculto detrás de tu interfaz HTML. 

**¿Cómo usarlo? Sigue este ejemplo:**

> 💡 **Nota:** No necesitas instalar ningún SDK ni importar librerías externas. El motor nativo de Phphone inyecta automáticamente el objeto `Kie` (el puente de comunicación) de forma invisible dentro del objeto global `window` de tu JavaScript en cuanto la app arranca.

**Paso 1: Agrega esta función a tu JavaScript**
Como Phphone es multiplataforma, la forma de hablarle al motor nativo cambia un poco entre iOS y Android. Copia este *wrapper* en tu código para unificarlo:

```javascript
function callNativeBrowser(action, params = {}) {
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    if (isIOS) {
        if (window.webkit?.messageHandlers?.Kie) {
            window.webkit.messageHandlers.Kie.postMessage({ action, ...params });
        }
    } else {
        if (window.Kie && typeof window.Kie[action] === 'function') {
            if (action === 'loadUrl') window.Kie.loadUrl(params.url);
            if (action === 'setBrowserActive') window.Kie.setBrowserActive(params.active);
            if (action === 'setBrowserMargins') window.Kie.setBrowserMargins(params.top, params.bottom);
            if (action === 'setUiRects') window.Kie.setUiRects(params.rectsJson);
            if (action === 'startDaemon') window.Kie.startDaemon(JSON.stringify(params));
        }
    }
}
```

**Paso 2: Abre la página y ajusta tu interfaz**
Cuando el usuario presione un botón en tu app, enciende el navegador, dale una URL y asegúrate de que el fondo de tu HTML sea transparente para que puedas verlo:

```javascript
// 1. Enciende el navegador de fondo
callNativeBrowser('setBrowserActive', { active: true });

// 2. Carga la página que quieras
callNativeBrowser('loadUrl', { url: 'https://google.com' });

// 3. Vuelve el fondo de tu App transparente (desde CSS)
document.body.style.backgroundColor = 'transparent';
```

**Paso 3 (Opcional): Protege tus menús**
Si tu app tiene una barra de navegación superior (Header) de `60px`, no querrás que el navegador nativo se dibuje por debajo de ella. Puedes "empujarlo" usando los márgenes:

```javascript
// Deja 60px libres arriba y 0px abajo
callNativeBrowser('setBrowserMargins', { top: 60, bottom: 0 });
```

**Paso 4 (Opcional): Permite que el usuario interactúe (Touch y Scroll)**
Como tu aplicación web ahora es un "cristal" transparente frente al navegador nativo, los toques se bloquearán. Para dejar pasar los toques al navegador nativo, dile a Phphone en qué rectángulos exactos están tus menús. Todo lo que esté fuera de ellos será clickeable en el navegador de fondo.

```javascript
// Pasa un array con las coordenadas de tus elementos UI
callNativeBrowser('setUiRects', { 
    rectsJson: JSON.stringify([
        { left: 0, top: 0, right: window.innerWidth, bottom: 60 }
    ]) 
});
```

Además, el navegador nativo te avisará cada vez que el usuario haga scroll, emitiendo un evento `nativeScroll`. Puedes escucharlo para ocultar/mostrar tus menús dinámicamente:

```javascript
window.addEventListener('nativeScroll', (e) => {
    const dy = e.detail.dy; // Positivo si baja, Negativo si sube
    if (dy > 10) console.log("Ocultar Header");
});
```

> [!WARNING]
> Cuando NO estés usando este navegador de fondo, asegúrate de apagarlo (`active: false`) y mantener el fondo de tu etiqueta `<body>` con un color sólido (ej. `background-color: white;`).

---

## 🔒 Gestión Dinámica de Permisos Bajo Demanda (Just-In-Time)

Phphone sigue la filosofía de **Permisos Mínimos y Bajo Demanda (Just-In-Time)**. Las aplicaciones creadas con Phphone no solicitan permisos al abrir la aplicación, sino únicamente cuando el desarrollador o el usuario ejecuta activamente la funcionalidad desde su código PHP.

### 💡 Ejemplo Práctico 1: Solicitar Permiso de Notificaciones antes de enviar una alerta

```php
use Phphone\Device;

// Paso 1: Pedir el permiso nativo al usuario (Android 13+ / iOS)
$permisoConcedido = Device::requestNotificationPermission();

if ($permisoConcedido) {
    // Paso 2: Si el usuario aceptó, enviamos la notificación
    Device::notification("¡Bienvenido!", "Gracias por activar las notificaciones");
} else {
    // Si denegó el permiso, mostramos un mensaje flotante nativo sin bloquear la app
    Device::toast("No podremos enviarte alertas porque rechazaste el permiso");
}
```

### 💡 Ejemplo Práctico 2: Tomar una foto usando Permisos dinámicos o explícitos

```php
use Phphone\Device;

// Opción A: Verificación previa con requestPermission
if (Device::requestPermission('camera')) {
    $fotoBase64 = Device::camera();
    if ($fotoBase64) {
        Device::toast("Foto capturada con éxito");
    }
} else {
    Device::toast("Se requiere acceso a la cámara");
}

// Opción B: Llamada directa (Device::camera() solicita permiso automáticamente si no fue concedido)
$fotoBase64 = Device::camera();
```

### 📋 Referencia de Permisos Soportados (`Device::requestPermission($tipo)`)

| Tipo `$tipo` | Permiso Nativo Android | Permiso Nativo iOS | Descripción |
| :--- | :--- | :--- | :--- |
| `'notifications'` | `POST_NOTIFICATIONS` | `UNUserNotificationCenter` | Notificaciones flotantes y Push. |
| `'gps'` | `ACCESS_FINE_LOCATION` | `CoreLocation` | Coordenadas latitud/longitud. |
| `'camera'` | `CAMERA` | `AVFoundation` | Captura de fotografías. |
| `'microphone'` | `RECORD_AUDIO` | `AVAudioSession` | Grabación de audio. |
| `'contacts'` | `READ_CONTACTS` | `Contacts` | Lista de contactos. |
| `'storage'` | `READ_EXTERNAL_STORAGE` / Picker | `UIDocumentPicker` | Archivos e imágenes del disco. |
| `'biometric'` | `BiometricPrompt` | `LocalAuthentication` | Face ID, Touch ID y Huella. |

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

### 4. 🔔 Configuración de Notificaciones Push (Firebase FCM)

Phphone incluye soporte nativo listo para usar con Firebase Cloud Messaging (FCM) mediante `Phphone\Device::getPushToken()`.

> [!IMPORTANT]
> **Coincidencia de Package ID:** Al registrar la aplicación en la Consola de Firebase, el *Nombre del paquete Android (Package Name)* o *Bundle ID de iOS* debe ser **exactamente idéntico** al Package ID de tu proyecto Phphone. De lo contrario, Gradle rechazará el archivo `google-services.json`.
>
> 💡 **Si tu proyecto ya existe en Firebase con otro paquete:**
> Puedes ajustar el Package ID de tu proyecto Phphone en los siguientes archivos:
> - **Android:** [`android/app/build.gradle.kts`](file:///android/app/build.gradle.kts) ➔ Modifica la línea `applicationId = "com.tuempresa.tuapp"`
> - **iOS:** [`ios/project.yml`](file:///ios/project.yml) o `phphone_meta.json` ➔ Modifica `"bundleId": "com.tuempresa.tuapp"`

#### Para Android:
1. En la [Consola de Firebase](https://console.firebase.google.com/), registra tu app con el Package ID de tu proyecto Phphone (ej: `com.phphone.miapp`).
2. Descarga el archivo **`google-services.json`**.
3. Guárdalo en la ruta de tu proyecto: `android/app/google-services.json`
4. El compilador detectará el archivo de forma automática al compilar.

#### Para iOS:
1. En la Consola de Firebase, ve a **Configuración del Proyecto ⚙️** > **Tus aplicaciones** > **App iOS**.
2. Descarga el archivo **`GoogleService-Info.plist`**.
3. Guárdalo en la ruta de tu proyecto: `ios/App/GoogleService-Info.plist`

#### Para tu Servidor Backend / API REST:
1. En la Consola de Firebase, ve a **Configuración del Proyecto ⚙️** > pestaña **Cuentas de Servicio**.
2. Haz clic en **Generar nueva clave privada** para descargar el archivo JSON del Admin SDK (`firebase-adminsdk-*.json`).
3. Utiliza este archivo exclusivamente en tu servidor backend PHP/Node.js en producción para autenticar el envío de notificaciones hacia los teléfonos.

#### 📌 Motor Nativo y Especificación de Payloads Push (Android & iOS)

Phphone incluye un **Parser/Motor Nativo Unificado de Notificaciones**. Esto permite al desarrollador controlar el comportamiento nativo de las notificaciones directamente desde el payload JSON de su backend en PHP/Node.js **sin tocar nada de código nativo (Kotlin / Swift)**:

- **Experiencia de Usuario Optimizada (Single-Top):** Al tocar cualquier notificación con la app abierta o en segundo plano, la aplicación vuelve al frente de forma fluida en Android y en iOS sin reiniciar la WebView ni volver a mostrar el splash screen.

##### 📋 Especificación de Parámetros Estándar (`data` / `notification`)

| Campo | Tipo | Descripción | Comportamiento Nativo |
| :--- | :--- | :--- | :--- |
| `title` | `string` | Título principal de la notificación. | Muestra el título en negrita. |
| `body` | `string` | Contenido del mensaje. | Texto descriptivo de la notificación. |
| `tag` / `id` | `string` | *(Opcional)* Identificador único de reemplazo. | Si se envía, la notificación **actualiza/reemplaza** a la anterior con el mismo ID. Si se omite, **se acumulan**. |
| `group` / `thread_id` | `string` | *(Opcional)* Clave de grupo. | Agrupa las notificaciones colapsadas (Android `Group Summary`, iOS `threadIdentifier`). |
| `route` / `url` | `string` | *(Opcional)* Ruta de navegación en la WebApp. | Se pasa como parámetro extra a la app para navegar al tocar la notificación. |
| `reply` | `boolean` / `string` | *(Opcional)* Respuesta directa (`true`). | Añade un campo de texto y botón nativo *"Responder"* en la propia notificación. |

---

##### 💡 Ejemplos Prácticos de Payloads (Backend / PHP)

###### Caso 1: Notificaciones que se ACUMULAN (Ej: Recordatorios / Avisos)
*Simplemente **omite** el parámetro `tag`.* Cada mensaje recibido creará una tarjeta independiente en la barra de tareas.

```json
{
  "notification": {
    "title": "Recordatorio de Tarea",
    "body": "Tienes una reunión pendiente a las 3:00 PM"
  }
}
```

###### Caso 2: Notificaciones que se SOBREPONEN / REEMPLAZAN (Ej: Estado de Pedido / Progreso)
*Envía un `tag` o `id` constante.* Si el `tag` es idéntico, la notificación anterior se actualiza (ej: de *"En preparación"* a *"En camino"*).

```json
{
  "notification": {
    "title": "Estado del Pedido #1052",
    "body": "Tu pedido ya está en camino 🚚"
  },
  "data": {
    "tag": "pedido_1052"
  }
}
```

###### Caso 3: Notificaciones AGRUPADAS estilo WhatsApp / Gmail (Ej: Hilos de Chat)
*Envía varios mensajes con el mismo `group` o `thread_id` (y con `tag`s distintos) para que se empaqueten juntos dentro de un contenedor desplegable.*

**Primer envío (Mensaje 1):**
```json
{
  "notification": {
    "title": "Carlos Ramírez",
    "body": "Hola, ¿tienes 5 minutos?"
  },
  "data": {
    "group": "chat_carlos_99",
    "tag": "msg_1001"
  }
}
```

**Segundo envío (Mensaje 2, unos segundos después):**
```json
{
  "notification": {
    "title": "Carlos Ramírez",
    "body": "¿Revisaste el borrador del proyecto?"
  },
  "data": {
    "group": "chat_carlos_99",
    "tag": "msg_1002"
  }
}
```

📱 **Resultado en el dispositivo:**
Android e iOS empaquetan automáticamente ambos avisos bajo un mismo encabezado **"Carlos Ramírez (2 mensajes)"**, permitiendo al usuario desplegar la lista o ver cada mensaje individualmente exactamente como en WhatsApp, Telegram o Gmail.

###### Caso 4: Notificaciones con RESPUESTA DIRECTA (Direct Reply)
*Envía `"reply": true` dentro del objeto `data`.* El sistema nativo colocará un botón y campo de texto *"Responder"* en la propia notificación.

```json
{
  "notification": {
    "title": "Soporte Técnico",
    "body": "¿Se resolvió tu problema con el servicio?"
  },
  "data": {
    "reply": true,
    "tag": "ticket_501"
  }
}
```

💬 **Resultado en el dispositivo:**
El usuario podrá presionar **"Responder"** directamente en la notificación de Android o iOS, escribir su texto (ej: *"Sí, todo perfecto, gracias"*) y enviar la respuesta sin abrir la aplicación.

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
- 👻 **Tareas en Segundo Plano (Demonios):** Ejecución silenciosa de scripts PHP de fondo (Foreground Services en Android & BGTaskScheduler en iOS).

### 👻 Tareas en Segundo Plano (Demonios)
Las tareas en segundo plano en el desarrollo móvil suelen ser muy complejas, pero Phphone lo simplifica usando una arquitectura híbrida. Así funciona el flujo:

1. **El Gatillo (JS ➔ Nativo):** Tu frontend en JavaScript le da la orden al sistema operativo (Android/iOS) de iniciar la tarea.
2. **El Bucle (Nativo ➔ PHP):** El sistema operativo crea un hilo de fondo persistente (`ForegroundService` en Android o `BGTaskScheduler` en iOS) que hace peticiones HTTP invisibles hacia tu servidor PHP local cada X segundos.
3. **La Ejecución (PHP):** Tu script PHP despierta, ejecuta la lógica de fondo, y vuelve a dormirse.

#### Escenario A: PHP Vanilla (Por defecto)
Si estás construyendo una app estándar de Phphone sin enrutamiento complejo, el motor nativo buscará un archivo `daemon.php` en tu directorio raíz por defecto.

**1. Iniciar desde JS:**
```javascript
callNativeBrowser('startDaemon', { taskName: 'sync_data', interval: 60 });
```

**2. Manejarlo en PHP (`src/daemon.php`):**
```php
<?php
$task = $_GET['task'] ?? 'unknown';
// Tu lógica de negocio aquí (ej: Sincronizar SQLite a un servidor externo, push)
```

#### Escenario B: Frameworks Avanzados (Laravel, Symfony, etc.)
Si estás corriendo un framework MVC completo dentro de Phphone, dejar un archivo `daemon.php` en la raíz rompe tu arquitectura de rutas. En su lugar, puedes pasar un `endpoint` personalizado para que el sistema operativo nativo llame directamente al enrutador de tu framework (ej. `public/index.php`).

> ⚠️ **Advertencia sobre Frameworks:** Frameworks pesados como Laravel funcionarán en Phphone, pero debido a que el sistema de archivos de un APK de Android es de **Solo-Lectura**, DEBES modificar las rutas de almacenamiento (ej. `storage/` o `var/cache/`) para que apunten al directorio `data` escribible del sistema operativo, de lo contrario colapsarán con un error fatal.
> 
> **Ejemplo de Solución para Laravel (`bootstrap/app.php`):**
> ```php
> $app = new Illuminate\Foundation\Application($_ENV['APP_BASE_PATH'] ?? dirname(__DIR__));
> // Redirigir el almacenamiento a la partición escribible del OS
> $app->useStoragePath(sys_get_temp_dir() . '/laravel_storage');
> ```

**1. Iniciar desde JS (Endpoint Personalizado):**
```javascript
callNativeBrowser('startDaemon', { 
    taskName: 'sync_data', 
    interval: 60,
    endpoint: '/api/background-tasks' // ¡El SO nativo hará ping a esta ruta de Laravel!
});
```

**2. Manejarlo en Laravel (`routes/api.php`):**
```php
Route::get('/background-tasks', function(Request $request) {
    if ($request->task === 'sync_data') {
        // Ejecutar modelos de Eloquent, colas, etc.
    }
});
```

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
👉 **[Apóyame en GitHub Sponsors](https://github.com/sponsors/stevenrojas888)**

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
