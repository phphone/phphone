# Contributing to Phphone 🐘📱

¡Gracias por tu interés en contribuir a **Phphone**! Ya sea reportando un bug, proponiendo mejoras en la documentación o enviando código para nuevas características, toda ayuda es bienvenida para hacer de Phphone la mejor herramienta de desarrollo móvil para desarrolladores PHP.

---

## 🌍 Language / Idiomas
- [English](#-english-guidelines)
- [Español](#-guía-en-español)

---

<a id="-guía-en-español"></a>
## 🇪🇸 Guía de Contribución (Español)

### 1. 🌿 Convención de Ramas
Al trabajar en una mejora o corrección, crea tu rama a partir de `main` utilizando prefijos semánticos descriptivos:

- `feat/nombre-mejora` o `feature/nombre-mejora` (Nuevas funcionalidades o extensiones del CLI)
- `fix/descripcion-bug` (Corrección de errores o fallos)
- `docs/tema-documentacion` (Mejoras en la documentación o guías)
- `refactor/nombre-modulo` (Optimización o limpieza de código sin cambiar funcionalidad)

**Ejemplos:**
```bash
git checkout -b feat/fcm-badge-counter
git checkout -b fix/ios-notch-inset
git checkout -b docs/sqlite-examples
```

---

### 2. 📝 Convención de Commits
Utilizamos el estándar de **Conventional Commits**:
- `feat: agregar soporte para lector de código QR en Device::qrScanner()`
- `fix: corregir cálculo de safe areas en Android 14`
- `docs: añadir ejemplos de integración con Vite y TypeScript`
- `refactor: optimizar script de empaquetado phphone.phar`

---

### 3. ⚠️ Reglas de Oro del Proyecto
Antes de enviar un Pull Request, ten en cuenta estas directrices arquitectónicas críticas:

1. **No romper `Phphone\Device`:** Si agregas o modificas un método en `Device.php`, debes asegurarte de que el puente nativo en C++/JNI (`MainActivity.kt` en Android y `ViewController.swift` en iOS) tenga el manejador correspondiente.
2. **`Device.php` nunca se encripta:** En los flujos de build de producción, `Device.php` debe permanecer en texto plano para que el runtime C++ pueda inicializar el puente antes de cargar el resto de scripts.
3. **Evitar Zend Bailouts:** En código PHP de runtime/boilerplate, nunca uses `exit;` ni `die();`. Utiliza `return` o excepciones dentro de `try/catch`.
4. **Cero Bloatware:** Phphone defiende la filosofía **Vanilla**. Mantén el core ligero, rápido y libre de dependencias pesadas innecesarias.

---

### 4. 🚀 Flujo de Trabajo para Pull Requests (PR)

1. **Haz un Fork** del repositorio oficial: `https://github.com/phphone/phphone`.
2. **Clona tu Fork** en tu máquina local:
   ```bash
   git clone https://github.com/TU-USUARIO/phphone.git
   cd phphone
   ```
3. **Crea tu rama de trabajo:**
   ```bash
   git checkout -b feat/mi-nueva-funcionalidad
   ```
4. **Realiza tus cambios y pruébalos localmente:**
   - Verifica que `phphone serve` o `phphone run` funcionen correctamente.
5. **Haz Commit y Push a tu Fork:**
   ```bash
   git add .
   git commit -m "feat: implementar nueva funcionalidad X"
   git push origin feat/mi-nueva-funcionalidad
   ```
6. **Abre el Pull Request (PR):**
   - Ve a `https://github.com/phphone/phphone/pulls` y pulsa **New Pull Request**.
   - Describe con claridad el problema que resuelves y los cambios realizados.
   - Adjunta capturas o logs de prueba si aplica.

---

<a id="-english-guidelines"></a>
## 🇺🇸 Contribution Guidelines (English)

### 1. 🌿 Branch Naming Convention
Always branch out from `main` using descriptive semantic prefixes:

- `feat/feature-name` or `feature/feature-name` (New features or CLI enhancements)
- `fix/bug-description` (Bug fixes and patches)
- `docs/doc-topic` (Documentation, guides, and translation improvements)
- `refactor/module-name` (Code refactoring without altering public API behavior)

**Examples:**
```bash
git checkout -b feat/biometric-prompt-custom-title
git checkout -b fix/cli-doctor-java-home-path
git checkout -b docs/add-lit-js-guide
```

---

### 2. 📝 Commit Message Convention
We adhere to **Conventional Commits**:
- `feat: add gyroscope listener stream in Device API`
- `fix: resolve sqlite lock contention in release mode`
- `docs: update translation parity for iOS packaging`
- `refactor: clean up PHAR build script exclusions`

---

### 3. ⚠️ Critical Architecture Rules
Keep these foundational constraints in mind:

1. **`Phphone\Device` Parity:** Any signature modifications in `Device.php` must have corresponding native handlers implemented in Android (Kotlin/C++) and iOS (Swift).
2. **`Device.php` Exemption:** `Device.php` must **never be AES-encrypted** in release builds so the native bridge can boot seamlessly.
3. **Prevent Zend Bailouts:** Never invoke `exit;` or `die();` in embedded PHP code, as it triggers an unrecoverable process crash on mobile. Use `return` or `try/catch`.
4. **Vanilla First:** Avoid injecting heavyweight 3rd-party dependencies into the core engine.

---

### 4. 🚀 Pull Request Workflow

1. **Fork** the official repository: `https://github.com/phphone/phphone`.
2. **Clone your fork** locally:
   ```bash
   git clone https://github.com/YOUR-USERNAME/phphone.git
   cd phphone
   ```
3. **Create your feature branch:**
   ```bash
   git checkout -b feat/my-awesome-feature
   ```
4. **Implement changes and test locally** (`phphone run` / `phphone serve`).
5. **Commit and push:**
   ```bash
   git add .
   git commit -m "feat: add support for XYZ"
   git push origin feat/my-awesome-feature
   ```
6. **Open a Pull Request (PR)** on `https://github.com/phphone/phphone/pulls` with a clear explanation of what was added or resolved.

---

### 💖 Code of Conduct & Gratitude
Phphone is built with care, passion, and respect for developers worldwide. Be kind, constructive, and open-minded in discussions. All merged contributions will receive official contributor attribution in our community!
