<?php
namespace Phphone\Cli;

/**
 * Manages project configuration from phphone.json
 * Synchronizes declarative settings into native platform files.
 */
class ProjectConfig
{
    private string $projectRoot;
    private ?array $data = null;
    private string $configPath;

    public function __construct(string $projectRoot)
    {
        $this->projectRoot = $projectRoot;
        $this->configPath = $projectRoot . DIRECTORY_SEPARATOR . 'phphone.json';
        if (file_exists($this->configPath)) {
            $raw = file_get_contents($this->configPath);
            $this->data = json_decode($raw, true) ?: [];
        }
    }

    public function exists(): bool
    {
        return $this->data !== null;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if ($this->data === null) {
            return $default;
        }

        $parts = explode('.', $key);
        $curr = $this->data;
        foreach ($parts as $part) {
            if (!is_array($curr) || !array_key_exists($part, $curr)) {
                return $default;
            }
            $curr = $curr[$part];
        }

        return $curr;
    }

    public function set(string $key, mixed $value): void
    {
        if ($this->data === null) {
            $this->data = [];
        }

        $parts = explode('.', $key);
        $curr = &$this->data;
        foreach ($parts as $part) {
            if (!isset($curr[$part]) || !is_array($curr[$part])) {
                $curr[$part] = [];
            }
            $curr = &$curr[$part];
        }
        $curr = $value;
    }

    public function save(): bool
    {
        if ($this->data === null) {
            return false;
        }
        return file_put_contents(
            $this->configPath,
            json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n"
        ) !== false;
    }

    /**
     * Propagates settings from phphone.json to Android and iOS native files
     */
    public function syncNative(): array
    {
        if ($this->data === null) {
            return ['status' => 'skipped', 'message' => 'No phphone.json found'];
        }

        $appName = $this->get('name');
        $packageName = $this->get('package');
        $version = $this->get('version', '1.0.0');
        $versionCode = $this->get('versionCode', 1);

        $androidMinSdk = $this->get('android.minSdk', 24);
        $androidTargetSdk = $this->get('android.targetSdk', 36);

        $iosDeploymentTarget = (string)$this->get('ios.deploymentTarget', '15.0');
        $iosBackgroundModes = $this->get('ios.backgroundModes', ['fetch']);
        $iosPermissions = $this->get('ios.permissions', []);

        $changes = [];

        // 1. Synchronize Android if platform directory exists
        $androidDir = $this->projectRoot . '/android';
        if (is_dir($androidDir)) {
            $this->syncAndroid(
                $androidDir,
                $appName,
                $packageName,
                $version,
                $versionCode,
                $androidMinSdk,
                $androidTargetSdk
            );
            $changes[] = 'android';
        }

        // 2. Synchronize iOS if platform directory exists
        $iosDir = $this->projectRoot . '/ios';
        if (is_dir($iosDir)) {
            $this->syncIos(
                $iosDir,
                $appName,
                $packageName,
                $version,
                $versionCode,
                $iosDeploymentTarget,
                $iosBackgroundModes,
                $iosPermissions
            );
            $changes[] = 'ios';
        }

        return ['status' => 'success', 'synced' => $changes];
    }

