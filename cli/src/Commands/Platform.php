<?php
namespace Phphone\Cli\Commands;

/**
 * Platform utility — detects OS and available devices.
 * All platform-specific logic lives here so commands stay clean.
 */
class Platform
{
    public static function isWindows(): bool
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    public static function isMac(): bool
    {
        return strtoupper(PHP_OS) === 'DARWIN';
    }

    public static function devNull(): string
    {
        return self::isWindows() ? 'NUL' : '/dev/null';
    }

    // ---------------------------------------------------------------
    // Android helpers
    // ---------------------------------------------------------------

    /** Returns list of connected Android device serials via adb. */
    public static function getAndroidDevices(): array
    {
        exec('adb devices 2>' . self::devNull(), $output);
        $devices = [];
        foreach ($output as $line) {
            if (preg_match('/^([a-zA-Z0-9_:.-]+)\s+device$/', $line, $m)) {
                $devices[] = $m[1];
            }
        }
        return $devices;
    }

    /** Returns list of available Android AVDs. */
    public static function getAvds(): array
    {
        $emulatorBin = self::resolveAndroidEmulatorBin();
        exec(escapeshellarg($emulatorBin) . ' -list-avds 2>' . self::devNull(), $avds, $rc);
        return ($rc === 0) ? array_filter($avds) : [];
    }

    public static function resolveAndroidEmulatorBin(): string
    {
        $sdkRoot = getenv('ANDROID_HOME') ?: getenv('ANDROID_SDK_ROOT') ?: '';

        if (empty($sdkRoot) && self::isWindows()) {
            $sdkRoot = getenv('LOCALAPPDATA') . '\Android\Sdk';
        }
        if (empty($sdkRoot)) {
            $sdkRoot = getenv('HOME') . '/Android/Sdk';
        }

        $ext = self::isWindows() ? '.exe' : '';
        $bin = rtrim($sdkRoot, '/\\') . DIRECTORY_SEPARATOR . 'emulator' . DIRECTORY_SEPARATOR . 'emulator' . $ext;

        return file_exists($bin) ? $bin : 'emulator';
    }

    // ---------------------------------------------------------------
    // iOS helpers (macOS only)
    // ---------------------------------------------------------------

    /**
     * Returns list of booted iOS Simulator UDIDs.
     * Only meaningful on macOS.
     */
    public static function getIosSimulators(): array
    {
        if (!self::isMac()) {
            return [];
        }
        exec('xcrun simctl list devices booted --json 2>' . self::devNull(), $jsonLines);
        $json = implode('', $jsonLines);
        $data = json_decode($json, true);
        if (!$data || empty($data['devices'])) {
            return [];
        }
        $booted = [];
        foreach ($data['devices'] as $runtimeDevices) {
            foreach ($runtimeDevices as $dev) {
                if (($dev['state'] ?? '') === 'Booted') {
                    $booted[] = $dev;
                }
            }
        }
        return $booted;
    }

    /**
     * Returns the first available (non-booted) iOS Simulator.
     */
    public static function getAvailableIosSimulators(): array
    {
        if (!self::isMac()) {
            return [];
        }
        exec('xcrun simctl list devices available --json 2>' . self::devNull(), $jsonLines);
        $json = implode('', $jsonLines);
        $data = json_decode($json, true);
        if (!$data || empty($data['devices'])) {
            return [];
        }
        $available = [];
        foreach ($data['devices'] as $runtime => $runtimeDevices) {
            foreach ($runtimeDevices as $dev) {
                $dev['_runtime'] = $runtime;
                $available[] = $dev;
            }
        }
        return $available;
    }

    // ---------------------------------------------------------------
    // Smart platform resolution
    // ---------------------------------------------------------------

    /**
     * Resolves which platform to use based on OS + connected devices + explicit flag.
     *
     * Returns: 'android' | 'ios' | 'ask'
     *
     * 'ask' means both platforms are available and the user must choose.
     */
    public static function resolvePlatform(array $args): string
    {
        // Explicit flags always win
        if (in_array('--ios', $args, true)) {
            return 'ios';
        }
        if (in_array('--android', $args, true)) {
            return 'android';
        }

        // On Windows → Android only
        if (self::isWindows()) {
            return 'android';
        }

        // On macOS → detect what's connected
        $hasAndroid = count(self::getAndroidDevices()) > 0;
        $hasIos     = count(self::getIosSimulators()) > 0;

        if ($hasIos && !$hasAndroid) {
            return 'ios';
        }
        if ($hasAndroid && !$hasIos) {
            return 'android';
        }
        if ($hasIos && $hasAndroid) {
            return 'ask';
        }

        // Nothing connected yet → default to ios on mac, android elsewhere
        return self::isMac() ? 'ios' : 'android';
    }

    // ---------------------------------------------------------------
    // Project config parsing
    // ---------------------------------------------------------------

    public static function getAndroidPackageName(string $projectRoot): string
    {
        $gradleKts = $projectRoot . '/android/app/build.gradle.kts';
        $gradleGroovy = $projectRoot . '/android/app/build.gradle';
        
        $content = '';
        if (file_exists($gradleKts)) {
            $content = file_get_contents($gradleKts);
        } elseif (file_exists($gradleGroovy)) {
            $content = file_get_contents($gradleGroovy);
        }

        if ($content && preg_match('/applicationId\s*=?\s*["\']([^"\']+)["\']/', $content, $matches)) {
            return $matches[1];
        }

        return 'com.example.phphone'; // Fallback
    }

    public static function getIosBundleId(string $projectRoot): string
    {
        $metaPath = $projectRoot . '/ios/phphone_meta.json';
        if (file_exists($metaPath)) {
            $meta = json_decode(file_get_contents($metaPath), true);
            if (!empty($meta['package_name'])) {
                return $meta['package_name'];
            }
        }

        $pbxproj = $projectRoot . '/ios/Phphone.xcodeproj/project.pbxproj';
        if (file_exists($pbxproj)) {
            $content = file_get_contents($pbxproj);
            if (preg_match('/PRODUCT_BUNDLE_IDENTIFIER\s*=\s*([a-zA-Z0-9\.]+)\s*;/', $content, $matches)) {
                return $matches[1];
            }
        }
        
        return 'com.example.phphone'; // Fallback
    }

    public static function getIosAppName(string $projectRoot): string
    {
        $metaPath = $projectRoot . '/ios/phphone_meta.json';
        if (file_exists($metaPath)) {
            $meta = json_decode(file_get_contents($metaPath), true);
            if (!empty($meta['app_name'])) {
                return $meta['app_name'];
            }
        }

        $pbxproj = $projectRoot . '/ios/Phphone.xcodeproj/project.pbxproj';
        if (file_exists($pbxproj)) {
            $content = file_get_contents($pbxproj);
            if (preg_match('/PRODUCT_NAME\s*=\s*([a-zA-Z0-9_-]+)\s*;/', $content, $matches)) {
                return $matches[1];
            }
        }

        return 'Phphone';
    }
}
