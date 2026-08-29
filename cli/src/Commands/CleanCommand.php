<?php
namespace Phphone\Cli\Commands;

class CleanCommand implements CommandInterface {
    
    public function execute(array $args): void {
        echo __('clean.start');

        $cwd = getcwd();
        $androidDir = $cwd . DIRECTORY_SEPARATOR . 'android';
        $cleanedSomething = false;

        // 1. Limpiar el cache interno de Gradle
        if (is_dir($androidDir)) {
            $gradleCmd = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? "gradlew.bat" : "gradlew";
            echo __('clean.gradle');
            
            chdir($androidDir);
            // Ejecutamos silenciosamente
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                exec("$gradleCmd clean > NUL 2>&1");
            } else {
                $devNull = Platform::devNull();
                exec("$gradleCmd clean > $devNull 2>&1");
            }
            chdir($cwd);
            $cleanedSomething = true;
        }

        // 2. Borrar la carpeta build/ de la raíz del proyecto
        $rootBuildDir = $cwd . DIRECTORY_SEPARATOR . 'build';
        if (is_dir($rootBuildDir)) {
            echo __('clean.build_folder');
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                exec(sprintf('rmdir /S /Q "%s" > NUL 2>&1', $rootBuildDir));
            } else {
                $devNull = Platform::devNull();
                exec(sprintf('rm -rf "%s" > %s 2>&1', escapeshellarg($rootBuildDir), $devNull));
            }
            $cleanedSomething = true;
        }

        // 3. Limpiar cachés de iOS / Xcode
        $iosDir = $cwd . DIRECTORY_SEPARATOR . 'ios';
        if (is_dir($iosDir) && class_exists('Phphone\Cli\Commands\Platform') && \Phphone\Cli\Commands\Platform::isMac()) {
            echo __('clean.ios_xcode');
            $manualBuildDir = $iosDir . DIRECTORY_SEPARATOR . 'build_manual.nosync';
            if (is_dir($manualBuildDir)) {
                $devNull = \Phphone\Cli\Commands\Platform::devNull();
                exec(sprintf('rm -rf "%s" > %s 2>&1', escapeshellarg($manualBuildDir), $devNull));
                $cleanedSomething = true;
            }
            
            // Limpiar los archivos intermedios de Xcode (DerivedData asociado)
            chdir($iosDir);
            $devNull = \Phphone\Cli\Commands\Platform::devNull();
            exec("xcodebuild clean > $devNull 2>&1");
            chdir($cwd);
            
            $cleanedSomething = true;
        }

        if ($cleanedSomething) {
            echo __('clean.success');
        } else {
            echo __('clean.already_clean');
        }
    }
}
