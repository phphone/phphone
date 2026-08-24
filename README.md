# 📱 Phphone Compiler

<p align="center">
  <strong>Native Hybrid Framework & Compiler powering PHP 8.4 directly on Android & iOS devices.</strong><br><br>
  <a href="https://ko-fi.com/stivmaster" target="_blank"><img src="https://img.shields.io/badge/Ko--fi-Support_Phphone-ff5e5b?style=for-the-badge&logo=ko-fi&logoColor=white" alt="Support Phphone on Ko-fi"></a>
</p>

---

## 🚀 Overview

**Phphone** is an engine and compiler that allows developers to run high-performance PHP 8.4 directly on mobile devices (Android via C++ JNI and iOS via Swift bridging). It eliminates CORS issues by running local request interception and provides full native device API access (`Phphone\Device`).

---

## 🔔 Native Push Notifications Engine (Android & iOS)

Phphone features a **Unified Native Push Notification Engine**. Developers can control full native notification behavior (accumulation, overwriting, and bundling) straight from their PHP / Node.js backend JSON payload **without touching any Kotlin or Swift code**.

- **Seamless Single-Top User Experience:** Tapping a notification smoothly brings the existing app to the foreground on both Android and iOS without destroying the WebView or re-triggering the splash screen.

### 📋 Standard Payload Fields (`data` / `notification`)

| Field | Type | Description | Native Behavior |
| :--- | :--- | :--- | :--- |
| `title` | `string` | Notification title. | Displays bold title text. |
| `body` | `string` | Notification message body. | Main description text. |
| `tag` / `id` | `string` | *(Optional)* Unique replacement ID. | If present, **replaces/updates** previous notification with the same ID. If omitted, notifications **accumulate**. |
| `group` / `thread_id` | `string` | *(Optional)* Group key. | Bundles notifications together (Android `Group Summary`, iOS `threadIdentifier`). |
| `route` / `url` | `string` | *(Optional)* App navigation route. | Passed as an extra intent/userinfo parameter for in-app navigation on tap. |
| `reply` | `boolean` / `string` | *(Optional)* Direct reply (`true`). | Adds a native input text field and *"Reply"* button directly inside the notification. |

---

### 💡 Payload Examples (Backend / PHP)

#### Case 1: ACCUMULATING Notifications (e.g. Reminders / Alerts)
*Simply **omit** the `tag` parameter.* Each incoming notification will stack naturally in the status bar.

```json
{
  "notification": {
    "title": "Task Reminder",
    "body": "You have a meeting scheduled at 3:00 PM"
  }
}
```

#### Case 2: REPLACING / OVERWRITING Notifications (e.g. Order Tracking / Progress)
*Send a constant `tag` or `id`.* Notifications sharing the same `tag` will update in-place (e.g., updating from *"Preparing"* to *"On the way"*).

```json
{
  "notification": {
    "title": "Order Status #1052",
    "body": "Your order is now on the way! 🚚"
  },
  "data": {
    "tag": "order_1052"
  }
}
```

#### Case 3: BUNDLED / GROUPED Notifications like WhatsApp / Gmail (e.g. Chat Threads)
*Send multiple messages sharing the same `group` or `thread_id` (with distinct `tag`s) so they bundle together into an expandable container.*

**First Push (Message 1):**
```json
{
  "notification": {
    "title": "Alex Rivera",
    "body": "Hey, do you have 5 minutes?"
  },
  "data": {
    "group": "chat_alex_99",
    "tag": "msg_1001"
  }
}
```

**Second Push (Message 2, sent a few seconds later):**
```json
{
  "notification": {
    "title": "Alex Rivera",
    "body": "Did you check the project draft?"
  },
  "data": {
    "group": "chat_alex_99",
    "tag": "msg_1002"
  }
}
```

📱 **Device Outcome:**
Android and iOS automatically bundle both messages under a single expandable accordion header titled **"Alex Rivera (2 messages)"**, matching the exact behavior of WhatsApp, Telegram, or Gmail.

#### Case 4: DIRECT REPLY Notifications
*Send `"reply": true` inside the `data` object.* The native system will attach an inline text box and *"Reply"* button to the notification.