    private function syncAndroid(
        string $androidDir,
        ?string $appName,
        ?string $packageName,
        string $version,
        int $versionCode,
        int $minSdk,
        int $targetSdk
    ): void {
        $gradlePath = $androidDir . '/app/build.gradle.kts';
        if (file_exists($gradlePath)) {
            $gradle = file_get_contents($gradlePath);
            if ($packageName) {
                $gradle = preg_replace('/applicationId\s*=\s*"[^"]+"/', 'applicationId = "' . $packageName . '"', $gradle);
            }
            $gradle = preg_replace('/minSdk\s*=\s*\d+/', 'minSdk = ' . $minSdk, $gradle);
            $gradle = preg_replace('/targetSdk\s*=\s*\d+/', 'targetSdk = ' . $targetSdk, $gradle);
            $gradle = preg_replace('/compileSdk\s*=\s*\d+/', 'compileSdk = ' . $targetSdk, $gradle);
            $gradle = preg_replace('/versionCode\s*=\s*\d+/', 'versionCode = ' . $versionCode, $gradle);
            $gradle = preg_replace('/versionName\s*=\s*"[^"]+"/', 'versionName = "' . $version . '"', $gradle);
            file_put_contents($gradlePath, $gradle);
        }

        $manifestPath = $androidDir . '/app/src/main/AndroidManifest.xml';
        if (file_exists($manifestPath)) {
            $manifest = file_get_contents($manifestPath);
            if ($appName) {
                $escaped = htmlspecialchars($appName, ENT_XML1, 'UTF-8');
                $manifest = preg_replace('/android:label="[^"]+"/', 'android:label="' . $escaped . '"', $manifest);
            }
            // Ensure usesCleartextTraffic="true" is stripped
            $manifest = preg_replace('/\s*android:usesCleartextTraffic="true"/', '', $manifest);
            // Ensure networkSecurityConfig attribute is present
            if (strpos($manifest, 'android:networkSecurityConfig') === false) {
                $manifest = preg_replace('/<application\b/', '<application android:networkSecurityConfig="@xml/network_security_config"', $manifest);
            }
            file_put_contents($manifestPath, $manifest);
        }

        $metaPath = $androidDir . '/phphone_meta.json';
        $meta = file_exists($metaPath) ? (json_decode(file_get_contents($metaPath), true) ?: []) : [];
        if ($appName) $meta['app_name'] = $appName;
        if ($packageName) $meta['package_name'] = $packageName;
        $meta['version'] = $version;
        $meta['android'] = ['min_sdk' => $minSdk, 'target_sdk' => $targetSdk];
        file_put_contents($metaPath, json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
    }

    private function syncIos(
        string $iosDir,
        ?string $appName,
        ?string $packageName,
        string $version,
        int $versionCode,
        string $deploymentTarget,
        array $backgroundModes,
        array $permissions
    ): void {
        $projectYmlPath = $iosDir . '/project.yml';
        if (file_exists($projectYmlPath)) {
            $yml = file_get_contents($projectYmlPath);
            if ($appName) {
                // Update target and scheme name cleanly without string concatenation
                $yml = preg_replace('/^name:\s*.+$/m', 'name: ' . (strpos($appName, ' ') !== false ? '"' . $appName . '"' : $appName), $yml);
            }
            if ($deploymentTarget) {
                $yml = preg_replace('/deploymentTarget:\s*["\']?[0-9\.]+["\']?/', 'deploymentTarget: "' . $deploymentTarget . '"', $yml);
            }
            if ($packageName) {
                $yml = preg_replace('/PRODUCT_BUNDLE_IDENTIFIER:\s*[^;\s\n]+/', 'PRODUCT_BUNDLE_IDENTIFIER: ' . $packageName, $yml);
            }
            file_put_contents($projectYmlPath, $yml);
        }

        $plistPath = $iosDir . '/App/Info.plist';
        if (file_exists($plistPath)) {
            $plist = file_get_contents($plistPath);
            if ($appName) {
                $escaped = htmlspecialchars($appName, ENT_XML1, 'UTF-8');
                $plist = preg_replace('/<key>CFBundleDisplayName<\/key>\s*<string>[^<]*<\/string>/s', "<key>CFBundleDisplayName</key>\n\t<string>{$escaped}</string>", $plist);
                $plist = preg_replace('/<key>CFBundleName<\/key>\s*<string>[^<]*<\/string>/s', "<key>CFBundleName</key>\n\t<string>{$escaped}</string>", $plist);
            }
            $plist = preg_replace('/<key>CFBundleShortVersionString<\/key>\s*<string>[^<]*<\/string>/s', "<key>CFBundleShortVersionString</key>\n\t<string>{$version}</string>", $plist);
            $plist = preg_replace('/<key>CFBundleVersion<\/key>\s*<string>[^<]*<\/string>/s', "<key>CFBundleVersion</key>\n\t<string>{$versionCode}</string>", $plist);

            // Background modes
            if (!empty($backgroundModes)) {
                $bgItems = '';
                foreach ($backgroundModes as $mode) {
                    $bgItems .= "\t\t<string>" . htmlspecialchars($mode, ENT_XML1, 'UTF-8') . "</string>\n";
                }
                $newBg = "<key>UIBackgroundModes</key>\n\t<array>\n" . rtrim($bgItems) . "\n\t</array>";
                if (strpos($plist, '<key>UIBackgroundModes</key>') !== false) {
                    $plist = preg_replace('/<key>UIBackgroundModes<\/key>\s*<array>.*?<\/array>/s', $newBg, $plist);
                }
            }

            // Permissions (Usage Descriptions)
            $permKeyMap = [
                'camera' => 'NSCameraUsageDescription',
                'location' => 'NSLocationWhenInUseUsageDescription',
                'microphone' => 'NSMicrophoneUsageDescription',
                'photoLibrary' => 'NSPhotoLibraryUsageDescription',
                'contacts' => 'NSContactsUsageDescription',
                'faceId' => 'NSFaceIDUsageDescription',
            ];

            foreach ($permissions as $k => $desc) {
                if (isset($permKeyMap[$k]) && !empty($desc)) {
                    $plistKey = $permKeyMap[$k];
                    $escapedDesc = htmlspecialchars($desc, ENT_XML1, 'UTF-8');
                    $pattern = '/<key>' . $plistKey . '<\/key>\s*<string>[^<]*<\/string>/s';
                    $replacement = "<key>{$plistKey}</key>\n\t<string>{$escapedDesc}</string>";
                    if (strpos($plist, "<key>{$plistKey}</key>") !== false) {
                        $plist = preg_replace($pattern, $replacement, $plist);
                    }
                }
            }

            file_put_contents($plistPath, $plist);
        }

        $metaPath = $iosDir . '/phphone_meta.json';
        $meta = file_exists($metaPath) ? (json_decode(file_get_contents($metaPath), true) ?: []) : [];
        if ($appName) $meta['app_name'] = $appName;
        if ($packageName) $meta['package_name'] = $packageName;
        $meta['version'] = $version;
        $meta['ios'] = ['deployment_target' => $deploymentTarget];
        file_put_contents($metaPath, json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
    }
}
