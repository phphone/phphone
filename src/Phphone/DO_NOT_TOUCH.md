# ⚠️ WARNING: DO NOT TOUCH THIS FOLDER ⚠️

## ¿Qué es la carpeta `Phphone/`?

Esta carpeta contiene el **Super Controlador Principal (Core Bridge)** de Phphone y Phphone. Es el puente vital que conecta tu código PHP puro de la carpeta `src/` con el hardware nativo (Sensores, Bluetooth, Motor de Vibración, Notificaciones, etc.) del teléfono (Android/iOS).

## ¿Por qué NO debes modificarla?

1. **Magia de C++:** Los archivos dentro de esta carpeta (`Device.php`, etc.) son inyectados directamente en la memoria RAM milisegundos antes de que tu código empiece a ejecutarse, mediante el motor de C++ embebido (`kie_engine.cpp`). 
2. **Sincronización de Hardware:** Cambiar el nombre de las funciones o la estructura interna de estos archivos romperá instantáneamente la comunicación con los servidores Kotlin/Swift nativos (`KieWebServer`). 
3. **Reglas de Empaquetado:** La carpeta se llama `Phphone` (sin punto ni guión bajo inicial) por una razón estrictamente arquitectónica: si usáramos prefijos como `.` o `_`, compiladores nativos como Gradle y AAPT (Android Asset Packaging Tool) ignorarían la carpeta durante la creación del APK, rompiendo la aplicación en producción.

## ¿Cómo usarla correctamente?

Tú, como desarrollador Phphone, **nunca necesitas modificar estos archivos ni requerirlos (`require`) manualmente.** 

La clase estará siempre disponible mágicamente en cualquier archivo de tu proyecto de forma global. Solo tienes que usarla, por ejemplo:

```php
\Phphone\Device::vibrate(1000);
\Phphone\Device::toast("¡Hola desde el OS nativo!");
$coords = \Phphone\Device::gps(); // Retorna array ['lat' => x, 'lng' => y]
$fotoBase64 = \Phphone\Device::camera(); // Abre cámara nativa y retorna string Base64
\Phphone\Device::notification("Título", "Cuerpo del mensaje"); // Lanza Push Notification nativa
```

**Si modificas algo aquí dentro y la aplicación explota, nosotros te lo advertimos.** 🏔️⚡
