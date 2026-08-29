<?php
namespace Phphone\Cli\Commands;

class ScreenshotCommand implements CommandInterface {

    public function execute(array $args): void {
        echo __('screenshot.start');

        $platform = Platform::resolvePlatform($args);
        if ($platform === 'ask') {
            if (in_array('--ios', $args, true)) {
                $platform = 'ios';
            } elseif (in_array('--android', $args, true)) {
                $platform = 'android';
            } else {
                echo __('screenshot.platform_ask');
                return;
            }
        }

        // Local destination folder
        $screenshotsDir = getcwd() . DIRECTORY_SEPARATOR . 'screenshots';
        if (!is_dir($screenshotsDir)) {
            mkdir($screenshotsDir, 0777, true);
        }

        // Filename with timestamp
        $timestamp = date('Y-m-d_H-i-s');
        $localPath = $screenshotsDir . DIRECTORY_SEPARATOR . "screenshot_$timestamp.png";

        if ($platform === 'ios') {
            $booted = Platform::getIosSimulators();
            if (empty($booted)) {
                echo __('screenshot.ios_error_capture');
                return;
            }
            exec("xcrun simctl screenshot booted " . escapeshellarg($localPath) . " 2>&1", $output, $returnVar);
            if ($returnVar !== 0) {
                echo __('screenshot.error_capture');
                return;
            }
        } else {
            // Temporary path in Android emulator
            $remotePath = '/sdcard/phphone_screenshot.png';

            // 1. Capture screen inside Android emulator
            exec("adb shell screencap -p \"$remotePath\" 2>&1", $output1, $return1);
            if ($return1 !== 0) {
                echo __('screenshot.error_capture');
                return;
            }

            // 2. Download the PNG file
            exec("adb pull \"$remotePath\" \"$localPath\" 2>&1", $output2, $return2);
            if ($return2 !== 0) {
                echo __('screenshot.error_pull');
                return;
            }

            // 3. Clean up remote file
            exec("adb shell rm \"$remotePath\"");
        }

        echo __('screenshot.success');
        echo __('screenshot.path', ['path' => $localPath]);

        // 4. Automatically open the image in the OS viewer
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            pclose(popen("start \"\" \"$localPath\"", "r"));
        } elseif (strtoupper(PHP_OS) === 'DARWIN') {
            exec("open \"$localPath\"");
        } else {
            exec("xdg-open \"$localPath\"");
        }
    }
}
