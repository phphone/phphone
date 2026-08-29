<?php
namespace Phphone\Cli\Commands;

class RenameCommand implements CommandInterface {
    
    public function execute(array $args): void {
        $targetIos = false;
        $targetAndroid = false;
        $newName = null;
        $newPackage = null;
        
        // Parsear argumentos y flags
        for ($i = 2; $i < count($args); $i++) {
            if ($args[$i] === '--ios') {
                $targetIos = true;
            } elseif ($args[$i] === '--android') {
                $targetAndroid = true;
            } else {
                if (!$newName) {
                    $newName = $args[$i];
                } elseif (!$newPackage) {
                    $newPackage = $args[$i];
                }
            }
        }
        
        // Si no se pasaron flags, se asume que aplica a ambas plataformas
        if (!$targetIos && !$targetAndroid) {
            $targetIos = true;
            $targetAndroid = true;
        }
        
        if (!$newName) {
            echo __('rename.error_missing_args');
            echo __('rename.error_missing_args_example');
            return;
        }

        // Formatear el nombre como en el comando Create
        $newName = ucwords(str_replace(['-', '_'], ' ', $newName));

        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $newName));
        if (empty($slug)) {
            echo __('create.error_invalid_name'); // Reusamos el de create
            return;
        }

        if (!$newPackage) {
            $newPackage = "com.phphone.$slug";
        }
        
        $targetDir = getcwd();

        echo __('rename.start', ['name' => $newName, 'package' => $newPackage]);

        if ($targetAndroid) {
            $this->renameAndroid($targetDir, $newName, $newPackage);
        }

        if ($targetIos) {
            $this->renameIos($targetDir, $newName, $newPackage);
        }

        echo __('rename.success');
    }

    private function renameAndroid($targetDir, $newName, $newPackage) {
        $metaPath = $targetDir . '/android/phphone_meta.json';
        if (!file_exists($metaPath)) {
            echo __('rename.error_no_meta', ['platform' => 'Android']);
            return;
        }

        $meta = json_decode(file_get_contents($metaPath), true);
        $oldPackage = $meta['package_name'];
        $oldAppName = $meta['app_name'];
        
        $oldAppName = $meta['app_name'];

        echo __('rename.processing', ['platform' => 'Android']);

        $filesToPatch = [
            $targetDir . '/android/app/build.gradle.kts',
            $targetDir . '/android/app/src/main/AndroidManifest.xml',
            $targetDir . '/android/app/src/main/res/values/strings.xml',
        ];

        foreach ($filesToPatch as $file) {
            if (!file_exists($file)) continue;
            $content = file_get_contents($file);
            
            if (basename($file) === 'build.gradle.kts') {
                // Solo cambiar applicationId, dejar namespace intacto
                $content = preg_replace('/applicationId\s*=\s*"' . preg_quote($oldPackage, '/') . '"/', 'applicationId = "' . $newPackage . '"', $content);
            } elseif (basename($file) === 'AndroidManifest.xml') {
                $escapedName = htmlspecialchars($newName, ENT_XML1, 'UTF-8');
                $content = preg_replace('/android:label="[^"]+"/', 'android:label="' . $escapedName . '"', $content);
                $content = preg_replace('/\s*package="[^"]+"/', '', $content);
            } elseif (basename($file) === 'strings.xml') {
                $escapedName = htmlspecialchars($newName, ENT_XML1, 'UTF-8');
                $content = preg_replace('/<string name="app_name">[^<]+<\/string>/', '<string name="app_name">' . $escapedName . '</string>', $content);
            }
            
            file_put_contents($file, $content);
        }

        // Ya no movemos la carpeta Kotlin ni cambiamos su namespace
        // para mantener intacta la firma JNI precompilada del motor.

        // Actualizar Meta
        $meta['app_name'] = $newName;
        $meta['package_name'] = $newPackage;
        file_put_contents($metaPath, json_encode($meta, JSON_PRETTY_PRINT));
    }

    private function renameIos($targetDir, $newName, $newPackage) {
        $metaPath = $targetDir . '/ios/phphone_meta.json';
        if (!file_exists($metaPath)) {
            echo __('rename.error_no_meta', ['platform' => 'iOS']);
            return;
        }

        $meta = json_decode(file_get_contents($metaPath), true);
        $oldPackage = $meta['package_name'];
        $oldAppName = $meta['app_name'];

        echo __('rename.processing', ['platform' => 'iOS']);

        // Renombrar .xcodeproj y .xcworkspace si existen
        $oldXcodeProj = $targetDir . '/ios/' . $oldAppName . '.xcodeproj';
        $newXcodeProj = $targetDir . '/ios/' . $newName . '.xcodeproj';
        if (is_dir($oldXcodeProj) && $oldAppName !== $newName) {
            rename($oldXcodeProj, $newXcodeProj);
        }

        $oldWorkspace = $targetDir . '/ios/' . $oldAppName . '.xcworkspace';
        $newWorkspace = $targetDir . '/ios/' . $newName . '.xcworkspace';
        if (is_dir($oldWorkspace) && $oldAppName !== $newName) {
            rename($oldWorkspace, $newWorkspace);
        }

        $filesToPatch = [
            $newXcodeProj . '/project.pbxproj',
            $targetDir . '/ios/project.yml',
            $targetDir . '/ios/App/Info.plist',
        ];

        foreach ($filesToPatch as $file) {
            if (!file_exists($file)) continue;
            $content = file_get_contents($file);
            
            if (basename($file) === 'Info.plist') {
                $escapedName = htmlspecialchars($newName, ENT_XML1, 'UTF-8');
                $content = preg_replace('/<key>CFBundleDisplayName<\/key>\s*<string>[^<]*<\/string>/s', "<key>CFBundleDisplayName</key>\n\t<string>{$escapedName}</string>", $content);
                $content = preg_replace('/<key>CFBundleName<\/key>\s*<string>[^<]*<\/string>/s', "<key>CFBundleName</key>\n\t<string>{$escapedName}</string>", $content);
            } elseif (basename($file) === 'project.yml') {
                $content = preg_replace('/name:\s*"?\b' . preg_quote($oldAppName, '/') . '\b"?/', 'name: ' . $newName, $content);
                $content = preg_replace('/bundleId:\s*"?\b' . preg_quote($oldPackage, '/') . '\b"?/', 'bundleId: ' . $newPackage, $content);
            } elseif (basename($file) === 'project.pbxproj') {
                $content = preg_replace('/PRODUCT_BUNDLE_IDENTIFIER\s*=\s*[^;]+;/', 'PRODUCT_BUNDLE_IDENTIFIER = ' . $newPackage . ';', $content);
                $content = preg_replace('/PRODUCT_NAME\s*=\s*"?[^";]+"?[^;]*;/', 'PRODUCT_NAME = "' . $newName . '";', $content);
            }
            
            file_put_contents($file, $content);
        }

        // Actualizar Meta
        $meta['app_name'] = $newName;
        $meta['package_name'] = $newPackage;
        file_put_contents($metaPath, json_encode($meta, JSON_PRETTY_PRINT));
    }
}