```json
{
  "notification": {
    "title": "Technical Support",
    "body": "Was your issue resolved?"
  },
  "data": {
    "reply": true,
    "tag": "ticket_501"
  }
}
```

💬 **Device Outcome:**
Users can tap **"Reply"** directly on the notification in Android or iOS, type their message (e.g. *"Yes, everything is working, thanks!"*), and send it without opening the app.

## 🔒 On-Demand Permission Management (Just-In-Time)

Phphone adheres to a **Strict Privacy & Just-In-Time (JIT) Permission Model**. Native permissions (Push Notifications, GPS, Camera, Microphone, Contacts, Storage, Biometrics) are never forced at startup. Instead, developers explicitly request permissions right when a feature requires it directly from PHP.

### 💡 Practical Example 1: Requesting Notification Permission Before Sending an Alert

```php
use Phphone\Device;

// Step 1: Prompt the native permission dialog (Android 13+ / iOS)
$granted = Device::requestNotificationPermission();

if ($granted) {
    // Step 2: Send notification if permission was granted
    Device::notification("Welcome!", "Thank you for enabling notifications");
} else {
    // Display a unobtrusive native toast if denied
    Device::toast("Notifications disabled by user");
}
```

### 💡 Practical Example 2: Capturing a Photo with Explicit or Auto-Prompting Permission

```php
use Phphone\Device;

// Option A: Explicit verification via requestPermission
if (Device::requestPermission('camera')) {
    $photoBase64 = Device::camera();
    if ($photoBase64) {
        Device::toast("Photo captured successfully");
    }
} else {
    Device::toast("Camera permission required");
}

// Option B: Auto-prompting (Device::camera() auto-prompts permission if not already granted)
$photoBase64 = Device::camera();
```

### 📋 Supported Permission Matrix (`Device::requestPermission($type)`)

| Type `$type` | Android Native Permission | iOS Native Framework | Description |
| :--- | :--- | :--- | :--- |
| `'notifications'` | `POST_NOTIFICATIONS` | `UNUserNotificationCenter` | Local & Push Notifications. |
| `'gps'` | `ACCESS_FINE_LOCATION` | `CoreLocation` | Current latitude/longitude. |
| `'camera'` | `CAMERA` | `AVFoundation` | Photo capture. |
| `'microphone'` | `RECORD_AUDIO` | `AVAudioSession` | Audio recording. |
| `'contacts'` | `READ_CONTACTS` | `Contacts` | Address book contacts. |
| `'storage'` | `READ_EXTERNAL_STORAGE` / Picker | `UIDocumentPicker` | Disk files and media. |
| `'biometric'` | `BiometricPrompt` | `LocalAuthentication` | Face ID, Touch ID & Fingerprint. |

---

## 🛠️ CLI Usage

```bash
# Create a new project
phphone create MyApp

# Run live development server with hot reload
phphone run

# Build release APK / App Bundle
phphone build apk --release
```

---

## 🚫 `.phphoneignore` File (Build & Packaging Optimization)

When using modern frontend tooling like **Vite, Webpack, TypeScript, Tailwind, or npm**, your workspace can accumulate hundreds of megabytes in `node_modules/`, raw `.ts` source files, and configuration files that are not needed inside the final mobile binary (`.apk`, `.aab`, `.ipa`).

To prevent slow compilation and keep builds lightweight, create a `.phphoneignore` file at your project root (generated automatically on `phphone create`):

```text
# Phphone Ignore Rules
# Files and directories excluded from APK/IPA encryption and packaging

# Dependencies & frontend tooling
node_modules/
package.json
package-lock.json
vite.config.*
tsconfig.json
.eslintrc*
.prettierrc*

# Unprocessed source files & raw assets
raw-assets/
src/src/
*.ts
*.tsx
*.scss

# System & log files
*.log
.DS_Store
Thumbs.db
```

The compiler and hot-reload engines (`phphone build` / `phphone run`) will automatically skip these files during scanning, AES-256 encryption, and device synchronization.

---

## 📄 License

Dual-licensed / Proprietary. See [LICENSE](LICENSE) for full details.
