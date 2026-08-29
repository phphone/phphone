<?php
namespace Phphone\Cli\Commands;

class RunCommand implements CommandInterface
{
    // Android constants
    private string $packageName = '';
    private string $remoteDir = '';
    private const SYNC_DIRS    = ['src'];
    private const SYNC_FILES   = [];

    // iOS constants
    private string $iosBundleId = '';
    private const IOS_APP_NAME    = 'Phphone';

    public function execute(array $args): void
    {
        echo __('run.start');

        $platform = Platform::resolvePlatform($args);

        if ($platform === 'ask') {
            echo __('run.platform_ask');
            exit(0);
        }

        $projectRoot = getcwd();
        $this->packageName = Platform::getAndroidPackageName($projectRoot);
        $this->iosBundleId = Platform::getIosBundleId($projectRoot);
        $this->remoteDir = '/data/data/' . $this->packageName . '/files/kie_app/';

        if ($platform === 'ios') {
            $this->runIos($projectRoot);
        } else {
            $this->runAndroid($projectRoot);
        }
    }

    // ===================================================================
    //  iOS flow
    // ===================================================================

    private function runIos(string $projectRoot): void
    {
        echo __('run.ios_start');
        $this->ensureLocalDependenciesExtracted('ios', $projectRoot);

        // 1. Ensure a simulator is booted
        $simulator = $this->ensureIosSimulatorBooted();

        // 2. Build & install if needed
        $this->ensureIosAppInstalled($projectRoot, $simulator['udid']);

        // 3. Launch
        echo __('run.ios_launching');
        exec('xcrun simctl launch ' . escapeshellarg($simulator['udid']) . ' ' . $this->iosBundleId . ' 2>&1');
        sleep(2);

        // 4. Sync workspace
        $this->syncIosWorkspace($projectRoot, $simulator['udid']);

        echo __('run.watching');
        echo __('run.watching_divider');

        $lastModTimes = $this->getWorkspaceModTimes($projectRoot);

        while (true) {
            clearstatcache(true);
            $currentModTimes = $this->getWorkspaceModTimes($projectRoot);

            $changedFile = null;
            foreach ($currentModTimes as $file => $mtime) {
                if (!isset($lastModTimes[$file]) || $lastModTimes[$file] < $mtime) {
                    $changedFile = $file;
                    break; // Solo procesar un archivo a la vez para evitar ráfagas
                }
            }

            if ($changedFile) {
                $relPath = str_replace($projectRoot . DIRECTORY_SEPARATOR, '', $changedFile);
                echo __('run.change_detected', ['path' => $relPath]);

                // Candado de concurrencia: Esperar 500ms para que el IDE termine su ráfaga de eventos (escritura temporal, renombrado, etc.)
                usleep(500000);

                if ($this->waitForFileUnlock($changedFile)) {
                    $this->pushAndReloadIos($changedFile, $projectRoot, $simulator['udid']);
                } else {
                    echo "[Warning] " . __('run.push_failed') . " - File locked or unreadable.\n";
                }

                // Actualizar los tiempos después de recargar para ignorar los eventos encolados
                clearstatcache(true);
                $lastModTimes = $this->getWorkspaceModTimes($projectRoot);

                // Cooldown para evitar que una recarga se monte encima de otra
                sleep(1);
            } else {
                $lastModTimes = $currentModTimes;
            }

            usleep(300000); // Ciclo de revisión más rápido (300ms)
        }
    }

    private function ensureIosSimulatorBooted(): array
    {
        $booted = Platform::getIosSimulators();

        if (!empty($booted)) {
            $sim = $booted[0];
            echo __('run.ios_simulator_found', ['name' => $sim['name']]);
            return $sim;
        }

        // No booted simulator — pick one and boot it
        echo __('run.ios_no_simulator');
        $available = Platform::getAvailableIosSimulators();

        // Filter to iPhone devices only for a cleaner list
        $iphones = array_values(array_filter($available, fn($d) => stripos($d['name'], 'iPhone') !== false));
        $list    = !empty($iphones) ? $iphones : $available;

        if (empty($list)) {
            echo __('run.ios_no_available_simulator');
            exit(1);
        }

        echo __('run.ios_available_simulators');
        foreach ($list as $i => $sim) {
            echo '  [' . ($i + 1) . '] ' . $sim['name'] . "\n";
        }

        echo __('run.ios_select_simulator', ['count' => count($list)]);
        $choice = trim(fgets(STDIN));

        if (!is_numeric($choice) || $choice < 1 || $choice > count($list)) {
            echo __('run.invalid_selection');
            exit(1);
        }

        $selected = $list[$choice - 1];
        echo __('run.ios_booting', ['name' => $selected['name']]);
        exec('xcrun simctl boot ' . escapeshellarg($selected['udid']) . ' 2>&1');
        exec('open -a Simulator 2>&1');

        // Wait until it's actually booted
        echo __('run.ios_waiting');
        $maxWait = 30;
        while ($maxWait-- > 0) {
            $booted = Platform::getIosSimulators();
            foreach ($booted as $b) {
                if ($b['udid'] === $selected['udid']) {
                    echo __('run.ios_ready');
                    return $b;
                }
            }
            sleep(2);
        }

        // Fallback — return the selected anyway
        echo __('run.ios_ready');
        return $selected;
    }

