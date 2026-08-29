<?php
namespace Phphone\Cli\Commands;

class BuildCommand implements CommandInterface {
    
    public function execute(array $args): void {
        $platform = Platform::resolvePlatform($args);
        $type = $args[2] ?? 'apk';
        
        // Determinar si hay flags de plataforma explícitos para no depender de la auto-detección
        if (in_array('--ios', $args, true)) {
            $platform = 'ios';
        } elseif (in_array('--android', $args, true)) {
            $platform = 'android';
        }

        $isRelease = in_array('--release', $args, true);
        $encrypt = $isRelease && !in_array('--no-encrypt', $args, true);

        $modeStr = $isRelease ? '(Release)' : '(Debug)';
        if ($encrypt) {
            $modeStr .= ' [🔐 Encrypted]';
        }
        echo __('build.start', ['type' => strtoupper($platform === 'ios' ? 'APP/IPA' : $type), 'mode' => $modeStr]);

        $projectRoot = getcwd();
        $srcDir = $projectRoot . DIRECTORY_SEPARATOR . 'src';
        
        $encryptedSrcDir = $projectRoot . DIRECTORY_SEPARATOR . 'build' . DIRECTORY_SEPARATOR . '.encrypted_src';

        if ($encrypt) {
            echo __('build.encrypting');
            $this->prepareEncryptedSource($srcDir, $encryptedSrcDir, $platform);
        }

        // ─── FLUJO iOS ───
        if ($platform === 'ios') {
            if (!Platform::isMac()) {
                echo __('build.ios_error_no_mac');
                return;
            }

            $iosDir = $projectRoot . DIRECTORY_SEPARATOR . 'ios';
            if (!is_dir($iosDir)) {
                echo __('build.ios_error_no_ios');
                return;
            }

            // Descomprimir motor si no existe
            $zipPath = $iosDir . '/php_env.zip';
            $destDir = $iosDir . '/build_ios';
            if (!is_dir($destDir) && file_exists($zipPath)) {
                echo __('build.ios_unzip');
                // Fallback para descompresión nativa
                if (class_exists('ZipArchive')) {
                    $zip = new \ZipArchive();
                    if ($zip->open($zipPath) === true) {
                        $zip->extractTo($destDir);
                        $zip->close();
                    }
                } else {
                    exec(sprintf('unzip -q "%s" -d "%s"', $zipPath, $destDir));
                }
            }

            $configuration = $isRelease ? 'Release' : 'Debug';
            echo __('build.ios_running', ['mode' => $configuration]);

            $cwd = getcwd();
            chdir($iosDir);
            
            // Ejecutamos la compilación nativa desactivando la firma obligatoria en consola para evitar bloqueos
            // PHPHONE_SRC_DIR se pasa como Xcode build setting (no env var) para que el Run Script lo reciba
            $encryptedSrcAbs = $cwd . '/build/.encrypted_src';
            $srcDirSetting = $encrypt ? "PHPHONE_SRC_DIR=\"$encryptedSrcAbs\"" : '';
            $cmd = "xcodebuild -project Phphone.xcodeproj -scheme Phphone -configuration $configuration -sdk iphoneos build CODE_SIGN_IDENTITY=\"\" CODE_SIGNING_REQUIRED=NO CODE_SIGNING_ALLOWED=NO $srcDirSetting";
            passthru($cmd, $returnVar);

            chdir($cwd);

            if ($returnVar !== 0) {
                echo __('build.error_failed');
                echo __('build.ios_error_tip');
                return;
            }

            echo __('build.success');
            return;
        }

        // ─── FLUJO ANDROID ───
        $androidDir = $projectRoot . DIRECTORY_SEPARATOR . 'android';
        
        if (!is_dir($androidDir)) {
            echo __('build.error_no_android');
            return;
        }

        // Ensure C++ headers/binaries are extracted before building
        $zipPath = $projectRoot . '/android/php_env.zip';
        $destDir = $projectRoot . '/android/app/src/main/cpp/php';
        if ((!is_dir($destDir . '/include') || !is_dir($destDir . '/lib')) && file_exists($zipPath)) {
            echo __('build.android_unzip');
            $zip = new \ZipArchive();
            if ($zip->open($zipPath) === true) {
                $zip->extractTo($destDir);
                $zip->close();
            }
        }

        // Determinar el comando Gradle
        $gradleCmd = "./gradlew";
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $gradleCmd = "gradlew.bat";
        }

