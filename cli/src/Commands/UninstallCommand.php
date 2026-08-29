<?php
namespace Phphone\Cli\Commands;

class UninstallCommand implements CommandInterface {
    
    public function execute(array $args): void {
        echo __('uninstall.start');
        
        $platform = Platform::resolvePlatform($args);

        if ($platform === 'ask') {
            if (in_array('--ios', $args, true)) {
                $platform = 'ios';
            } elseif (in_array('--android', $args, true)) {
                $platform = 'android';
            } else {
                echo __('uninstall.platform_ask');
                return;
            }
        }

        $projectRoot = getcwd();
        
        if ($platform === 'ios') {
            $packageName = Platform::getIosBundleId($projectRoot);
            $booted = Platform::getIosSimulators();
            if (empty($booted)) {
                echo __('uninstall.ios_error_no_sim');
                return;
            }
            exec("xcrun simctl uninstall booted " . $packageName . " 2>&1", $output, $returnVar);
        } else {
            $packageName = Platform::getAndroidPackageName($projectRoot);
            exec("adb uninstall " . $packageName . " 2>&1", $output, $returnVar);
        }

        $outputStr = implode("\n", $output);

        if ($returnVar === 0) {
            echo __('uninstall.success');
            echo __('uninstall.success_details');
        } elseif (strpos($outputStr, 'Unknown package') !== false || strpos($outputStr, 'DELETE_FAILED_INTERNAL_ERROR') !== false || strpos($outputStr, 'does not have') !== false || strpos(strtolower($outputStr), 'success') !== false) {
            echo __('uninstall.already_uninstalled');
        } else {
            echo __('uninstall.failed');
            echo __('uninstall.failed_details', ['output' => $outputStr]);
        }
    }
}
