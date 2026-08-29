<?php
namespace Phphone\Cli\Commands;

class StopCommand implements CommandInterface {

    public function execute(array $args): void {
        echo __('stop.start');
        
        $isClean = in_array('--clean', $args);

        $platform = Platform::resolvePlatform($args);
        if ($platform === 'ask') {
            if (in_array('--ios', $args, true)) {
                $platform = 'ios';
            } elseif (in_array('--android', $args, true)) {
                $platform = 'android';
            } else {
                echo __('stop.platform_ask');
                return;
            }
        }

        $projectRoot = getcwd();

        if ($platform === 'ios') {
            $packageName = Platform::getIosBundleId($projectRoot);
            $booted = Platform::getIosSimulators();
            if (empty($booted)) {
                echo __('stop.ios_error_no_sim');
                return;
            }
            exec("xcrun simctl terminate booted " . $packageName . " 2>&1", $stopOutput, $stopReturn);
        } else {
            $packageName = Platform::getAndroidPackageName($projectRoot);
            // Android verify connection
            exec("adb devices", $output, $returnVar);
            if ($returnVar !== 0) {
                echo __('stop.adb_error');
                return;
            }
            exec("adb shell am force-stop " . $packageName, $stopOutput, $stopReturn);
        }
        
        if ($stopReturn === 0) {
            echo __('stop.success');
        } else {
            echo __('stop.failed');
            return;
        }

        // 3. Cache cleanup (if requested)
        if ($isClean) {
            echo __('stop.cleaning_cache');
            
            if ($platform === 'ios') {
                $container = trim(shell_exec("xcrun simctl get_app_container booted " . $packageName . " data 2>/dev/null"));
                if (!empty($container)) {
                    $targetDir = $container . '/Documents/kie_app/';
                    exec("rm -rf " . escapeshellarg($targetDir), $cleanOutput, $cleanReturn);
                } else {
                    $cleanReturn = 1;
                }
            } else {
                $targetDir = '/data/data/' . $packageName . '/files/kie_app/';
                exec("adb shell run-as " . $packageName . " rm -rf " . escapeshellarg($targetDir . "*"), $cleanOutput, $cleanReturn);
            }
            
            if ($cleanReturn === 0) {
                echo __('stop.cache_success');
            } else {
                echo __('stop.cache_failed');
            }
        }
    }
}
