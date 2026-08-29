<p align="center">
  <img src="https://raw.githubusercontent.com/stevenrojas888/phphone-cli/main/setup/icon.png" width="150" alt="Phphone Logo">
</p>

<div align="center">
  🌍 Languages: <strong>🇺🇸 English</strong> | <a href="README.es.md">🇪🇸 Español</a>
</div>

<h1 align="center">🐘 PHPHONE</h1>

<p align="center">
  <strong>The Vanilla Mobile Manifesto: Bringing Freedom Back to Code.</strong><br>
  Build high-performance hybrid mobile applications for Android and iOS using solely <b>Vanilla PHP 8.4, HTML, CSS, and JavaScript</b>. No Flutter, no React Native, no Electron, and zero bloated dependencies.<br><br>
  <a href="https://packagist.org/packages/phphone/phphone" target="_blank"><img src="https://img.shields.io/packagist/v/phphone/phphone?color=6366F1&label=Packagist&logo=composer" alt="Packagist Version"></a>
  <a href="https://packagist.org/packages/phphone/phphone" target="_blank"><img src="https://img.shields.io/packagist/dt/phphone/phphone?color=10B981&label=Downloads" alt="Total Downloads"></a>
  <a href="https://ko-fi.com/stivmaster" target="_blank"><img src="https://img.shields.io/badge/Ko--fi-Support_Phphone-ff5e5b?logo=ko-fi&logoColor=white" alt="Support Phphone on Ko-fi"></a>
</p>

<p align="center">
  <a href="#why-phphone">Why Phphone</a> •
  <a href="#features">Features</a> •
  <a href="#installation">Installation</a> •
  <a href="#apis">Native APIs</a> •
  <a href="#support">💖 Support the Project</a>
</p>

---

## 📖 Glossary

- **Phphone:** The overarching framework and compiler powering the ecosystem.
- **Kie Engine (Motor Kie):** The core C++ engine hosting precompiled PHP binaries and native communication bridge. It injects the global `window.Kie` object into JavaScript runtime.
- **Dual WebView:** Phphone's native architecture layering two overlapping browsers: a front transparent browser for your HTML/PHP app, and a rear browser for loading external web pages without security blocks.
- **KieBridge:** The direct communication channel between JavaScript and the native mobile operating system (Android/iOS).

---

<a id="why-phphone"></a>
## ⚡ The Crisis of Modern Mobile Development

Modern mobile development is broken. It has been hijacked by the absurd complexity of oversized environments, fragile dependency trees (NPM hell), and bloated architectures that force you to learn new languages or compile massive Electron/React Native binaries just to display a simple interface.

For years, a narrative was written claiming that PHP belonged strictly to dark, distant servers. It was declared that its era had ended under the glamour of modern architectures, and that its destiny would never reside in the palm of our hands.

**Yet true code is timeless.** Phphone is the beautiful poetry of witnessing our old companion awake inside a brand new ecosystem. It is technical proof that the simplicity, elegance, and absolute maturity of PHP can beat—with astonishing speed and featherweight efficiency—directly inside native hardware.

The language that built the web now trumpets proudly in your pocket.

## 🐘 Why PHP Still Rules the Web (And Now Mobile)

We have heard the same joke for 20 years: *"PHP is dead"*. Yet today it powers over 70% of the worldwide web (WordPress, Wikipedia, the foundations of Facebook, Laravel). While JavaScript frameworks are born and die every 6 months leaving a trail of deprecated code, PHP has matured in silence.

With PHP 8+, strict typing, JIT (Just-In-Time) compilation, and blistering execution speed, the elephant is faster and safer than ever.

**Why use it on mobile?**
Because it is remarkably simple to learn for a beginner, yet possesses enterprise-grade power in the hands of a Senior engineer. You don't need to reinvent the wheel: the Composer ecosystem has decades of battle-tested code free from the daily security chaos of *"NPM Hell"*. Now, all that indestructible stability steps out of remote cloud servers and lands directly inside your users' pockets.

## 💥 If You Can Build It for the Web, You Can Ship It to Mobile.
**Without changing technologies. Without learning bloated frameworks.**

Say goodbye to bloated emulators and the overwhelming complexity of today's mobile landscape. **Flutter** forces you into Dart and memorizing endless widget trees. **React Native** drowns you in brittle NPM setups that break on every update. They are greasy, heavy, and over-engineered architectures.