        $task = "";
        $outputFile = "";
        $targetExt = "";

        if ($type === 'apk') {
            $task = $isRelease ? "assembleRelease" : "assembleDebug";
            $variant = $isRelease ? "release" : "debug";
            $suffix = $isRelease ? "release-unsigned" : "debug";
            
            // Gradle 8.x produce a veces app-release-unsigned.apk si no hay firma
            $outputFile1 = "app/build/outputs/apk/$variant/app-$variant.apk";
            $outputFile2 = "app/build/outputs/apk/$variant/app-$suffix.apk";
            $targetExt = "apk";
        } elseif ($type === 'aab') {
            $task = $isRelease ? "bundleRelease" : "bundleDebug";
            $variant = $isRelease ? "release" : "debug";
            $outputFile1 = "app/build/outputs/bundle/$variant/app-$variant.aab";
            $outputFile2 = "app/build/outputs/bundle/$variant/app-release.aab";
            $targetExt = "aab";
        } else {
            echo __('build.error_unknown_type', ['type' => $type]);
            return;
        }

        echo __('build.running_gradle', ['task' => $task]);
        
        $cwd = getcwd();
        chdir($androidDir);

        // Limpiar la carpeta de assets existente para forzar la actualización
        $assetsDir = $androidDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'src';
        if (is_dir($assetsDir)) {
            $this->deleteDirectory($assetsDir);
        }

        // Usar ruta absoluta para evitar problemas con Gradle
        $encryptedSrcAbs = $cwd . DIRECTORY_SEPARATOR . 'build' . DIRECTORY_SEPARATOR . '.encrypted_src';
        $gradleArgs = $encrypt ? ' "-PphphoneSourceDir=' . $encryptedSrcAbs . '"' : '';
        passthru("$gradleCmd $task" . $gradleArgs, $returnVar);
        
        chdir($cwd);

        if ($returnVar !== 0) {
            echo __('build.error_failed');
            return;
        }

