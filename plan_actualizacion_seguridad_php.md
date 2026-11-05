# 🛡️ Plan de Actualización de Seguridad: PHP 8.4.27
> **Objetivo:** Mitigar las vulnerabilidades críticas **CVE-2026-109618** y **CVE-2026-109619** en el runtime embebido de Phphone.  
> **Fecha programada de ejecución:** Fin de semana.  
> **Versión objetivo:** PHP 8.4.27 (Parche oficial de emergencia).

---

## 📌 1. Contexto y Análisis de Vulnerabilidad

Se han divulgado dos vulnerabilidades críticas en el núcleo del motor Zend de PHP:

* **CVE-2026-109618 & CVE-2026-109619:** Permiten forzar agotamiento súbito de memoria (Out-Of-Memory) y reutilización de memoria sucia (*dirty memory reuse*) entre ciclos de ejecución. En servidores expuestos a red esto facilita ejecución remota de código (RCE) no autenticada.
* **Impacto en Phphone:**
  * Aunque las apps móviles generadas por Phphone ejecutan PHP localmente (`127.0.0.1` / JNI C++) y no como servidores públicos de internet, un payload malicioso procesado por el WebView o peticiones externas podría causar corrupción de memoria o cierre forzoso (*crash*).
  * **Riesgo en Tiendas (Play Store / App Store):** Los escáneres estáticos de seguridad (SAST) de Google y Apple detectan firmas y símbolos de versiones vulnerables en librerías nativas (`libphp.so` / `libphp.a`), con riesgo de rechazo de compilaciones o advertencias de seguridad para los desarrolladores finales.

---

## 📋 2. Tareas a Ejecutar (Paso a Paso)

### 🔹 Fase 1: Actualización de Scripts y Fórmulas de Construcción
Actualizar la versión fuente en todos los scripts del constructor:

1. **Android (WSL2):**  
   Archivo: `constructor/android/build_php_wsl.sh`  
   ```bash
   - export PHP_VERSION=8.4.0
   + export PHP_VERSION=8.4.27
   ```
2. **Android (Dockerfile):**  
   Archivo: `constructor/android/Dockerfile`  
   ```dockerfile
   - ENV PHP_VERSION 8.4.0
   + ENV PHP_VERSION 8.4.27
   ```
3. **iOS (macOS):**  
   Archivo: `constructor/ios/build_php_ios.sh`  
   ```bash
   - PHP_VERSION="8.4.0"
   + PHP_VERSION="8.4.27"
   ```

---

### 🔹 Fase 2: Recompilación Cruzada para Android (WSL2 / Linux NDK)
Recompilar las librerías dinámicas compartidas con NDK r26d y API 24:

1. Abrir terminal en WSL2 Ubuntu.
2. Navegar a la carpeta constructora:
   ```bash
   cd /mnt/e/Escritorio/phphone/phphone-compiler/constructor/android
   bash build_php_wsl.sh
   ```
3. **Verificaciones críticas durante la compilación:**
   * Descarga correcta del tarball oficial `php-8.4.27.tar.xz`.
   * Flags activos: `--enable-embed=shared`, OpenSSL, cURL, SQLite3.
   * Target: `aarch64-linux-android` (API 24 para compatibilidad universal Android 7.0+).
4. **Copia de artefactos generados:**
   * Validar que el nuevo `.libs/libphp.so` se copie en:  
     `android/app/src/main/cpp/php/lib/arm64-v8a/libphp.so`
   * Actualizar las cabeceras C (`Zend/`, `main/`, `TSRM/`, `ext/`) en `android/app/src/main/cpp/php/include/` si hubo cambios en firmas de la versión.

---

### 🔹 Fase 3: Recompilación Cruzada para iOS (macOS / Xcode)
Generar la librería estática y framework unificado para iOS:

1. En el entorno macOS de compilación:
   ```bash
   cd constructor/ios
   bash build_php_ios.sh
   ```
2. Generar el binario estático `libphp.a` y empaquetar en `libphp.xcframework` (ARM64 dispositivo + x86_64 simulador).
3. Inyectar el framework actualizado en la plantilla base de iOS (`ios/`).

---

### 🔹 Fase 4: Pruebas de Integración y Validación
Comprobar que el motor parcheado bootee correctamente y no introduzca regresiones:

1. **Crear o compilar un proyecto de prueba:**
   ```bash
   phphone build apk --release
   ```
2. **Validación en dispositivo físico o emulador:**
   * Revisar en logs nativos (Logcat) que el Zend Engine inicie sin errores de enlace (`UnsatisfiedLinkError`).
   * Evaluar script de diagnóstico en PHP:
     ```php
     <?php
     echo "Versión PHP activa: " . phpversion(); // Debe retornar 8.4.27
     ```
   * Probar llamadas al hardware nativo mediante `Phphone\Device` (vibración, cámara, SQLite3).

---

### 🔹 Fase 5: Distribución y Sincronización
1. Copiar los binarios nativos actualizados a la plantilla de distribución pública (`phphone-cli`).
2. Actualizar `CHANGELOG.md` y `CHANGELOG.es.md` documentando la inclusión del parche de seguridad CVE-2026-109618 y CVE-2026-109619.
3. Ejecutar sincronización pública mediante `php cli/phphone sync:public`.

---

## 📌 Check de Seguridad y Paridad
* [ ] No alterar la firma de `src/Phphone/Device.php` (debe permanecer sin encriptar).
* [ ] Mantener paridad de claves AES-256 entre `BuildCommand.php` y los runtimes nativos.
* [ ] Confirmar compatibilidad con Android API 24 mínima para evitar cierres en dispositivos antiguos.