**The Web, on the other hand, is eternal.** HTML written in 1995 still renders flawlessly today, whereas a Flutter app from 3 years ago often refuses to compile due to breaking ecosystem shifts. With Phphone, your visual layer never expires. You simply upgrade the engine to the latest PHP runtime and your legacy code runs as crisp as day one.

By unlocking the infinite universe of JavaScript and CSS, Phphone obsoletes today's giants:

* **That 2D HTML5 game you built with Pixi.js?**
  No need to download 10 GB of Unity or learn C# to publish on Google Play. Phphone turns it into a native app in seconds.
* **That spectacular 3D interactive scene created with Three.js?**
  Render it at smooth 60 FPS directly on the phone's GPU without suffering React Native bridge bottlenecks.
* **That enterprise dashboard you assemble in 10 minutes with Bootstrap, Tailwind, or jQuery?**
  Deliver it to your clients' pockets today. No more stress wrestling with Flutter's *StatelessWidgets*.
* **That fluid UI you already perfected in Vue or pure Web Components?**
  Package it natively without rewriting a single comma of your visual code.

### Why is this NOT "just a wrapped website"?
Many developers mistakenly assume that using a *WebView* for the graphical layer (HTML/CSS) makes an app merely a browser disguised as an APK. **This is completely false in Phphone.**
While traditional web wrappers require heavy server logic to execute in the cloud, **Phphone injects the backend into your pocket**. The WebView is solely the "monitor screen"; the true magic happens behind the scenes where a high-performance **C/C++** engine executes a genuine **PHP 8.4 interpreter** and an embedded **SQLite3** database directly on the device's CPU cores with direct native hardware access.

<a id="features"></a>
## 🚀 Key Features

1. **The Phone as a Physical Server:** Phphone injects a full **PHP 8.4** interpreter compiled in C++ directly into the client device. The phone spins up its own local server (internally routed through `http://127.0.0.1:8081`). Your application requires zero internet connection to execute.
2. **Zero Configuration:** No Webpack, no Babel, no node_modules. Drop your `index.php` into the `src/` directory and you immediately have a working mobile application.
3. **Zero-Hardcode Banking-Grade Security:** PHP leverages OpenSSL directly in RAM to encrypt your source code. Your `.php` files are injected into `.apk` or `.ipa` packages shielded with **AES-256**. Reverse engineers will never view your proprietary business logic, credentials, or API keys.
4. **Armored Integrated SQLite3:** Encrypted local database engine. Even with root access to the physical device, attackers cannot read your users' private data.
5. **Featherweight Footprint:** The base engine weighs less than 15 MB, crushing the excessive RAM footprint of alternative hybrid solutions.
6. **🎁 Included Hardware Test Demo App (<20 MB):** Unlike boring "counter" starter apps, your default boilerplate includes a complete **Hardware Diagnostic Dashboard** (`index.php`) ready to test the flashlight, GPS, and camera, plus a live CSS gradient generator (`newgradient.php`). The entire package—PHP runtime, SQLite, and demo app—weighs **less than 20 MB**. For context: a blank React Native Hello World weighs ~35 MB, and Flutter ~25 MB. Phphone delivers a full backend runtime in a fraction of that size.

---

## 🧩 Ecosystem & Dependencies (Technical Notice)

> [!NOTE]
> **The Vanilla Manifesto (Responsibility Notice)**
> *I built Phphone's core based on my personal engineering philosophy: Vanilla, lightweight, and dependency-free. While our architectural goal is 100% web compatibility (thanks to modern embedded Chromium/WebKit engines), the sheer scale of the web cannot be tackled by a solo developer.*
> *Heavy libraries, bleeding-edge experimental JS features, or aggressive client-side routers may occasionally encounter friction. Phphone is an "Open Core" project: if you encounter a JS library that misbehaves, I encourage you to inspect the engine, adapt your code, and share your solution with the community.*

Our core philosophy is **Vanilla PHP / HTML / JS / CSS**. However, Phphone's architectural flexibility offers tremendous breadth:

