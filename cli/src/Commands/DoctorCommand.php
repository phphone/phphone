<?php
namespace Phphone\Cli\Commands;

class DoctorCommand implements CommandInterface {
    
    public function execute(array $args): void {
        echo __('doctor.start');

        $isMac = (strtoupper(PHP_OS) === 'DARWIN');
        
        // On macOS, Android is optional because iOS is available (and vice versa)
        $androidOptional = $isMac;
        $iosOptional = true;

        // 1. Check PHP (Mandatory everywhere)
        $hasPhp = $this->checkDependency(
            "PHP CLI",
            "php -v",
            __('doctor.help_php'),
            false
        );

        // 2. Check Composer (Optional everywhere)
        $this->checkDependency(
            "Composer",
            "composer -V",
            __('doctor.help_composer'),
            true
        );

        echo "\n";

        // 3. Check Java JDK (Required for Android)
        $hasJava = $this->checkDependency(
            "Java JDK",
            "java -version",
            __('doctor.help_java'),
            $androidOptional
        );

        // 4. Check ADB (Required for Android)
        $hasAdb = $this->checkDependency(
            "Android SDK (ADB)",
            "adb version",
            __('doctor.help_adb'),
            $androidOptional
        );

        $androidReady = ($hasJava && $hasAdb);

        // 5. iOS Platform Checks (macOS only)
        $iosReady = false;
        if ($isMac) {
            echo "\n";

            $hasXcode = $this->checkDependency(
                "Xcode Command Line Tools",
                "xcodebuild -version",
                __('doctor.help_xcode'),
                $iosOptional
            );

            $hasSimctl = $this->checkDependency(
                "iOS Simulator CLI (simctl)",
                "xcrun simctl help",
                __('doctor.help_simctl'),
                $iosOptional
            );

            $hasGawk = $this->checkDependency(
                "GNU Awk (gawk)",
                "gawk --version",
                __('doctor.help_gawk'),
                $iosOptional
            );

            $iosReady = ($hasXcode && $hasSimctl && $hasGawk);
        }

        echo "\n";
        echo __('doctor.platform_status');
        
        $androidStatusStr = $androidReady ? __('doctor.status_ready') : __('doctor.status_not_ready');
        echo __('doctor.android_status', ['status' => $androidStatusStr]);

        if ($isMac) {
            $iosStatusStr = $iosReady ? __('doctor.status_ready') : __('doctor.status_not_ready');
            echo __('doctor.ios_status', ['status' => $iosStatusStr]);
        }

        echo "\n";

        $anyPlatformReady = ($androidReady || $iosReady);
        $allGood = ($hasPhp && $anyPlatformReady);

        if ($allGood) {
            echo __('doctor.success');
        } else {
            echo __('doctor.failure');
        }
    }

    private function checkDependency(string $name, string $command, string $helpText, bool $isOptional = false): bool {
        $output = [];
        $returnVar = 0;
        
        // Execute command and capture STDERR as well
        exec("$command 2>&1", $output, $returnVar);

        // Find the first non-empty output line
        $firstLine = '';
        foreach ($output as $line) {
            if (trim($line) !== '') {
                $firstLine = trim($line);
                break;
            }
        }

        if ($returnVar === 0 && !empty($firstLine)) {
            // Attempt to extract version
            $versionInfo = "";
            if (preg_match('/(\d+\.\d+(\.\d+)?(_\d+)?)/', $firstLine, $matches)) {
                $versionInfo = " (v" . $matches[1] . ")";
            }
            
            echo __('doctor.found', ['name' => $name, 'version' => $versionInfo]);
            return true;
        } else {
            $icon = $isOptional ? "[⚠️]" : "[❌]";
            echo __('doctor.not_found', ['icon' => $icon, 'name' => $name]);
            echo __('doctor.suggestion', ['help' => $helpText]);
            return false;
        }
    }
}
