<p align="center">
  <img src="https://raw.githubusercontent.com/stevenrojas888/phphone-cli/main/setup/icon.png" width="150" alt="Phphone Logo">
</p>

<div align="center">
  🌍 Languages: <strong>🇺🇸 English</strong> | <a href="README.es.md">🇪🇸 Español</a>
</div>

<h1 align="center">🐘 PHPHONE</h1>

<p align="center">
  <strong>The Vanilla Mobile Manifesto: Giving Code Its Freedom Back.</strong><br>
  Build high-performance hybrid mobile applications for Android and iOS using only <b>Vanilla PHP 8.4, HTML, CSS, and JavaScript</b>. No Flutter, no React Native, no Electron, and no heavy dependencies.
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

- **Phphone:** The global framework or compiler that encompasses the project.
- **Kie Engine:** The core engine written in C++ that contains the precompiled PHP binaries and the communication bridge. It is responsible for the existence of the `window.Kie` object in your JavaScript.
- **Dual WebView:** Native Phphone architecture that overlays two browsers: a transparent front one for your HTML/PHP app, and a back one for loading external websites.
- **KieBridge:** The direct communication channel between your JavaScript and the native operating system (Android/iOS).

---

<a id="why-phphone"></a>
## ⚡ The Problem with Modern Mobile Development

Modern mobile development is broken. It has been hijacked by the absurd complexity of bloated environments, fragile dependencies (NPM hell), and heavy architectures that force you to learn new languages or compile massive Electron/React Native binaries just to show a simple interface.

For years, a narrative was written where PHP belonged only to dark and distant servers. It was said that its cycle had ended against the shine of modern architectures, and that its destiny would never be in the palm of our hands.

**But true code is timeless.** Phphone is the beautiful poetry of seeing our old friend awaken in a new ecosystem. It is the technical demonstration that the simplicity, elegance, and absolute maturity of PHP can beat—with astonishing speed and lightness—directly in the heart of native hardware.

The language that built the web, now trumpets in your pocket.

## 🐘 Why PHP Still Rules the Web (And Now Mobile)

We've been hearing the same joke for 20 years: *"PHP is dead"*. Yet today, it powers over 70% of the entire web (WordPress, Wikipedia, the foundations of Facebook, Laravel). While JavaScript frameworks are born and die every 6 months leaving a trail of obsolete legacy code, PHP has matured in silence.

With PHP 8+, strict typing, JIT (Just In Time) compilation, and brutal execution speed, the elephant is faster and safer than ever.

**Why use it for mobile?**
Because it's ridiculously easy for a beginner to learn, yet delivers Enterprise-grade power in the hands of a Senior developer. You don't need to reinvent the wheel: the Composer ecosystem has decades of battle-tested code, free from the daily vulnerabilities of the *"NPM Hell"*. Now, all that indestructible stability leaves the cloud servers and installs directly into your users' pockets.

## 💥 If you can build it for the Web, you can ship it to Mobile.
**Without changing technologies. Without learning bloated frameworks.**

Say goodbye to heavy emulators and the absurd complexity of modern mobile development. **Flutter** forces you to learn Dart and fight an endless tree of Widgets. **React Native** drowns you in fragile NPM configs that break on every update. They are bloated, heavy, and over-engineered architectures.

**The Web, however, is eternal.** HTML from 1995 still renders perfectly today, while a 3-year-old Flutter app refuses to compile due to breaking changes. With Phphone, your visual layer never expires. You just update the engine to the latest PHP version, and your legacy code runs flawlessly like day one.

By unlocking the infinite JavaScript and CSS ecosystem, Phphone makes the current giants obsolete:

* **That 2D game you know how to code in HTML5 with Pixi.js?** 
  You no longer need to download 10 GB of Unity or learn C# to publish it on the Play Store. Phphone turns it into a native App in seconds.
* **That stunning 3D scene you made with Three.js?** 
  Render it at 60 FPS straight on the device, bypassing the notorious React Native bridge bottlenecks.
* **That CRM you can put together in 10 minutes with Bootstrap, Tailwind, or good old jQuery?** 
  Put it in your clients' pockets today. Stop stressing over UI layouts using Flutter's *Stateless Widgets*.
* **That sleek UI you already built in Vue or pure Web Components?** 
  Package it natively for iOS and Android without rewriting a single line of your visual code.

