# Assets Configuration (Setup)

This folder (`setup/`) is the official directory where you should place the "raw" or source graphic resources for your application. The `phphone setup` CLI command will automatically read these files to generate and inject the icons and splash screen into the native Android and iOS projects.

**Note:** None of these files will be directly included in the final device's public or web folder. They act purely as raw materials for native packaging.

---

## 1. App Icon

The application icon must be a static image. The CLI will automatically resize it and generate all the necessary formats (`mipmap` for Android and `.appiconset` for iOS).

- **Expected Name:** `icon.png`
- **Format:** `.png`
- **Dimensions:** 1024x1024 pixels (mandatory to prevent pixelation).
- **Transparency:** Avoid background transparency to ensure iOS compatibility (Apple does not allow icons with an alpha channel).

---

## 2. Splash Screen

The Splash Screen is the first screen the user sees while the Phphone engine boots up in the background. You can provide a static or animated format.

You should place **only one** of the following files in this folder. The CLI will detect which one you used and adapt the native code accordingly:

### Option A: Static Splash (Recommended for simplicity)
- **Expected Name:** `splash.png`
- **Description:** A traditional static image. It will be set as the background of the main native window (Android) and in the `LaunchScreen` (iOS).

### Option B: Lottie Animation (Recommended for modern fluidity)
- **Expected Name:** `splash.json`
- **Description:** An animated vector exported from After Effects / LottieFiles. The CLI will integrate the native Lottie player into Android/iOS to display your animation.

### Option C: GIF Animation
- **Expected Name:** `splash.gif`
- **Description:** A classic animated GIF.

---

## 3. How to apply the changes?

Once you have your files ready (for example, `icon.png` and `splash.png`) inside this folder, simply open your terminal at the root of the project and run:

```bash
phphone setup
```

The CLI will process the `setup/` folder, crop the icon, inject the splash screen, and leave the project ready for the next `phphone run` or `phphone build` command.