- **Frontend Libraries & Web Components:** The native shell runs modern browser engines, allowing any visual framework (Vue, React, Tailwind, Bootstrap) to run seamlessly. We give a **special spotlight to Lit.js and native Web Components**, as they represent the perfect match for Phphone's lightweight Vanilla philosophy, enabling modular interfaces without dependency bloat. *(Pro-Tip: If you wish to leverage ultra-modern JS APIs on older Android devices, simply inject standard polyfills into your HTML just as you would on a traditional website).*
- **Routing & Relative Paths:** While the local backend listens on `http://127.0.0.1:8081`, **you never need to hardcode this IP in your markup**. Write HTML/PHP using standard relative paths (`<a href="/newgradient.php">`). The native container resolves them automatically, ensuring your codebase remains 100% portable if ports change dynamically.
- **Backend Packages (Composer) & Full Frameworks:** You are free to use `composer` for third-party packages. Thanks to the raw efficiency of the C++ runtime, **you can run complete frameworks like Laravel or Symfony directly inside the user's mobile device**. Yes, embedded local Laravel execution with zero remote servers. **CRITICAL REQUIREMENT:** Packages must be written in **pure PHP**. Dependencies requiring native C system compilation are **not compatible** with the embedded runtime.

### 🛠️ Modern Bundlers Pipeline (TypeScript, React, Vue, Vite) & `.phphoneignore`
If you use modern frontend toolchains requiring prior compilation (such as TypeScript, Vite, Tailwind CLI, or Webpack), the workflow is seamless:

1. **Build Your Frontend:** Write your source code in TypeScript (`.ts`) or components and execute your standard build step (e.g. `npm run build` or `npx tsc`) to emit final `.js` and `.css` bundles inside your project assets directory (e.g., `src/js/`).
2. **Exclude PC Development Bloat with `.phphoneignore`:** Your project comes out of the box with a preconfigured `.phphoneignore` in the root directory to instruct the compiler which files to strip before packaging mobile binaries:
   ```text
   # .phphoneignore
   node_modules/
   package.json
   package-lock.json
   tsconfig.json
   vite.config.js
   src_ts/
   tests/
   .git/
   ```
3. **Run or Build:** When executing `phphone run` or `phphone build apk --release`, the compiler reads `.phphoneignore`, discards development overhead, and exclusively packages your clean JavaScript and PHP backend runtime. This keeps **your final production app under 20 MB**.

---

## 🔐 Security Best Practices (Mandatory Reading)

> [!WARNING]  
> **The Golden Rule of Mobile Security: Never Trust the Client**
> 
> Although Phphone symmetrically encrypts source code (AES-256) to protect intellectual property, **you must NEVER hardcode sensitive master secrets** (such as database administrative passwords, Stripe/AWS secret keys, or Master Auth Tokens) inside your PHP code or `.env` files.
>
> Mobile binaries (whether built with Phphone, Flutter, Swift, or Kotlin) can be decompiled by determined attackers, exposing any hardcoded strings.
>
> **Best Practice (Backend Proxy):** Your Phphone client should act as a secure consumer. Critical operations (like charging credit cards or querying private company servers) must be dispatched via HTTPS requests to your remote REST API where credentials remain secure. For local storage, always encrypt sensitive user data via PHP's `openssl_encrypt` before persisting it to SQLite.

---

## 🌐 Background Native Browser (Dual WebView)

Imagine building your Phphone app and needing to load an external web service (such as Google or a checkout gateway) *inside* your application. Using traditional `iframe` tags triggers security blocks (CORS, X-Frame-Options) and causes pages to fail loading.

To solve this, Phphone provides an isolated native browser layered behind your transparent HTML interface.

**How to use it:**

> 💡 **Note:** No external SDK installation required. The native Phphone engine automatically injects the `Kie` bridge object into the global JavaScript `window` object upon startup.

**Step 1: Add this helper wrapper to your JavaScript**
Because Phphone is cross-platform, native dispatch syntax varies slightly between iOS and Android. Use this wrapper to unify calls:

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

**Step 2: Launch the browser and configure transparency**
When the user triggers an action, activate the browser, supply the target URL, and set your HTML body background to transparent:

```javascript
// 1. Activate background native browser
callNativeBrowser('setBrowserActive', { active: true });

// 2. Load target external URL
callNativeBrowser('loadUrl', { url: 'https://google.com' });

// 3. Make App background transparent in CSS
document.body.style.backgroundColor = 'transparent';
```

**Step 3 (Optional): Protect your headers and navigation bars**
If your app features a `60px` top header, you don't want the native browser rendering underneath it. Inset it using margins:

```javascript
// Reserve 60px on top and 0px on bottom
callNativeBrowser('setBrowserMargins', { top: 60, bottom: 0 });
```

**Step 4 (Optional): Enable touch passthrough and scroll listening**
Because your web app acts as a transparent window over the native browser, touches may be intercepted. Provide Phphone with the exact bounding rectangles of your interactive UI elements. Any area outside these rectangles passes touch events directly through to the background browser.