### Why is it NOT "just a wrapped website"?
Many developers assume that if you use a *WebView* for the graphical interface (HTML/CSS), your app is just a web browser disguised as an APK. **This is false in Phphone.**
While a traditional wrapped app requires complex logic to happen in the cloud, **Phphone injects the backend into your pocket**. The WebView is purely the "monitor glass"; the real magic happens behind the scenes where a native **C/C++** engine runs a real **PHP 8.4** interpreter and a **SQLite** database, all executing directly on your phone's CPU cores with deep access to native hardware.

<a id="features"></a>
## 🚀 Key Features

1. **The Phone as a Physical Server:** Phphone injects the full **PHP 8.4** interpreter compiled in C++ directly into the user's pocket. The mobile device spins up its own local server (internally routed through `http://127.0.0.1:8081`). Your app does not need the internet to function.
2. **Zero Configuration:** No Webpack, no Babel, no Node_modules. Just place your `index.php` file in the `src/` folder, and magically, you have an app on your phone.
3. **Zero-Hardcode Security (Bank-Grade):** PHP uses OpenSSL directly in RAM to encrypt your code. Your `.php` code is injected into the `.apk` or `.ipa` installer encrypted with **AES-256**. Reverse engineers will never see your source code, credentials, or API keys.
4. **Armored SQLite3 Built-in:** Encrypted local databases out-of-the-box. Even with Root access to the phone, stealing your users' data is impossible.
5. **Featherweight:** The core engine weighs less than 15 MB. It crushes the disproportionate RAM consumption of other hybrid solutions.
6. **🎁 Demo App Included (Under 20 MB):** Unlike the boring "counter" app of other ecosystems, your boilerplate includes a full **Hardware Testing Dashboard** (`index.php`) ready to test the flashlight, GPS, and camera, plus a visual gradient generator (`newgradient.php`). The best part? The PHP interpreter, SQLite, and this graphical demo app weigh **less than 20 MB** combined. For context: a blank "Hello World" app in React Native weighs ~35 MB and in Flutter ~25 MB. Phphone gives you a full backend ecosystem weighing significantly less.

---

## 🧩 Ecosystem & Dependencies (Technical Notice)

> [!NOTE]
> **The Vanilla Manifesto (Disclaimer)**
> *I built the core of Phphone based on my own programming philosophy: Vanilla, lightweight, and dependency-free. While the architectural goal is for any Web technology to work flawlessly (thanks to the embedded Chromium/WebKit engines), the immensity of the web is too vast for a single developer.* 
> *It is highly probable that some heavy libraries, hyper-experimental JS features, or aggressive routing frameworks might experience friction. Phphone is an "Open Core" project, which means if you find a JS library that doesn't render perfectly, I invite you to explore the engine, adapt your code, and share the solution with the community.*

Our core philosophy is **Vanilla PHP / HTML / JS / CSS**. However, Phphone's flexibility allows for much more:

- **Frontend Libraries & Web Components:** The native container runs a modern browser engine, so any visual framework (Vue, React, Tailwind, Bootstrap) will work flawlessly. We give a **special mention to Lit.js and native Web Components**, as they are the perfect match for Phphone's ultra-lightweight and "Vanilla" philosophy, allowing you to build complex interfaces without massive dependencies. You are completely free to use the architecture you are most comfortable with. *(Pro-Tip: If you want to use ultra-modern JS features and ensure compatibility with older Android devices, simply drop a JS Polyfill into your HTML, just like you would on a standard website).*
- **Routing & Relative Paths (Technical Data):** Even though the local server runs at `http://127.0.0.1:8081`, **you don't need to hardcode this IP in your code**. Write standard HTML/PHP using traditional relative paths (`<a href="/newgradient.php">`). The native container resolves them automatically, ensuring your code is 100% portable and fail-proof if the port changes.
- **Backend Libraries (Composer) & Heavy Frameworks:** You are free to use `composer` to pull in third-party packages. Thanks to the brutal efficiency of the C++ engine, **you can run full frameworks like Laravel or Symfony directly in the user's pocket**. Yes, Laravel embedded and processed locally on mobile, without external servers. This changes the rules of the game in the mobile ecosystem. **IMPORTANT:** You can only use dependencies written in **pure PHP**. If a package requires compiling C extensions on the host operating system, it **will not be compatible** with the embedded engine.

---

## 🔐 Security Best Practices (Must Read)