    private function ensureIosAppInstalled(string $projectRoot, string $udid): void
    {
        // Check if app is installed
        exec('xcrun simctl get_app_container ' . escapeshellarg($udid) . ' ' . $this->iosBundleId . ' 2>&1', $out, $rc);
        if ($rc === 0) {
            return; // Already installed
        }

        echo __('run.ios_building');
        $buildScript = './build_manual.sh';

        if (!file_exists($projectRoot . '/ios/build_manual.sh')) {
            echo __('run.ios_no_build_script');
            exit(1);
        }

        $cmd = 'cd ' . escapeshellarg($projectRoot . '/ios') . ' && bash ' . escapeshellarg($buildScript);
        passthru($cmd, $buildCode);

        if ($buildCode !== 0) {
            echo __('run.build_failed');
            exit(1);
        }

        // Install the built .app
        $appName = Platform::getIosAppName($projectRoot);
        $appPath = $projectRoot . '/ios/build_manual.nosync/' . $appName . '.app';
        if (!is_dir($appPath)) {
            // Fallback: search for any .app in build_manual.nosync
            $apps = glob($projectRoot . '/ios/build_manual.nosync/*.app');
            if (!empty($apps)) {
                $appPath = $apps[0];
            } else {
                echo __('run.ios_app_not_found');
                exit(1);
            }
        }

        exec('xcrun simctl install ' . escapeshellarg($udid) . ' ' . escapeshellarg($appPath) . ' 2>&1', $installOut, $installRc);
        if ($installRc !== 0) {
            echo __('run.ios_install_failed');
            echo implode("\n", $installOut) . "\n";
            exit(1);
        }

        echo __('run.build_success');
    }

    private function syncIosWorkspace(string $projectRoot, string $udid): void
    {
        echo __('run.ios_syncing');
        // iOS apps access files via the app's Documents or bundle — we use simctl to push
        $rawContainer = shell_exec('xcrun simctl get_app_container ' . escapeshellarg($udid) . ' ' . escapeshellarg($this->iosBundleId) . ' data 2>/dev/null');
        $container = trim($rawContainer ?? '');
        if (empty($container)) {
            return;
        }

        $destDir = $container . '/Documents/kie_app/';
        foreach (self::SYNC_DIRS as $dir) {
            $src = $projectRoot . '/' . $dir;
            if (is_dir($src)) {
                exec('cp -r ' . escapeshellarg($src) . ' ' . escapeshellarg($destDir));
            }
        }
        echo __('run.sync_complete');
    }

    private function pushAndReloadIos(string $localFile, string $projectRoot, string $udid): void
    {
        $rawContainer = shell_exec('xcrun simctl get_app_container ' . escapeshellarg($udid) . ' ' . escapeshellarg($this->iosBundleId) . ' data 2>/dev/null');
        $container = trim($rawContainer ?? '');
        if (empty($container)) {
            return;
        }

        $relativePath = str_replace($projectRoot . DIRECTORY_SEPARATOR, '', $localFile);
        $relativePath = str_replace('\\', '/', $relativePath);
        $destPath     = $container . '/Documents/kie_app/' . $relativePath;
        $destDir      = dirname($destPath);

        exec('mkdir -p ' . escapeshellarg($destDir));
        exec('cp ' . escapeshellarg($localFile) . ' ' . escapeshellarg($destPath), $out, $rc);

        if ($rc === 0) {
            echo __('run.firing_reload');
            // Notify the app via simctl (URL scheme reload trigger)
            exec('xcrun simctl openurl ' . escapeshellarg($udid) . ' "phphone://reload" 2>/dev/null');
        } else {
            echo __('run.push_failed');
        }
    }

    // ===================================================================
    //  Android flow (unchanged from original)
    // ===================================================================