        $sourcePath1 = $androidDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $outputFile1);
        $sourcePath2 = $androidDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $outputFile2);

        $finalSource = "";
        if (file_exists($sourcePath1)) {
            $finalSource = $sourcePath1;
        } elseif (file_exists($sourcePath2)) {
            $finalSource = $sourcePath2;
        }

        if ($finalSource) {
            $destPath = $cwd . DIRECTORY_SEPARATOR . "build" . DIRECTORY_SEPARATOR . "app-$variant.$targetExt";
            
            if (!is_dir($cwd . DIRECTORY_SEPARATOR . "build")) {
                mkdir($cwd . DIRECTORY_SEPARATOR . "build");
            }

            copy($finalSource, $destPath);
            echo __('build.success');
            echo __('build.copied_to', ['path' => $destPath]);
        } else {
            echo __('build.warning_missing_output');
            echo __('build.paths_searched', ['path1' => $sourcePath1, 'path2' => $sourcePath2]);
        }
        
        if ($encrypt) {
            $this->restoreNativeSecrets();
        }
    }

    private function prepareEncryptedSource(string $srcDir, string $destDir, string $platform): void {
        if (is_dir($destDir)) {
            $this->deleteDirectory($destDir);
        }
        mkdir($destDir, 0777, true);
        
        $aesKey = random_bytes(32); // 256 bits
        $hexKey = bin2hex($aesKey);
        
        // 1. Inyectar llave en Nativos
        $this->injectNativeSecrets($hexKey);
        
        // 2. Encriptar archivos recursivamente
        $this->encryptDirectory($srcDir, $destDir, $aesKey);
        
        // 3. Inyectar el Wrapper de PHP
        $this->injectPhpWrapper($destDir, $hexKey);
    }

    private function encryptDirectory(string $source, string $dest, string $aesKey): void {
        $excludedExtensions = [
            // Imágenes
            'png',
            'jpg',
            'jpeg',
            'gif',
            'svg',
            'webp',
            'ico',
            // Audio y Video
            'mp3',
            'wav',
            'ogg',
            'm4a',
            'mp4',
            'webm',
            'avi',
            'mov',
            // Fuentes y Documentos
            'ttf',
            'otf',
            'woff',
            'woff2',
            'pdf',
            'txt',
            'csv',
            'md',
            // Librerías nativas y binarios
            'so',
            'dylib',
            'dll',
            'a',
            // Bases de datos locales
            'sqlite',
            'sqlite3',
            'db',
            // Archivos comprimidos
            'zip',
            'tar',
            'gz',
            'rar',
            // Modelos 3D
            'obj',
            'glb',
            'gltf',
            'fbx',
            'stl',
            'dae'
        ];

        $dir = opendir($source);
        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') continue;
            
            $srcFile = $source . DIRECTORY_SEPARATOR . $file;
            $destFile = $dest . DIRECTORY_SEPARATOR . $file;
            
            if (is_dir($srcFile)) {
                mkdir($destFile, 0777, true);
                $this->encryptDirectory($srcFile, $destFile, $aesKey);
            } else {
                // EXCEPCIÓN CRÍTICA: KIE_JNI (C++) requiere Device.php nativamente ANTES de registrar el Wrapper.
                // Si lo encriptamos, KIE_JNI lee basura binaria y la escupe a la pantalla arruinando la vista.
                // Device.php es OpenSource (SDK bridge), así que lo dejamos en texto plano.
                if ($file === 'Device.php') {
                    copy($srcFile, $destFile);
                    continue;
                }

                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($extension, $excludedExtensions, true)) {
                    copy($srcFile, $destFile);
                    continue;
                }

                $content = file_get_contents($srcFile);
                $iv = random_bytes(16);
                $encrypted = openssl_encrypt($content, 'AES-256-CBC', $aesKey, OPENSSL_RAW_DATA, $iv);
                $finalContent = "KIE_ENC:" . $iv . $encrypted;
                file_put_contents($destFile, $finalContent);
            }
        }
        closedir($dir);
    }

    private function getAndroidPackageInfo(string $projectRoot): array {
        $gradlePath = $projectRoot . '/android/app/build.gradle.kts';
        $namespace = 'com.example.phphone';
        if (file_exists($gradlePath)) {
            $content = file_get_contents($gradlePath);
            if (preg_match('/namespace\s*=\s*["\']([^"\']+)["\']/', $content, $matches)) {
                $namespace = $matches[1];
            }
        }
        
        $dirPath = $projectRoot . '/android/app/src/main/java/' . str_replace('.', '/', $namespace);
        return [
            'package' => $namespace,
            'class' => 'KieSecrets',
            'path' => $dirPath . '/KieSecrets.kt'
        ];
    }

    private function injectNativeSecrets(string $hexKey): void {
        $projectRoot = getcwd();
        
        // Android
        $androidInfo = $this->getAndroidPackageInfo($projectRoot);
        if (is_dir($projectRoot . '/android')) {
            $packageName = $androidInfo['package'];
            $className = $androidInfo['class'];
            $ktContent = "package $packageName\n\nobject $className {\n    const val IS_ENCRYPTED = true\n    const val AES_KEY_HEX = \"$hexKey\"\n}\n";
            if (!is_dir(dirname($androidInfo['path']))) {
                mkdir(dirname($androidInfo['path']), 0777, true);
            }
            file_put_contents($androidInfo['path'], $ktContent);
        }
        
        // iOS
        $iosControllerPath = $projectRoot . '/ios/App/ViewController.swift';
        if (file_exists($iosControllerPath)) {
            $swiftContent = file_get_contents($iosControllerPath);
            $secretCode = "struct KieSecrets {\n    static let isEncrypted = true\n    static let aesKeyHex = \"$hexKey\"\n}";
            
            if (strpos($swiftContent, 'struct KieSecrets') !== false) {
                $swiftContent = preg_replace('/struct KieSecrets \{.*?\}/s', $secretCode, $swiftContent);
            } else {
                $swiftContent .= "\n" . $secretCode . "\n";
            }
            file_put_contents($iosControllerPath, $swiftContent);
            
            if (file_exists($projectRoot . '/ios/App/KieSecrets.swift')) {
                @unlink($projectRoot . '/ios/App/KieSecrets.swift');
            }
        }
    }

    private function restoreNativeSecrets(): void {
        $projectRoot = getcwd();
        
        // Android
        $androidInfo = $this->getAndroidPackageInfo($projectRoot);
        if (is_file($androidInfo['path'])) {
            $packageName = $androidInfo['package'];
            $className = $androidInfo['class'];
            $ktContent = "package $packageName\n\nobject $className {\n    const val IS_ENCRYPTED = false\n    const val AES_KEY_HEX = \"\"\n}\n";
            file_put_contents($androidInfo['path'], $ktContent);
        }
        
        // iOS
        $iosControllerPath = $projectRoot . '/ios/App/ViewController.swift';
        if (file_exists($iosControllerPath)) {
            $swiftContent = file_get_contents($iosControllerPath);
            $secretCode = "struct KieSecrets {\n    static let isEncrypted = false\n    static let aesKeyHex = \"\"\n}";
            if (strpos($swiftContent, 'struct KieSecrets') !== false) {
                $swiftContent = preg_replace('/struct KieSecrets \{.*?\}/s', $secretCode, $swiftContent);
                file_put_contents($iosControllerPath, $swiftContent);
            }
        }
        $iosSecretPath = $projectRoot . '/ios/App/KieSecrets.swift';
        if (is_file($iosSecretPath)) {
            $swiftContent = "import Foundation\n\nstruct KieSecrets {\n    static let isEncrypted = false\n    static let aesKeyHex = \"\"\n}\n";
            file_put_contents($iosSecretPath, $swiftContent);
        }
    }

    private function injectPhpWrapper(string $destDir, string $hexKey): void {
        $wrapperCode = '<?php
if (!class_exists("PhphoneDecryptWrapper")) {
    class PhphoneDecryptWrapper {
        public $context;
    private $stream;
    private $aesKey;

    public function stream_open($path, $mode, $options, &$opened_path) {
        $this->aesKey = hex2bin("' . $hexKey . '");
        stream_wrapper_restore("file");
        
        // Si es un modo de escritura o append, simplemente pasamos el stream nativo transparente
        if (strpbrk($mode, "waxc+") !== false && strpos($mode, "r") === false) {
            $this->stream = @fopen($path, $mode);
            stream_wrapper_unregister("file");
            stream_wrapper_register("file", "PhphoneDecryptWrapper");
            return $this->stream !== false;
        }
        
        $fp = @fopen($path, "rb");
        if (!$fp) {
            stream_wrapper_unregister("file");
            stream_wrapper_register("file", "PhphoneDecryptWrapper");
            return false;
        }
        
        $content = stream_get_contents($fp);
        fclose($fp);
        
        if (strpos($content, "KIE_ENC:") === 0) {
            $iv = substr($content, 8, 16);
            $encrypted = substr($content, 24);
            $decrypted = @openssl_decrypt($encrypted, "AES-256-CBC", $this->aesKey, 1, $iv);
            if ($decrypted === false) {
                // Si la desencriptación falla, lanzamos el error exacto para debugear
                $err = openssl_error_string();
                throw new \Exception("PhphoneDecryptWrapper CRASH en $path. Motivo: " . ($err ?: "Desconocido"));
            }
        } else {
            $decrypted = $content;
        }
        
        // 1. Intentar memoria RAM pura
        $this->stream = @fopen("php://memory", "r+b");
        
        // 2. Fallback: Escribir archivo fantasma y borrarlo (se mantiene en memoria el handle)
        if (!$this->stream) {
            $baseAppDir = dirname(dirname(__DIR__)); // apunta a files/kie_app
            $ghostFile = $baseAppDir . "/.ghost_" . md5(uniqid());
            $this->stream = @fopen($ghostFile, "w+b");
            if ($this->stream) {
                @unlink($ghostFile);
            } else {
                stream_wrapper_unregister("file");
                stream_wrapper_register("file", "PhphoneDecryptWrapper");
                return false; // Error crítico: Ni memoria ni disco disponibles
            }
        }
        
        fwrite($this->stream, $decrypted);
        rewind($this->stream);
        
        stream_wrapper_unregister("file");
        stream_wrapper_register("file", "PhphoneDecryptWrapper");
        return true;
    }

    public function stream_read($count) {
        return fread($this->stream, $count);
    }
    public function stream_write($data) {
        return fwrite($this->stream, $data);
    }
    public function stream_eof() {
        return feof($this->stream);
    }
    public function stream_stat() {
        return fstat($this->stream);
    }
    public function stream_set_option($option, $arg1, $arg2) {
        return false;
    }
    public function url_stat($path, $flags) {
        stream_wrapper_restore("file");
        $stat = @stat($path);
        stream_wrapper_unregister("file");
        stream_wrapper_register("file", "PhphoneDecryptWrapper");
        return $stat;
    }

    // --- MANEJO DE DIRECTORIOS Y SISTEMA DE ARCHIVOS (Inyectados) ---

    public function mkdir($path, $mode, $options) {
        stream_wrapper_restore("file");
        $result = @mkdir($path, $mode, ($options & STREAM_MKDIR_RECURSIVE) === STREAM_MKDIR_RECURSIVE);
        stream_wrapper_unregister("file");
        stream_wrapper_register("file", "PhphoneDecryptWrapper");
        return $result;
    }

    public function rmdir($path, $options) {
        stream_wrapper_restore("file");
        $result = @rmdir($path);
        stream_wrapper_unregister("file");
        stream_wrapper_register("file", "PhphoneDecryptWrapper");
        return $result;
    }

    public function rename($path_from, $path_to) {
        stream_wrapper_restore("file");
        $result = @rename($path_from, $path_to);
        stream_wrapper_unregister("file");
        stream_wrapper_register("file", "PhphoneDecryptWrapper");
        return $result;
    }

    public function unlink($path) {
        stream_wrapper_restore("file");
        $result = @unlink($path);
        stream_wrapper_unregister("file");
        stream_wrapper_register("file", "PhphoneDecryptWrapper");
        return $result;
    }

    private $dir_handle;

    public function dir_opendir($path, $options) {
        stream_wrapper_restore("file");
        $this->dir_handle = @opendir($path);
        stream_wrapper_unregister("file");
        stream_wrapper_register("file", "PhphoneDecryptWrapper");
        return $this->dir_handle !== false;
    }

    public function dir_readdir() {
        return @readdir($this->dir_handle);
    }

    public function dir_rewinddir() {
        if ($this->dir_handle) {
            @rewinddir($this->dir_handle);
            return true;
        }
        return false;
    }

    public function dir_closedir() {
        if ($this->dir_handle) {
            @closedir($this->dir_handle);
            $this->dir_handle = null;
            return true;
        }
        return false;
    }

    // --- MANEJO DE PUNTEROS Y CACHÉ (Requeridos por SQLite) ---

    public function stream_seek($offset, $whence = SEEK_SET) {
        if ($this->stream) {
            return fseek($this->stream, $offset, $whence) === 0;
        }
        return false;
    }

    public function stream_tell() {
        if ($this->stream) {
            return ftell($this->stream);
        }
        return false;
    }

    public function stream_flush() {
        if ($this->stream) {
            return fflush($this->stream);
        }
        return false;
    }
    }
    stream_wrapper_unregister("file");
    stream_wrapper_register("file", "PhphoneDecryptWrapper");
}
';

        // Escribimos el wrapper desencriptado plano
        file_put_contents($destDir . '/_phphone_wrapper.php', $wrapperCode);

        // Inyectar GLOBALMENTE en Device.php (que es cargado por kie_engine.cpp antes de TODO request)
        $devicePath = $destDir . '/Phphone/Device.php';
        if (file_exists($devicePath)) {
            $deviceContent = file_get_contents($devicePath);
            // Inyectar justo después del namespace
            if (preg_match('/namespace\s+Phphone\s*;/i', $deviceContent)) {
                $deviceContent = preg_replace('/(namespace\s+Phphone\s*;)/i', "$1\nrequire_once dirname(__DIR__) . '/_phphone_wrapper.php';", $deviceContent);
            } else {
                // Fallback por si acaso no tiene namespace
                $deviceContent = preg_replace('/^<\?php/i', "<?php\nrequire_once dirname(__DIR__) . '/_phphone_wrapper.php';", ltrim($deviceContent));
            }
            file_put_contents($devicePath, $deviceContent);
        }
    }

    private function deleteDirectory(string $dir): void {
        if (!file_exists($dir)) return;
        $files = array_diff(scandir($dir), ['.','..']);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->deleteDirectory("$dir/$file") : unlink("$dir/$file");
        }
        rmdir($dir);
    }
}

