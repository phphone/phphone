<?php
namespace Phphone\Cli\Commands;

class LogsCommand implements CommandInterface {
    
    public function execute(array $args): void {
        echo __('logs.start');
        echo __('logs.hint');

        $platform = Platform::resolvePlatform($args);
        if ($platform === 'ask') {
            if (in_array('--ios', $args, true)) {
                $platform = 'ios';
            } elseif (in_array('--android', $args, true)) {
                $platform = 'android';
            } else {
                echo __('logs.platform_ask');
                return;
            }
        }

        if ($platform === 'ios') {
            $booted = Platform::getIosSimulators();
            if (empty($booted)) {
                echo __('logs.ios_error_no_sim');
                return;
            }
            // Stream logs from the Phphone process in the booted simulator
            $cmd = 'xcrun simctl spawn booted log stream --process Phphone';
        } else {
            // Clean previous logcat
            exec("adb logcat -c > NUL 2>&1");

            // ADB Logcat filter:
            // Kie:V -> Phphone Tag (Verbose)
            // WebConsole:V -> JavaScript console.log
            // *:S -> Silence all other noise
            $cmd = 'adb logcat Kie:V WebConsole:V *:S';
        }
        
        passthru($cmd, $returnVar);

        if ($returnVar !== 0) {
            echo __('logs.error');
        }
    }
}