```javascript
// Pass bounding rects of your active UI components
callNativeBrowser('setUiRects', { 
    rectsJson: JSON.stringify([
        { left: 0, top: 0, right: window.innerWidth, bottom: 60 }
    ]) 
});
```

Additionally, the native browser broadcasts a `nativeScroll` event on scroll, allowing dynamic menu transitions:

```javascript
window.addEventListener('nativeScroll', (e) => {
    const dy = e.detail.dy; // Positive on scroll down, Negative on scroll up
    if (dy > 10) console.log("Hide Header");
});
```

> [!WARNING]
> When the background browser is inactive, make sure to deactivate it (`active: false`) and restore a solid background color on your `<body>` (e.g. `background-color: white;`).

---

## 🔒 Dynamic Just-In-Time Permission Handling

Phphone embraces **Just-In-Time (JIT) Minimal Permissions**. Apps created with Phphone never prompt users with overwhelming permission dialogs on launch; permissions are requested strictly when the developer or user triggers the relevant hardware feature in PHP.

### 💡 Example 1: Requesting Notification Permission Before Dispatching

```php
use Phphone\Device;

// Step 1: Request native permission (Android 13+ / iOS)
$permissionGranted = Device::requestNotificationPermission();

if ($permissionGranted) {
    // Step 2: Dispatch notification upon approval
    Device::notification("Welcome!", "Thank you for enabling push alerts");
} else {
    // If denied, display a non-intrusive native toast
    Device::toast("Notifications disabled by user preference");
}
```

### 💡 Example 2: Capturing a Photo with Explicit or Auto Permissions

```php
use Phphone\Device;

// Option A: Explicit pre-verification
if (Device::requestPermission('camera')) {
    $photoBase64 = Device::camera();
    if ($photoBase64) {
        Device::toast("Photo captured successfully");
    }
} else {
    Device::toast("Camera permission required");
}

// Option B: Direct call (Device::camera() auto-prompts permissions if not yet granted)
$photoBase64 = Device::camera();
```

### 📋 Supported Permission Matrix (`Device::requestPermission($type)`)

| Type `$type` | Android Native Permission | iOS Native Framework | Description |
| :--- | :--- | :--- | :--- |
| `'notifications'` | `POST_NOTIFICATIONS` | `UNUserNotificationCenter` | Local & Push notifications. |
| `'gps'` | `ACCESS_FINE_LOCATION` | `CoreLocation` | Precise GPS coordinates. |
| `'camera'` | `CAMERA` | `AVFoundation` | High-res camera capture. |
| `'microphone'` | `RECORD_AUDIO` | `AVAudioSession` | Audio recording. |
| `'contacts'` | `READ_CONTACTS` | `Contacts` | Native contacts address book. |
| `'storage'` | `READ_EXTERNAL_STORAGE` / Picker | `UIDocumentPicker` | Local file system and media. |
| `'biometric'` | `BiometricPrompt` | `LocalAuthentication` | Face ID, Touch ID & Fingerprint. |

---

## 🎨 UI/UX Design & Configuration Guide

To ensure your Phphone applications deliver authentic native feel rather than feeling like wrapped web pages, follow these key recommendations:

### 1. Device Configuration (Via CLI)
Configure native device behaviors directly from your terminal without editing Kotlin or Swift source files:

*   **Screen Orientation:**
    ```bash
    phphone config orientation portrait  # Lock to portrait (Social feeds, forms)
    phphone config orientation landscape # Lock to landscape (Games, video players)
    phphone config orientation auto      # Allow dynamic rotation (Default)
    ```
*   **Pinch-to-Zoom Control:**
    For a clean native feel, you will typically want to disable touch zooming on the interface.
    ```bash
    phphone config zoom off  # Disable pinch zoom (Recommended)
    phphone config zoom on   # Enable zoom (Accessibility support)
    ```

### 2. Handling Notches and Safe Areas (HTML/CSS)
Because Phphone renders edge-to-edge across the entire display, ensure your layout accounts for camera notches and system navigation bars.

**In your HTML `<head>`:**
Use `viewport-fit=cover` to allow CSS calculation of safe area insets.
```html
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
```

**In your stylesheet (`style.css`):**
Use CSS environment variables provided by the engine to pad headers and navigation bars:
```css
.header {
    /* Dynamic top padding protecting against camera notches */
    padding-top: env(safe-area-inset-top, 20px);
}

.bottom-navbar {
    /* Bottom padding protecting against system gesture bars */
    padding-bottom: env(safe-area-inset-bottom, 20px);
}
```