> [!WARNING]  
> **The Golden Rule of Mobile Security: Never Trust the Client**
> 
> Even though Phphone encrypts your source code (AES-256) to protect your Intellectual Property, **you must NEVER hardcode sensitive secrets** (like Database passwords, Stripe/AWS keys, or Master API Tokens) in your PHP code or `.env` files. 
>
> Mobile apps (built with Phphone, Flutter, Swift, etc.) can be decompiled by determined attackers, exposing any text strings inside.
>
> **Best Practice (Backend Proxy):** Your Phphone app should act only as a client. All sensitive operations (like charging a credit card or querying a private database) must be done by making HTTP requests to your own remote REST API, where your secrets are safely stored on a server you control. For local storage, standard SQLite saves data in plain text, so always encrypt sensitive user data before saving it using PHP's `openssl_encrypt`.

---

## 🌐 Background Native Browser (Dual WebView)

Suppose you are building your app with Phphone and you need to open an external web page (like Google or a payment gateway) *inside* your application. If you use a traditional `iframe`, you will run into security blocks (CORS, X-Frame-Options) and sites that simply refuse to load.

To solve this, Phphone includes a native browser hidden behind your HTML interface.

**How to use it? Follow this example:**

> 💡 **Note:** You do not need to install any SDK or import external libraries. The Phphone native engine automatically injects the `Kie` object (the communication bridge) invisibly into your JavaScript's global `window` object as soon as the app starts.

**Step 1: Add this function to your JavaScript**
Since Phphone is cross-platform, the way to talk to the native engine changes slightly between iOS and Android. Copy this *wrapper* into your code to unify it:

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

**Step 2: Open the page and adjust your interface**
When the user presses a button in your app, turn on the browser, give it a URL, and make sure the background of your HTML is transparent so you can see it:

```javascript
// 1. Turn on the background browser
callNativeBrowser('setBrowserActive', { active: true });

// 2. Load the page you want
callNativeBrowser('loadUrl', { url: 'https://google.com' });

// 3. Make your App background transparent (from CSS)
document.body.style.backgroundColor = 'transparent';
```

**Step 3 (Optional): Protect your menus**
If your app has a top navigation bar (Header) of `60px`, you won't want the native browser to draw underneath it. You can "push" it using margins:

```javascript
// Leave 60px free at the top and 0px at the bottom
callNativeBrowser('setBrowserMargins', { top: 60, bottom: 0 });
```

**Step 4 (Optional): Allow User Interaction (Touch & Scroll)**
Since your web application is now a transparent "glass" in front of the native browser, touches will be blocked. To pass touches through to the native browser, tell Phphone exactly in which rectangles your menus are located. Everything outside of them will be clickable in the background browser.

```javascript
// Pass an array with the coordinates of your UI elements
callNativeBrowser('setUiRects', { 
    rectsJson: JSON.stringify([
        { left: 0, top: 0, right: window.innerWidth, bottom: 60 }
    ]) 
});
```

Additionally, the native browser will notify you every time the user scrolls by emitting a `nativeScroll` event. You can listen to it to hide/show your menus dynamically:

```javascript
window.addEventListener('nativeScroll', (e) => {
    const dy = e.detail.dy; // Positive if scrolling down, Negative if scrolling up
    if (dy > 10) console.log("Hide Header");
});
```

> [!WARNING]
> When you are NOT using this background browser, make sure to turn it off (`active: false`) and keep your `<body>` tag's background a solid color (e.g. `background-color: white;`).

---

## 🎨 Design & Configuration Guide (UI/UX)

To make your Phphone applications feel truly native and not just like "wrapped websites", follow these key recommendations:

### 1. Device Configuration (Via CLI)
You can adjust the native behavior of your app directly from the terminal without touching Swift or Kotlin code:

*   **Screen Orientation:**
    ```bash
    phphone config orientation portrait  # Locks in portrait mode (e.g., Social Apps)
    phphone config orientation landscape # Locks in landscape mode (e.g., Games)
    phphone config orientation auto      # Allows rotation (Default)
    ```
*   **Pinch to Zoom:**
    For a 100% native feel, you usually want to prevent the user from zooming in on the graphical interface.
    ```bash
    phphone config zoom off  # Disables touch zoom (Recommended)
    phphone config zoom on   # Enables zoom (For accessibility)
    ```

### 2. Handling the "Notch" and Safe Areas (HTML/CSS)
Since Phphone spans 100% of the screen (edge-to-edge), you must ensure your interface isn't hidden behind the front camera notch or the bottom gesture bar.

