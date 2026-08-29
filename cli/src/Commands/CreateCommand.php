<?php
namespace Phphone\Cli\Commands;

use ZipArchive;

class CreateCommand implements CommandInterface {
    
    private const REPO_OWNER = 'phphone';
    private const REPO_NAME = 'phphone';
    
    // Valores de plantilla que se reemplazarán en el boilerplate
    private const TEMPLATE_PACKAGE  = 'com.example.phphone';
    private const TEMPLATE_APP_NAME = 'Phphone';
    private const TEMPLATE_THEME    = 'Theme.ProjectK2';
    
    public function execute(array $args): void {
        // En Phphone CLI, los args suelen ser: [0] => phphone, [1] => create, [2] => Name
        $name = $args[2] ?? null;
        
        // Evitar que el nombre sea un flag
        if ($name && strpos($name, '--') === 0) {
            $name = null;
        }

        if (!$name) {
            echo __('create.error_no_name');
            echo __('create.error_no_name_example');
            return;
        }

        // Buscar flag --version=v1.0.0
        $version = null;
        foreach ($args as $arg) {
            if (strpos($arg, '--version=') === 0) {
                $version = substr($arg, 10);
            }
        }

        // Determinar URL de descarga
        if ($version) {
            $downloadUrl = sprintf('https://github.com/%s/%s/archive/refs/tags/%s.zip', self::REPO_OWNER, self::REPO_NAME, $version);
            echo "ℹ️  Usando versión específica: $version\n";
        } else {
            $downloadUrl = sprintf('https://github.com/%s/%s/archive/refs/heads/main.zip', self::REPO_OWNER, self::REPO_NAME);
        }

        $targetDir = getcwd() . DIRECTORY_SEPARATOR . $name;

        if (is_dir($targetDir)) {
            echo __('create.error_dir_exists', ['name' => $name]);
            return;
        }

        // Derivar el package ID a partir del nombre del proyecto
        // Ej: "mi-app" -> "com.phphone.miapp"
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));
        if (empty($slug)) {
            echo __('create.error_invalid_name');
            return;
        }
        $newPackage = "com.phphone.$slug";
        $newAppName = ucwords(str_replace(['-', '_'], ' ', $name));
        $newTheme   = "Theme." . str_replace([' ', '-', '_'], '', ucwords(str_replace(['-', '_'], ' ', $name)));

        echo __('create.start', ['name' => $name]);
        echo __('create.package_id', ['package' => $newPackage]);
        echo __('create.downloading');

        $zipPath = getcwd() . DIRECTORY_SEPARATOR . 'kie_temp_boilerplate.zip';
        
        if (function_exists('\curl_init')) {
            $ch = \curl_init($downloadUrl);
            $fp = fopen($zipPath, 'w+');
            \curl_setopt($ch, CURLOPT_FILE, $fp);
            \curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            \curl_setopt($ch, CURLOPT_NOPROGRESS, false);
            
            $lastMb = -1;
            \curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function($resource, $downloadSize, $downloaded, $uploadSize, $uploaded) use (&$lastMb) {
                if ($downloadSize > 0) {
                    $percent = round(($downloaded / $downloadSize) * 100);
                    echo "\r📦 Descargando motores base... " . $percent . "%";
                } else if ($downloaded > 0) {
                    // GitHub no envía el tamaño total (Content-Length) en los .zip generados al vuelo.
                    // En lugar de porcentaje, mostramos los MB descargados.
                    $mb = round($downloaded / 1024 / 1024, 1);
                    if ($mb !== $lastMb) {
                        echo "\r📦 Descargando motores base... " . $mb . " MB";
                        $lastMb = $mb;
                    }
                }
            });
            
            \curl_exec($ch);
            $httpCode = \curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = \curl_error($ch);
            \curl_close($ch);
            fclose($fp);
            echo "\n";
            
            $returnVar = ($httpCode >= 200 && $httpCode < 400 && empty($curlError)) ? 0 : 1;
        } else {
            // Fallback al comando del sistema si la extensión cURL de PHP no está activa
            echo "\r📦 Descargando motores base (esto puede tardar unos minutos)...\n";
            $curlCommand = sprintf('curl -L -s -o "%s" "%s"', $zipPath, $downloadUrl);
            exec($curlCommand, $output, $returnVar);
        }

        if ($returnVar !== 0 || !file_exists($zipPath) || filesize($zipPath) == 0) {
            echo "Error: No se pudo descargar la plantilla desde GitHub.\n";
            echo "URL intentada: $downloadUrl\n";
            if ($version) {
                echo "Verifica que el tag '$version' exista en el repositorio.\n";
            }
            return;
        }

        echo __('create.extracting');

        $tempExtractDir = getcwd() . DIRECTORY_SEPARATOR . 'kie_extract_temp_' . time();
        mkdir($tempExtractDir);

        $extractionSuccess = false;

        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive;
            if ($zip->open($zipPath) === TRUE) {
                $zip->extractTo($tempExtractDir);
                $zip->close();
                $extractionSuccess = true;
            }
        } else {
            // Fallback al comando nativo del OS (Windows 10/11 y Linux traen 'tar' por defecto)
            $tarCommand = sprintf('tar -xf "%s" -C "%s"', $zipPath, $tempExtractDir);
            exec($tarCommand, $outputTar, $returnTar);
            if ($returnTar === 0) {
                $extractionSuccess = true;
            }
        }

        if (!$extractionSuccess) {
            unlink($zipPath);
            rmdir($tempExtractDir);
            echo __('create.error_extract');
            return;
        }

        // Buscar la carpeta que GitHub generó adentro y moverla al target final
        $extractedFolders = array_diff(scandir($tempExtractDir), ['.', '..']);
        $githubRootFolder = reset($extractedFolders);
        $sourceDir = $tempExtractDir . DIRECTORY_SEPARATOR . $githubRootFolder;
        
        rename($sourceDir, $targetDir);
        rmdir($tempExtractDir);
        unlink($zipPath);

        // ── PERSONALIZACIÓN DEL PROYECTO ─────────────────────────────────────
        echo __('create.customizing');

        // 1. Archivos de texto que contienen el package/nombre antiguo
        $filesToPatch = [
            $targetDir . '/android/app/build.gradle.kts',
            $targetDir . '/android/app/src/main/AndroidManifest.xml',
            $targetDir . '/android/app/src/main/res/values/strings.xml',
            $targetDir . '/android/app/src/main/res/values/themes.xml',
            $targetDir . '/android/phphone_meta.json',
            
            // Archivos de iOS
            $targetDir . '/ios/Phphone.xcodeproj/project.pbxproj',
            $targetDir . '/ios/project.yml',
            $targetDir . '/ios/App/Info.plist',
            $targetDir . '/ios/build_manual.sh',
            $targetDir . '/ios/phphone_meta.json',
        ];

        foreach ($filesToPatch as $file) {
            if (!file_exists($file)) continue;
            $content = file_get_contents($file);
            
            if (basename($file) === 'build.gradle.kts') {
                // Solo cambiar applicationId, dejar namespace como com.example.phphone para no romper JNI
                $content = preg_replace('/applicationId\s*=\s*"[^"]+"/', 'applicationId = "' . $newPackage . '"', $content);
            } else {
                $content = str_replace(self::TEMPLATE_PACKAGE,  $newPackage,  $content);
                if (basename($file) === 'AndroidManifest.xml') {
                    $content = preg_replace('/\s*package="[^"]+"/', '', $content);
                }
            }

            // Parche específico para el bundle de iOS si el template usaba com.phphone.Phphone
            $content = str_replace('com.phphone.Phphone', $newPackage, $content);
            if (basename($file) === 'AndroidManifest.xml') {
                $content = str_replace('android:label="' . self::TEMPLATE_APP_NAME . '"', 'android:label="' . $newAppName . '"', $content);
            } else {
                $content = str_replace(self::TEMPLATE_APP_NAME, $newAppName,  $content);
            }
            // Corregir espacios en bundle IDs inyectados por error en project.pbxproj o project.yml
            $content = preg_replace('/PRODUCT_BUNDLE_IDENTIFIER\s*=\s*([a-zA-Z0-9\.]+)\.[^;]+;/', 'PRODUCT_BUNDLE_IDENTIFIER = ' . $newPackage . ';', $content);
            $content = str_replace(self::TEMPLATE_THEME,    $newTheme,    $content);
            file_put_contents($file, $content);
        }

        echo __('create.success', ['name' => $name]);
        echo __('create.hint_cd', ['name' => $name]);
        echo __('create.hint_run');
    }
}