### 3. CSS Polish for True Native Feel
Add this base block to your stylesheet to enhance touch responsiveness:
```css
body {
    /* Prevent accidental text selection on long press */
    -webkit-user-select: none;
    user-select: none;
    
    /* Remove grey tap highlight box on mobile touch */
    -webkit-tap-highlight-color: transparent;
    
    /* Prevent elastic rubber-band overscroll bouncing */
    overscroll-behavior-y: none; 
}
```

### 4. 🔔 Native Push Notifications Engine (Firebase FCM)

Phphone includes out-of-the-box native support for Firebase Cloud Messaging (FCM) via `Phphone\Device::getPushToken()`.

> [!IMPORTANT]
> **Package ID Consistency:** When registering your application in Firebase Console, the *Android Package Name* or *iOS Bundle ID* must **exactly match** your Phphone project's package identifier. Otherwise, Gradle and Xcode will reject the configuration.
>
> 💡 **If your Firebase project uses a different package ID:**
> Modify your package identifier in:
> - **Android:** [`android/app/build.gradle.kts`](file:///android/app/build.gradle.kts) ➔ Update `applicationId = "com.yourcompany.yourapp"`
> - **iOS:** [`ios/project.yml`](file:///ios/project.yml) or `phphone_meta.json` ➔ Update `"bundleId": "com.yourcompany.yourapp"`

#### For Android:
1. In [Firebase Console](https://console.firebase.google.com/), register your app with your Phphone package ID (e.g. `com.phphone.myapp`).
2. Download the **`google-services.json`** file.
3. Save it into: `android/app/google-services.json`
4. The compiler detects it automatically upon build.

#### For iOS:
1. In Firebase Console, navigate to **Project Settings ⚙️** > **Your Apps** > **iOS App**.
2. Download the **`GoogleService-Info.plist`** file.
3. Save it into: `ios/App/GoogleService-Info.plist`

#### For Backend Servers / REST APIs:
1. In Firebase Console, go to **Project Settings ⚙️** > **Service Accounts** tab.
2. Click **Generate new private key** to download the Admin SDK JSON credential (`firebase-adminsdk-*.json`).
3. Store this file strictly on your production backend server (PHP/Node.js) to authenticate push dispatches.

#### 📌 Unified Native Notification Parser & Payload Specification (Android & iOS)

Phphone features a **Unified Native Push Notification Engine**. Developers can control full native notification behavior (accumulation, overwriting, and bundling) straight from their PHP / Node.js backend JSON payload **without touching any Kotlin or Swift code**:

- **Seamless Single-Top User Experience:** Tapping a notification smoothly brings the existing app to the foreground on both Android and iOS without destroying the WebView or re-triggering the splash screen.

##### 📋 Standard Payload Fields (`data` / `notification`)

| Field | Type | Description | Native Behavior |
| :--- | :--- | :--- | :--- |
| `title` | `string` | Notification title. | Displays bold title text. |
| `body` | `string` | Notification message body. | Main description text. |
| `tag` / `id` | `string` | *(Optional)* Unique replacement ID. | If present, **replaces/updates** previous notification with the same ID. If omitted, notifications **accumulate**. |
| `group` / `thread_id` | `string` | *(Optional)* Group key. | Bundles notifications together (Android `Group Summary`, iOS `threadIdentifier`). |
| `route` / `url` | `string` | *(Optional)* App navigation route. | Passed as an extra intent/userinfo parameter for in-app navigation on tap. |
| `reply` | `boolean` / `string` | *(Optional)* Direct reply (`true`). | Adds a native input text field and *"Reply"* button directly inside the notification. |

---

##### 💡 Backend Payload Examples (PHP / Node.js)

###### Case 1: ACCUMULATING Notifications (e.g. Reminders / Alerts)
*Simply **omit** the `tag` parameter.* Each incoming notification will stack naturally in the status bar.

```json
{
  "notification": {
    "title": "Task Reminder",
    "body": "You have a meeting scheduled at 3:00 PM"
  }
}
```

###### Case 2: REPLACING / OVERWRITING Notifications (e.g. Order Tracking / Progress)
*Send a constant `tag` or `id`.* Notifications sharing the same `tag` will update in-place (e.g., updating from *"Preparing"* to *"On the way"*).

```json
{
  "notification": {
    "title": "Order Status #1052",
    "body": "Your order is on the way 🚚"
  },
  "data": {
    "tag": "order_1052"
  }
}
```

###### Case 3: BUNDLED Notifications (WhatsApp / Gmail Style Chat Threads)
*Send multiple notifications sharing the same `group` or `thread_id` (with distinct `tag` values) to collapse them into an expandable summary.*

**Message 1:**
```json
{
  "notification": {
    "title": "Carlos Ramirez",
    "body": "Hey, do you have 5 minutes?"
  },
  "data": {
    "group": "chat_carlos_99",
    "tag": "msg_1001"
  }
}
```

**Message 2 (sent seconds later):**
```json
{
  "notification": {
    "title": "Carlos Ramirez",
    "body": "Did you review the project draft?"
  },
  "data": {
    "group": "chat_carlos_99",
    "tag": "msg_1002"
  }
}
```

📱 **Device Result:**
Android and iOS group both messages under **"Carlos Ramirez (2 messages)"**, allowing users to expand the stack or inspect messages individually.

###### Case 4: DIRECT REPLY Notifications
*Include `"reply": true` inside the `data` payload.* The native OS attaches an interactive text box and *"Reply"* action to the notification.

```json
{
  "notification": {
    "title": "Customer Support",
    "body": "Was your issue resolved?"
  },
  "data": {
    "reply": true,
    "tag": "ticket_501"
  }
}
```

💬 **Device Result:**
The user can tap **"Reply"** directly inside the Android or iOS notification shade, enter text, and dispatch the answer without opening the application.

<a id="installation"></a>
## ⚙️ Prerequisites (Development Environment)

Because Phphone compiles real native applications, native platform toolchains **must be installed** on your computer.

- **PHP 8.0+** installed in your terminal (required to run the CLI toolchain).
- **For Android:** [Android Studio](https://developer.android.com/studio) installed (with Android SDK & configured Emulator).
- **For iOS:** A Mac with [Xcode](https://developer.apple.com/xcode/) installed (and active Command Line Tools).

---

## 🛠️ Installation & CLI Usage

Phphone includes an intelligent global command-line interface (CLI).

### 1. Installing the CLI (Requires local PHP)

**Option A: Global Automated Installation (Recommended)**
Our installer scripts handle downloading, permissions, and PATH setup:

- **macOS / Linux:**
  ```bash
  curl -sS https://phphone.xyz/install.sh | bash
  ```
- **Windows (PowerShell):**
  Open PowerShell as Administrator and run:
  ```powershell
  irm https://phphone.xyz/install.ps1 | iex
  ```

**Option B: Via Composer (Official Packagist Package)**
If you prefer initializing projects through Composer directly:
```bash
composer create-project phphone/phphone my-store
cd my-store
# Run with the bundled local CLI runner:
php cli/bin/phphone run
```

**Option C: Manual Git Clone & Setup**
To isolate your environment manually from source:
```bash
git clone https://github.com/stevenrojas888/phphone.git my-store
cd my-store/cli
composer install
cd ..
# Use the local executable path instead of global 'phphone':
php cli/bin/phphone run
```

### 2. Create a Project
```bash
phphone create "My Store" com.mystore.app
```
This provisions a clean starter template and configures your project container.

### 3. Run & Test (Hot Reload)
```bash
cd my-store
phphone run
```
Automatically detects running Android/iOS emulators, boots the app, and performs instant Hot Reload of PHP, JavaScript, and CSS upon saving changes.

### 4. Brand Customization (Icons & Splash Screen)
Place your app icon (`icon.png`) and splash image (`splash.png`) inside the `setup/` directory. Then execute:
```bash
phphone setup
```
This automatically resizes and injects all required native asset densities for both Android and iOS.

### 5. Build for Production
```bash
phphone build apk --release
```
Generates the final cryptographically encrypted production package (APK/AAB/IPA).

### 6. Code Signing (App Stores)
```bash
phphone sign --keystore my-release-key.jks
```
Signs your release binary cryptographically, making it immediately ready for submission to Google Play Store or Apple App Store.

### 🧰 Complete CLI Command Reference

| Command | Description |
| :--- | :--- |
| `create <name> <pkg>` | Scaffolds a new Phphone project |
| `run` | Builds and boots the application with instant Hot Reload |
| `setup` | Generates and injects custom app icons and splash screens |
| `build <target>` | Compiles production binaries (APK, AAB, IPA) |
| `sign` | Signs release binaries for store distribution |
| `rename` | Safely updates application display name and package identifier |
| `doctor` | Diagnoses missing environment toolchains and dependencies |
| `logs` | Streams live native logs (Logcat / Console) in real time |
| `devices` | Lists connected physical hardware and emulators |
| `screenshot` | Captures high-res screen from connected device |
| `clean` | Purges native build artifacts and Gradle cache |
| `stop` | Terminates running app process on device |
| `uninstall` | Uninstalls app from target device |

---

<a id="apis"></a>
## 🔌 Native APIs Reference (Phase 1.0)

Phphone comes with "batteries included". You have full native hardware access directly from PHP via our static `Phphone\Device` facade:

```php
<?php
use Phphone\Device;

// No require_once needed! The C++ runtime registers this class globally.

// Capture a high-res photo from native camera
$base64Image = Device::takeCameraPicture();

// Query accurate GPS coordinates
$location = Device::getGpsLocation();
echo $location['lat'] . ", " . $location['lng'];

// Persist a secure secret to native Keychain / Keystore
Device::secureWrite("api_token", "super_secret_token_123");
```

*(💡 **Pro Tip:** You can also create PHP API endpoints that execute these hardware methods and consume them asynchronously from JavaScript using `fetch()` or AJAX for hyper-dynamic interfaces).*

**Out-of-the-box Supported Hardware APIs:**
- 📸 Native Camera & Photo Gallery Picker.
- 📍 GPS Geolocation coordinates.
- 📇 **Dynamic Address Book Contacts:** Asynchronous contact retrieval with pagination (lazy loading).
- 📲 **Real-time Motion Sensors:** High-frequency Gyroscope and Accelerometer streaming.
- ☁️ **Remote Push Notifications:** Built-in Firebase Cloud Messaging (FCM) integration.
- 💳 **In-App Purchases (IAP):** Native monetization gateways (StoreKit & Google Play Billing).
- 🎤 Native Microphone Audio Recording & Sound Playback.
- 📂 Native Document & File Picker (iCloud / Android Storage Access Framework).
- 🔔 Local Notification Dispatcher.
- 🔐 Secure Hardware Storage (Keychain / Keystore) & Biometrics (Face ID, Touch ID, Fingerprint).
- 🌐 Embedded In-App Browser (SafariViewController / Chrome Custom Tabs).
- 📤 Native Share Dialog (Social media, WhatsApp, system sheet).
- 🔋 Real-time Battery Status, Network Connectivity, and Clipboard access.
- 🔦 Flashlight Toggle and Haptic Vibration Feedback.
- 👻 **Background Workers (Daemons):** Silent background PHP execution (Android Foreground Services & iOS BGTaskScheduler).

### 👻 Background Workers (Daemons)
Handling background tasks in mobile applications is typically intricate. Phphone unifies this workflow:

1. **The Trigger (JS ➔ Native):** Your JavaScript frontend instructs the OS to spawn a background daemon.
2. **The Loop (Native ➔ PHP):** The OS maintains a background worker (`ForegroundService` in Android or `BGTaskScheduler` in iOS) that dispatches invisible HTTP queries to your local embedded PHP server every X seconds.
3. **The Execution (PHP):** Your PHP script wakes up, executes background logic, and returns to sleep.

#### Scenario A: Vanilla PHP (Default)
In standard Phphone apps, the native engine targets `src/daemon.php` in your root project directory.

**1. Trigger from JS:**
```javascript
callNativeBrowser('startDaemon', { taskName: 'sync_data', interval: 60 });
```

**2. Handle in PHP (`src/daemon.php`):**
```php
<?php
$task = $_GET['task'] ?? 'unknown';
// Business logic here (e.g. Sync local SQLite records with remote server)
```

#### Scenario B: Advanced Frameworks (Laravel, Symfony, etc.)
When embedding a full MVC framework inside Phphone, a root `daemon.php` breaks routing. Provide a custom `endpoint` so native background pings target your framework router (e.g. `public/index.php`):

> ⚠️ **Notice for Frameworks:** Heavy frameworks like Laravel run on Phphone, but because Android APK storage is **Read-Only**, you MUST redirect internal storage paths (e.g. `storage/` or `var/cache/`) to the OS writable `data` directory, otherwise the app will halt with a fatal permission error.
> 
> **Laravel Configuration Example (`bootstrap/app.php`):**
> ```php
> $app = new Illuminate\Foundation\Application($_ENV['APP_BASE_PATH'] ?? dirname(__DIR__));
> // Redirect storage path to writable OS temp directory
> $app->useStoragePath(sys_get_temp_dir() . '/laravel_storage');
> ```

**1. Trigger from JS (Custom Route Endpoint):**
```javascript
callNativeBrowser('startDaemon', { 
    taskName: 'sync_data', 
    interval: 60,
    endpoint: '/api/background-tasks' // Native OS pings this Laravel route!
});
```

**2. Handle in Laravel (`routes/api.php`):**
```php
Route::get('/background-tasks', function(Request $request) {
    if ($request->task === 'sync_data') {
        // Execute Eloquent models, queued jobs, etc.
    }
});
```

### ⚠️ Embedded Engine Nuances (Preventing Zend Bailouts)
Because Phphone maintains the C++ PHP Zend Core in persistent shared memory (via NanoHTTPD/GCDWebServer) rather than tearing down worker processes on every request like Apache, **fatal errors behave differently**.

If the Zend Engine encounters an unrecoverable state, it fires a **"Zend Bailout"** (a C-level `longjmp`), which terminates the native worker thread and forces the app to crash.

> [!CAUTION]
> **Golden Rules of Phphone Backend Development:**
> 1. **NEVER use `exit;`, `exit();`, or `die();`**: These calls force an immediate Zend Bailout crash. Always use `return` or throw exceptions to handle execution flow.
> 2. **Wrap API Handlers in `try/catch`**: Prevent uncaught exceptions or Fatal Errors (such as calling undefined functions or type mismatch errors) from reaching the top-level runtime. Catch them and return structured JSON errors to the frontend.
> 3. **Execution Limits**: When processing large data sets, use `set_time_limit(0);` to prevent internal Zend timeout interrupts.

### 💾 Persistent Storage & SQLite (Read-Only Environments)
When building Android APK packages, all files inside your project directory (and PHP's `__DIR__`) are packaged as **Read-Only** assets.

Attempting to open or initialize an SQLite database directly inside `__DIR__` will fail. To ensure persistence across emulators and physical devices, **NEVER** store SQLite databases in `__DIR__`. Use the official path resolution helper:

```php
// Official Recommended SQLite Resolution Pattern in Phphone
function getDB() {
    // 1. Attempt local project path if writable (e.g. Development / Hot Reload mode)
    $dataDir = __DIR__ . '/../../data';
    
    // 2. If Read-Only (Production APK or iOS IPA), resolve native OS storage
    if (!is_writable(__DIR__)) {
        $temp = rtrim(sys_get_temp_dir(), '/\\');
        if (strpos($temp, 'cache') !== false) {
            $dataDir = dirname($temp) . '/files/app_data'; // Android Production
        } else {
            $dataDir = dirname($temp) . '/Documents/app_data'; // iOS Production / Fallback
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

### ⚠️ Network Considerations in Production (POST Body Interception)
When your application runs in **Release Mode** (production APK with native source encryption), Phphone routes traffic through `http://kie.local`. Due to Android WebView security constraints, native network interception drops the HTTP request body on **POST** requests.

**As a result:** AJAX or `fetch()` calls using POST will reach the PHP runtime with empty `$_POST` and `php://input` streams.

**The Official Workaround:**
* Transmit data from JavaScript to PHP via **GET requests**, encoding JSON payloads directly into URL query parameters.
* Example: `fetch('api.php?data=' + encodeURIComponent(JSON.stringify(payload)))`

---

<a id="support"></a>
## 💖 Support the Project (GitHub Sponsors)

Phphone is an ambitious open-source project built independently (Open-Core with MIT License).

Currently, **I urgently need community support to acquire a Mac workstation**. The entire iOS Swift bridge has been developed almost "blindly". To ensure continuous updates, automated builds, Xcode testing, and sustained support for the Apple ecosystem, **community backing is vital**.

If you believe in the vision of revitalizing PHP for the mobile era:
👉 **[Sponsor on GitHub](https://github.com/sponsors/stevenrojas888)**

Every contribution, no matter how small, directly helps maintain this engine free, lightweight, and blazingly fast for everyone.

---

## 🌍 The Road Ahead: Phphone.org

We are preparing the official launch of **phphone.org**, featuring:
- Extensive guides, API documentation, and tutorials.
- **Premium UI Starter Kits:** Ready-to-use WhatsApp, E-commerce, and CRM templates built purely with native HTML/PHP/CSS.
- A Community Showcase gallery of published store apps powered by Phphone.

***

<p align="center">
  Crafted with 🐘 + ❤️ for the Global Web Community.
</p>