**In your HTML `<head>`:**
Use `viewport-fit=cover` to allow CSS to calculate safe areas.
```html
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
```

**In your CSS file (`style.css`):**
Use the environment variables injected by the native engine to add margins to your Header and NavBar.
```css
.header {
    /* Dynamic padding to avoid the top camera/notch */
    padding-top: env(safe-area-inset-top, 20px);
}

.bottom-navbar {
    /* Bottom spacing to avoid the system gesture bar */
    padding-bottom: env(safe-area-inset-bottom, 20px);
}
```

### 3. CSS Tips for a Native Feel
Add this block to your main CSS file to polish user interaction:
```css
body {
    /* Prevents the user from accidentally selecting UI text on long press */
    -webkit-user-select: none;
    user-select: none;
    
    /* Removes the ugly gray highlight box that appears when tapping buttons on mobile */
    -webkit-tap-highlight-color: transparent;
    
    /* Prevents the elastic "overscroll" bounce when reaching the end of the app */
    overscroll-behavior-y: none; 
}
```

<a id="installation"></a>
## ⚙️ Prerequisites (Development Environment)

Since Phphone compiles true native applications, it is **mandatory** to have the operating systems' build tools installed on your computer. Without them, the CLI will not be able to compile your code.

- **PHP 8.0+** installed in your terminal (required to run the CLI).
- **For Android:** [Android Studio](https://developer.android.com/studio) installed (with the Android SDK and a configured Emulator).
- **For iOS:** A Mac with [Xcode](https://developer.apple.com/xcode/) installed (and Command Line Tools active).

---

## 🛠️ Installation and Usage (CLI)

Phphone includes a smart, global Command Line Interface (CLI).

### 1. Install the CLI (Requires local PHP)

**Option A: Automatic Global Installation (Recommended)**
Our installer scripts do all the heavy lifting for you (downloading, granting permissions, and adding it to your PATH).

- **Mac/Linux:**
  ```bash
  curl -sS https://phphone.org/install.sh | bash
  ```
- **Windows (PowerShell):**
  Open PowerShell as administrator and run:
  ```powershell
  irm https://phphone.org/install.ps1 | iex
  ```

**Option B: Local Installation (Via Composer)**
If you prefer not to install anything globally or want total portability, you can clone the repository and install dependencies locally:
```bash
git clone https://github.com/stevenrojas888/phphone.git my-store
cd my-store/cli
composer install
cd ..
# Instead of using the global 'phphone' command, you will use the local script:
php cli/bin/phphone run
```

### 2. Create a Project
```bash
phphone create "My Store" com.mystore.app
```
This will download the clean template and automatically configure the project, isolating the environment.

### 3. Run and Test (Hot Reload)
```bash
cd my-store
phphone run
```
It automatically detects if you have an Android or iOS emulator open, injects the app, and reloads your PHP/JS/CSS changes in real-time every time you save a file (Instant Hot Reload).

### 4. Customize Branding (Icon & Splash)
Place your desired app icon (`icon.png`) and splash screen image (`splash.png`) inside the `setup/` folder. Then run:
```bash
phphone setup
```
This automatically resizes and injects all native resolutions for iOS and Android.

### 5. Build for Production
```bash
phphone build apk --release
```
Generates the final package (APK/AAB/IPA) fully encrypted. 

### 6. Code Signing (Stores)
```bash
phphone sign --keystore my-release-key.jks
```
Signs your built release package so it is ready to be uploaded to Google Play or the App Store.

### 🧰 Full CLI Command Reference
Phphone's CLI provides a complete suite of tools for your development lifecycle:

| Command | Description |
| :--- | :--- |
| `create <name> <pkg>` | Bootstraps a new Phphone project |
| `run` | Compiles and launches the app with Hot Reload |
| `setup` | Injects your custom icon and splash screen |
| `build <target>` | Compiles the project (APK, AAB, IPA) |
| `sign` | Cryptographically signs the release packages |
| `rename` | Safely changes the app's name and package ID |
| `doctor` | Diagnoses missing SDKs or environment issues |
| `logs` | Streams native logs (Logcat/Console) in real-time |
| `devices` | Lists available physical devices and emulators |
| `screenshot` | Captures a screenshot from the connected device |
| `clean` | Clears the native build cache |
| `stop` | Stops the running app on the device |
| `uninstall` | Removes the app from the connected device |

---

<a id="apis"></a>
## 🔌 Available Native APIs (Phase 1.0)

Phphone comes with "batteries included". We have achieved basic feature parity with Flutter. You can access the physical hardware of the device directly from your PHP code by importing our `Device::` class:

```php
<?php
use Phphone\Device;

// No need for require_once! The C++ engine auto-injects this class globally.

// Take a picture with the native camera
$base64Image = Device::takeCameraPicture();

// Get precise GPS location
$location = Device::getGpsLocation();
echo $location['lat'] . ", " . $location['lng'];

// Save a token in the native secure keychain (Keychain / Keystore)
Device::secureWrite("api_token", "super_secret_token_123");
```

*(💡 **Pro Tip:** You can also create a PHP endpoint that executes these hardware methods and call it asynchronously from your JavaScript using `fetch()` or AJAX to build ultra-dynamic interfaces without reloading the page).*

**Supported out-of-the-box APIs (No third-party libraries needed):**
- 📸 Camera and Photo Gallery.
- 📍 GPS Geolocation.
- 📇 **High-Performance Contacts:** Asynchronous contact reading with lazy loading support for massive address books.
- 📲 **Real-Time Sensors:** Gyroscope and Accelerometer streaming at native frequency.
- ☁️ **Remote Push Notifications:** Firebase Cloud Messaging (FCM) foundation embedded (Latent).
- 💳 **In-App Purchases:** Native monetization bridge (StoreKit / Google Play Billing) included as latent code.
- 🎤 Audio Recording (Microphone) and Sound Playback.
- 📂 Native File Picker (iCloud / Android Storage).
- 🔔 Local Push Notifications.
- 🔐 Secure Keychain (Keychain / Keystore) and Biometrics (FaceID / Fingerprint).
- 🌐 In-App Browser (SafariViewController / Chrome Custom Tabs).
- 📤 Native Share Sheet (Share content to WhatsApp, Social Media, etc).
- 🔋 Battery Status, Network State, and Clipboard.
- 🔦 Flashlight and Haptics (Vibration).
- 👻 **Background Tasks (Daemons):** Run PHP scripts silently in the background (Android Foreground Services & iOS BGTaskScheduler).

### 👻 Background Tasks (Daemons)
Background tasks in mobile development are notoriously complex, but Phphone simplifies this by leveraging a hybrid architecture. Here is how the flow works:

1. **The Trigger (JS ➔ Native):** Your JavaScript frontend tells the native OS to start the task.
2. **The Loop (Native ➔ PHP):** The OS spawns an unkillable background thread (Android `ForegroundService` or iOS `BGTaskScheduler`) which makes invisible, periodic HTTP requests to your local PHP server.
3. **The Execution (PHP):** Your PHP script wakes up, executes the background logic, and goes back to sleep.

#### Setup A: Vanilla PHP (Default)
If you are building a standard Phphone app without complex routing, the native engine will ping `daemon.php` in your root directory by default.

**1. Start from JS:**
```javascript
callNativeBrowser('startDaemon', { taskName: 'sync_data', interval: 60 });
```

**2. Handle in PHP (`src/daemon.php`):**
```php
<?php
$task = $_GET['task'] ?? 'unknown';
// Your logic here (e.g. Sync SQLite, send push notification)
```

#### Setup B: Advanced Frameworks (Laravel, Symfony, etc.)
If you are running a full MVC framework inside Phphone, leaving a `daemon.php` file in the root breaks your routing architecture. Instead, you can pass a custom `endpoint` so the native OS pings your framework's router (e.g., `public/index.php`).

> ⚠️ **Framework Caveat:** Heavy frameworks like Laravel will work on Phphone, but because the Android APK filesystem is **Read-Only**, you MUST override their default storage paths (e.g., `storage/` or `var/cache/`) to point to the writable OS `data` directory, otherwise they will crash with a fatal error.
> 
> **Laravel Fix Example (`bootstrap/app.php`):**
> ```php
> $app = new Illuminate\Foundation\Application($_ENV['APP_BASE_PATH'] ?? dirname(__DIR__));
> // Redirect storage to the writable OS temp directory
> $app->useStoragePath(sys_get_temp_dir() . '/laravel_storage');
> ```

**1. Start from JS (Custom Endpoint):**
```javascript
callNativeBrowser('startDaemon', { 
    taskName: 'sync_data', 
    interval: 60,
    endpoint: '/api/background-tasks' // The native OS will ping this Laravel route!
});
```

**2. Handle in Laravel (`routes/api.php`):**
```php
Route::get('/background-tasks', function(Request $request) {
    if ($request->task === 'sync_data') {
        // Execute Eloquent models, queues, etc.
    }
});
```

### ⚠️ Embedded Engine Peculiarities (Avoiding Zend Bailout)
Since Phphone runs the PHP C++ core in a persistent shared-memory state (via NanoHTTPD/GCDWebServer) rather than tearing it down per-request like Apache, **fatal errors behave differently**. 

If the Zend Engine encounters an unrecoverable state, it triggers a **"Zend Bailout"** (a C-level `longjmp`), which will instantly crash the native Android/iOS application thread.

> [!CAUTION]
> **The Golden Rules for Phphone Backend:**
> 1. **NEVER use `exit;`, `exit();`, or `die();`**: These functions force a native Zend Bailout. Always use `return` or throw exceptions to halt code execution gracefully.
> 2. **Wrap your API endpoints in `try/catch`**: Prevent uncaught Exceptions or Fatal Errors (like missing functions or `TypeErrors`) from reaching the top of the interpreter. Catch them and return a standard JSON error to the frontend.
> 3. **Memory and Timeouts**: If processing massive amounts of data, use `set_time_limit(0);` to prevent the Zend timer from forcing a timeout bailout.

### 💾 Persistent Storage and SQLite (Read-Only Environments)
When compiling for Android, all files inside your project folder (and the PHP `__DIR__`) are packed as **Read-Only** inside the APK for security reasons.

If you try to have the SQLite driver open or create a database directly in `__DIR__`, the engine will crash. To avoid this and ensure persistence on both emulators and physical devices, **NEVER** use `__DIR__` to save SQLite data. Use the following official validation to route storage to the native `/files` directory:

```php
// Official Recommended Practice for SQLite in Phphone
function getDB() {
    // 1. Try to use local project path if we have write permissions (e.g., Dev Mode / Emulator)
    $dataDir = __DIR__ . '/../../data';
    
    // 2. If it is Read-Only (e.g., APK Production or iOS IPA), find the native path
    if (!is_writable(__DIR__)) {
        $temp = rtrim(sys_get_temp_dir(), '/\\');
        if (strpos($temp, 'cache') !== false) {
            $dataDir = dirname($temp) . '/files/app_data'; // Android Production
        } else {
            $dataDir = dirname($temp) . '/Documents/app_data'; // iOS Production or Fallback
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

### ⚠️ Network Limitations in Production (The POST Body Problem)
When you compile your application in **Release** mode (production APK with native encryption), Phphone routes traffic using `http://kie.local`. Due to a strict security restriction in the internal architecture of the **Android WebView**, network interceptions automatically destroy the `body` of **POST** HTTP requests.

**As a result:** AJAX or `fetch()` requests using the POST method will reach the PHP interpreter with `php://input` and `$_POST` completely empty.

**The Official Solution (Workaround):**
* To send data from JavaScript to PHP, **use GET requests**, packaging the payload as a JSON string URL-encoded in the URL.
* Example: `fetch('api.php?data=' + encodeURIComponent(JSON.stringify(payload)))`

---

<a id="support"></a>
## 💖 Support the Project (GitHub Sponsors)

Phphone is a massive project built independently (Open-Core with MIT License).

Currently, **I urgently need your support to get a Mac computer**. The entire iOS bridge (Swift) has been designed almost "blindly", and in order to guarantee updates, compile, test in Xcode, and keep Phphone alive in the Apple ecosystem, **the community's help is vital**.

If you believe in the vision of reviving PHP for the mobile era and want this tool to keep growing:
👉 **[Support me on GitHub Sponsors](https://github.com/sponsors/stevenrojas888)**

Your contribution (no matter how small) will help me keep this engine free, lightweight, and brutally efficient for all of us.

---

## 🌍 The Future: Phphone.org

We will soon launch our official portal **phphone.org**, where you will find:
- Extensive documentation and tutorials.
- **Premium Template Store:** Clones of WhatsApp, E-commerce, and CRMs ready to use, made purely with nativized HTML/PHP/CSS.
- A Showcase wall of published apps made with Phphone.

***

<p align="center">
  Made with 🐘 + ❤️ for the Web community.
</p>
