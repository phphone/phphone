# Phphone Compiler & Framework - AI Agent Guide

Welcome to the **Phphone** codebase! This document provides guidelines, architecture principles, security rules, and CLI conventions for AI coding assistants (Antigravity, Cursor, Claude Code, GitHub Copilot, Codex).

---

## 🎯 What is Phphone?

**Phphone** is a Next-Generation PHP-Native Hybrid Framework and Compiler for Android, iOS, and Desktop. It packages a full, embedded **PHP 8.4 runtime** into mobile applications, enabling developers to build native mobile applications using standard PHP, HTML5, CSS3, and JavaScript with direct hardware access.

---

## 🏗️ Core Architecture & Philosophy

### 1. Display vs. Embedded Backend
* **The WebView is only the display:** Unlike traditional web wrappers where the entire application logic runs on heavy client-side scripts, Phphone uses the system WebView solely as a graphic window.
* **True Business Logic:** Controllers, database operations (SQLite3), and API processing execute directly on the phone processor via the embedded PHP 8.4 engine.

### 2. Phphone Native Bridge™ (`Phphone\Device`)
* All hardware integrations are exposed via the `Phphone\Device` class (`src/Phphone/Device.php`).
* Key APIs include:
  - `Device::camera()` / `Device::pickImage()`
  - `Device::gps()` / `Device::startGyroscope()`
  - `Device::vibrate($ms)` / `Device::toast($message)`
  - `Device::notification($title, $message)`
  - `Device::authenticate()` (Face ID / Touch ID / Fingerprint)
  - `Device::secureWrite($key, $val)` / `Device::secureRead($key)`
  - `Device::getContacts()` / `Device::clipboard()`
  - `Device::recordAudioStart()` / `Device::recordAudioStop()`

### 3. Dual WebView Architecture
* Phphone maintains two overlapping WebViews:
  - **Front WebView:** Transparent primary browser rendering the local app UI.
  - **Rear WebView:** Secondary browser for rendering external web content without security blocks (CORS, X-Frame-Options).

---

## 🔐 Release Encryption & Security Rules

When building release binaries (`phphone build apk --release` / `phphone build aab --release`):

### 1. AES-256 Source Code Encryption
* Source files (`.php`, `.html`, `.css`, `.js`, `.env`, `.json`, `.yaml`) are cryptographically shielded with **AES-256-CBC**.
* Encrypted files start with the magic header `KIE_ENC:` followed by 16 bytes IV and ciphertext.
* Decryption occurs symmetrically in RAM inside the native layer (`MainActivity.kt` in Android / `ViewController.swift` in iOS).

### 2. Asset Exclusion List
To prevent RAM saturation and CPU overhead, heavy media assets are **explicitly excluded from AES encryption**:
* **Images:** `png`, `jpg`, `jpeg`, `gif`, `svg`, `webp`, `ico`
* **Audio & Video:** `mp3`, `wav`, `ogg`, `m4a`, `mp4`, `webm`, `avi`, `mov`
* **Fonts & Docs:** `ttf`, `otf`, `woff`, `woff2`, `pdf`, `txt`, `csv`, `md`
* **Databases:** `sqlite`, `sqlite3`, `db`
* **3D Models & Archives:** `obj`, `glb`, `gltf`, `fbx`, `stl`, `zip`, `tar`, `gz`, `rar`, `so`, `dll`

### 3. SDK Bridge Exemption (`Device.php`)
* **CRITICAL RULE:** `src/Phphone/Device.php` must **NEVER be encrypted**. It is copied in plain text to allow the native C++/JNI runtime to boot the bridge before loading the PHP wrapper.

---

## 🛠️ Main CLI Commands

* `phphone serve` - Launches local development server (default port 3000 with auto-increment).
* `phphone build apk --release` - Builds encrypted production APK binary for Android.
* `phphone build aab --release` - Builds encrypted AAB bundle for Google Play Store.
* `phphone run android --release` - Installs and runs release build on physical device/emulator.
* `phphone clean` - Clears build artifacts, Gradle cache, and temp files.
* `phphone doctor` - Environment diagnostic tool for Android SDK, Java, PHP, and dependencies.

---

## ⚠️ Guidelines & Pitfalls for AI Agents

1. **Do NOT edit `Device.php` signature without updating Native C++/Kotlin/Swift handlers.**
2. **Preserve exact 256-bit key parity** between `BuildCommand.php`, `KieSecrets.kt`, and `ViewController.swift`.
3. **Never encrypt media assets or binary files.**
4. **Never remove the `web/` landing page from sync exclusions** (`SyncPublicCommand.php` blacklist).
5. **Always verify compilation success** after modifying CLI commands or native Android/iOS bridge logic.