    private function runAndroid(string $projectRoot): void
    {
        echo __('run.android_start');
        $this->ensureLocalDependenciesExtracted('android', $projectRoot);

        $this->ensureAndroidDeviceConnected();
        $this->ensureAppInstalled($projectRoot);

        echo __('run.launching');
        $redirect = Platform::isWindows() ? '> NUL 2>&1' : '> /dev/null 2>&1';
        // Phphone Engine siempre usa com.example.phphone internamente
        exec('adb shell am start -n ' . $this->packageName . '/com.example.phphone.MainActivity ' . $redirect);
        sleep(2);

        $this->syncWorkspace($projectRoot);
        exec('adb shell am broadcast -a ' . $this->packageName . '.RELOAD > NUL 2>&1');

        echo __('run.watching');
        echo __('run.watching_divider');

        $lastModTimes = $this->getWorkspaceModTimes($projectRoot);

        while (true) {
            clearstatcache(true);
            $currentModTimes = $this->getWorkspaceModTimes($projectRoot);

            $changedFile = null;
            foreach ($currentModTimes as $file => $mtime) {
                if (!isset($lastModTimes[$file]) || $lastModTimes[$file] < $mtime) {
                    $changedFile = $file;
                    break; // Solo procesar un archivo a la vez para evitar ráfagas
                }
            }

            if ($changedFile) {
                $relPath = str_replace($projectRoot . DIRECTORY_SEPARATOR, '', $changedFile);
                echo __('run.change_detected', ['path' => $relPath]);

                // Candado de concurrencia: Esperar 500ms para que el IDE termine su ráfaga de eventos (escritura temporal, renombrado, etc.)
                usleep(500000);

                if ($this->waitForFileUnlock($changedFile)) {
                    $this->pushAndReload($changedFile, $projectRoot);
                } else {
                    echo "[Warning] " . __('run.push_failed') . " - File locked or unreadable.\n";
                }

                // Actualizar los tiempos después de recargar para ignorar los eventos encolados
                clearstatcache(true);
                $lastModTimes = $this->getWorkspaceModTimes($projectRoot);

                // Cooldown para evitar que una recarga se monte encima de otra
                sleep(1);
            } else {
                $lastModTimes = $currentModTimes;
            }

            usleep(300000); // Ciclo de revisión más rápido (300ms)
        }
    }

    private function ensureAndroidDeviceConnected(): void
    {
        $devices = Platform::getAndroidDevices();

        if (count($devices) > 0) {
            echo __('run.adb_connected', ['count' => count($devices)]);
            return;
        }

        echo __('run.no_devices');
        echo __('run.searching_emulators');

        $avds = Platform::getAvds();
        if (empty($avds)) {
            echo __('run.no_emulators');
            echo __('run.no_emulators_hint');
            exit(1);
        }

        echo __('run.available_emulators');
        foreach ($avds as $index => $avd) {
            echo '  [' . ($index + 1) . '] ' . $avd . "\n";
        }

        echo __('run.select_emulator', ['count' => count($avds)]);
        $choice = trim(fgets(STDIN));

        if (!is_numeric($choice) || $choice < 1 || $choice > count($avds)) {
            echo __('run.invalid_selection');
            exit(1);
        }

        $selectedAvd    = $avds[$choice - 1];
        $emulatorBin    = Platform::resolveAndroidEmulatorBin();
        echo __('run.booting_emulator', ['avd' => $selectedAvd]);

        if (Platform::isWindows()) {
            pclose(popen('start /B "" ' . escapeshellarg($emulatorBin) . ' -avd ' . escapeshellarg($selectedAvd), 'r'));
        } else {
            exec(escapeshellarg($emulatorBin) . ' -avd ' . escapeshellarg($selectedAvd) . ' > /dev/null 2>&1 &');
        }

        echo __('run.waiting_emulator');
        exec('adb wait-for-device');

        echo __('run.emulator_online');
        while (true) {
            $bootCompleted = trim(shell_exec('adb shell getprop sys.boot_completed 2>&1'));
            $bootAnim = trim(shell_exec('adb shell getprop init.svc.bootanim 2>&1'));
            
            if ($bootCompleted === '1' && $bootAnim === 'stopped') {
                break;
            }
            sleep(2);
        }
        // Wait a bit more for system services like PackageManager to be fully ready
        sleep(3);
        echo __('run.android_ready');
    }

    private function ensureAppInstalled(string $projectRoot): void
    {
        $output = shell_exec('adb shell pm list packages 2>&1');
        if (strpos((string)$output, $this->packageName) !== false) {
            return;
        }

        echo __('run.building');
        $androidDir = $projectRoot . DIRECTORY_SEPARATOR . 'android';

        $cmd = Platform::isWindows()
            ? 'cd ' . escapeshellarg($androidDir) . ' && gradlew installDebug'
            : 'cd ' . escapeshellarg($androidDir) . ' && ./gradlew installDebug';

        exec($cmd, $buildOutput, $buildCode);

        if ($buildCode !== 0) {
            echo __('run.build_failed');
            echo implode("\n", $buildOutput) . "\n";
            exit(1);
        }

        echo __('run.build_success');
    }

