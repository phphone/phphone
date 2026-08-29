<?php
namespace Phphone\Cli\Commands;

class ConfigCommand implements CommandInterface
{
    public function execute(array $args): void
    {
        if (count($args) < 2) {
            echo __('config.usage');
            exit(1);
        }

        $property = strtolower($args[0]);
        $value = strtolower($args[1]);

        $projectRoot = getcwd();

        switch ($property) {
            case 'orientation':
                $this->setOrientation($projectRoot, $value);
                break;
            case 'zoom':
                $this->setZoom($projectRoot, $value);
                break;
            default:
                echo __('config.unknown_property', ['property' => $property]);
                exit(1);
        }
    }

    private function setOrientation(string $projectRoot, string $value): void
    {
        $allowed = ['portrait', 'landscape', 'auto'];
        if (!in_array($value, $allowed)) {
            echo __('config.invalid_orientation');
            exit(1);
        }

        echo __('config.setting_orientation', ['value' => $value]);

        // iOS - Info.plist
        $plistPath = $projectRoot . '/ios/App/Info.plist';
        if (file_exists($plistPath)) {
            $plist = file_get_contents($plistPath);
            
            $portraitStr = "<string>UIInterfaceOrientationPortrait</string>\n\t\t<string>UIInterfaceOrientationPortraitUpsideDown</string>";
            $landscapeStr = "<string>UIInterfaceOrientationLandscapeLeft</string>\n\t\t<string>UIInterfaceOrientationLandscapeRight</string>";
            
            $newOrientations = "";
            if ($value === 'portrait') {
                $newOrientations = $portraitStr;
            } elseif ($value === 'landscape') {
                $newOrientations = $landscapeStr;
            } else {
                $newOrientations = $portraitStr . "\n\t\t" . $landscapeStr;
            }

            // Regex para encontrar UISupportedInterfaceOrientations y su array
            $pattern = '/<key>UISupportedInterfaceOrientations<\/key>\s*<array>.*?<\/array>/s';
            $replacement = "<key>UISupportedInterfaceOrientations</key>\n\t<array>\n\t\t$newOrientations\n\t</array>";
            
            $plist = preg_replace($pattern, $replacement, $plist);
            file_put_contents($plistPath, $plist);
            echo __('config.ios_plist_updated');
        }

        // Android - AndroidManifest.xml
        $manifestPath = $projectRoot . '/android/app/src/main/AndroidManifest.xml';
        if (file_exists($manifestPath)) {
            $manifest = file_get_contents($manifestPath);
            
            $androidVal = "unspecified";
            if ($value === 'portrait') $androidVal = "portrait";
            if ($value === 'landscape') $androidVal = "sensorLandscape";
            
            // Reemplazar o añadir android:screenOrientation en el activity principal
            // Buscar la etiqueta activity que contenga MAIN y LAUNCHER (el punto de entrada)
            if (preg_match('/android:screenOrientation="[^"]+"/', $manifest)) {
                $manifest = preg_replace('/android:screenOrientation="[^"]+"/', 'android:screenOrientation="' . $androidVal . '"', $manifest);
            } else {
                // Agregar el atributo de manera sencilla (asumimos formato estándar del manifest de Phphone)
                $manifest = preg_replace('/(<activity[^>]+android:name=".MainActivity"[^>]*?)>/s', '$1 android:screenOrientation="' . $androidVal . '">', $manifest);
            }
            file_put_contents($manifestPath, $manifest);
            echo __('config.android_manifest_updated');
        }
    }

    private function setZoom(string $projectRoot, string $value): void
    {
        $enabled = ($value === 'on') ? 'true' : 'false';

        echo __('config.setting_zoom', ['value' => $value]);

        // iOS
        $swiftPath = $projectRoot . '/ios/App/ViewController.swift';
        if (file_exists($swiftPath)) {
            $swift = file_get_contents($swiftPath);
            $swift = preg_replace('/static var KIE_ZOOM_ENABLED = (true|false)/', "static var KIE_ZOOM_ENABLED = $enabled", $swift);
            file_put_contents($swiftPath, $swift);
            echo __('config.ios_swift_updated');
        }

        // Android
        $kotlinPath = $projectRoot . '/android/app/src/main/java/com/example/phphone/MainActivity.kt';
        if (file_exists($kotlinPath)) {
            $kotlin = file_get_contents($kotlinPath);
            $kotlin = preg_replace('/var KIE_ZOOM_ENABLED = (true|false)/', "var KIE_ZOOM_ENABLED = $enabled", $kotlin);
            file_put_contents($kotlinPath, $kotlin);
            echo __('config.android_kotlin_updated');
        }
    }
}