    private function getWorkspaceModTimes(string $projectRoot): array
    {
        $times = [];

        foreach (self::SYNC_DIRS as $dirName) {
            $dirPath = $projectRoot . DIRECTORY_SEPARATOR . $dirName;
            if (!is_dir($dirPath)) continue;

            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dirPath));
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $times[$file->getPathname()] = @filemtime($file->getPathname());
                }
            }
        }

        foreach (self::SYNC_FILES as $fileName) {
            $filePath = $projectRoot . DIRECTORY_SEPARATOR . $fileName;
            if (is_file($filePath)) {
                $times[$filePath] = @filemtime($filePath);
            }
        }

        return $times;
    }

    private function syncWorkspace(string $projectRoot): void
    {
        echo __('run.syncing');

        $tmpDir = '/data/local/tmp/kie_sync';
        exec('adb shell rm -rf ' . escapeshellarg($tmpDir));
        exec('adb shell mkdir -p ' . escapeshellarg($tmpDir));

        foreach (self::SYNC_DIRS as $dirName) {
            $dirPath = $projectRoot . DIRECTORY_SEPARATOR . $dirName;
            if (is_dir($dirPath)) {
                exec('adb push ' . escapeshellarg($dirPath) . ' ' . escapeshellarg($tmpDir . "/$dirName") . ' > NUL 2>&1');
            }
        }

        foreach (self::SYNC_FILES as $fileName) {
            $filePath = $projectRoot . DIRECTORY_SEPARATOR . $fileName;
            if (is_file($filePath)) {
                exec('adb push ' . escapeshellarg($filePath) . ' ' . escapeshellarg($tmpDir) . ' > NUL 2>&1');
            }
        }

        exec('adb shell chmod -R 777 ' . escapeshellarg($tmpDir));

        $targetDir = '/data/data/' . $this->packageName . '/files/kie_app/';
        exec('adb shell run-as ' . $this->packageName . ' rm -rf ' . escapeshellarg($targetDir));
        exec('adb shell run-as ' . $this->packageName . ' mkdir -p ' . escapeshellarg($targetDir));
        exec('adb shell run-as ' . $this->packageName . ' cp -r ' . escapeshellarg($tmpDir) . '/. ' . escapeshellarg($targetDir) . ' > NUL 2>&1');

        echo __('run.sync_complete');
    }

    private function pushAndReload(string $localFile, string $projectRoot): void
    {
        $relativePath = str_replace($projectRoot . DIRECTORY_SEPARATOR, '', $localFile);
        $relativePath = str_replace('\\', '/', $relativePath);

        $remoteFilePath = $this->remoteDir . $relativePath;
        $remoteDirPath  = dirname($remoteFilePath);

        exec('adb shell run-as ' . $this->packageName . ' mkdir -p ' . escapeshellarg($remoteDirPath));

        echo __('run.pushing');
        $tmpRemote = '/data/local/tmp/phphone_hot_reload_tmp';
        exec('adb push ' . escapeshellarg($localFile) . ' ' . escapeshellarg($tmpRemote) . ' > NUL 2>&1', $pushOutput, $pushReturn);
        exec('adb shell run-as ' . $this->packageName . ' cp ' . escapeshellarg($tmpRemote) . ' ' . escapeshellarg($remoteFilePath), $cpOutput, $cpReturn);

        if ($pushReturn === 0 && $cpReturn === 0) {
            echo __('run.firing_reload');
            exec('adb shell am broadcast -a ' . $this->packageName . '.RELOAD > NUL 2>&1');
        } else {
            echo __('run.push_failed');
        }
    }

    private function ensureLocalDependenciesExtracted(string $platform, string $projectRoot): void
    {
        if ($platform === 'ios') {
            $zipPath = $projectRoot . '/ios/php_env.zip';
            $destDir = $projectRoot . '/ios/build_ios';
            if (!is_dir($destDir) && file_exists($zipPath)) {
                echo __('run.unzip_ios');
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
        } else {
            $zipPath = $projectRoot . '/android/php_env.zip';
            $destDir = $projectRoot . '/android/app/src/main/cpp/php';
            if ((!is_dir($destDir . '/include') || !is_dir($destDir . '/lib')) && file_exists($zipPath)) {
                echo __('run.unzip_android');
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
        }
    }

    private function waitForFileUnlock(string $localFile): bool
    {
        $maxRetries = 3;
        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            clearstatcache(true, $localFile);
            if (!file_exists($localFile)) {
                return false; // El archivo fue eliminado
            }

            // Intentar leer el archivo para verificar que el SO no lo tenga bloqueado y no esté vacío temporalmente
            $content = @file_get_contents($localFile);
            if ($content !== false) {
                return true; // Lectura exitosa, el archivo está listo
            }

            // Si falla (ej. bloqueado por otro proceso), esperamos 100ms y reintentamos
            usleep(100000); 
        }
        return false;
    }
}
