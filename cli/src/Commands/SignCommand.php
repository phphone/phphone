<?php
namespace Phphone\Cli\Commands;

class SignCommand implements CommandInterface {
    
    public function execute(array $args): void {
        echo __('sign.start');

        $platform = class_exists('Phphone\Cli\Commands\Platform') ? \Phphone\Cli\Commands\Platform::resolvePlatform($args) : 'android';
        if (in_array('--ios', $args, true)) {
            $platform = 'ios';
        } elseif (in_array('--android', $args, true)) {
            $platform = 'android';
        }

        if ($platform === 'ios') {
            echo __('sign.ios_title');
            echo __('sign.ios_divider');
            echo __('sign.ios_warning');
            echo __('sign.ios_step1');
            echo __('sign.ios_step2');
            echo __('sign.ios_step3');
            echo __('sign.ios_step4');
            echo __('sign.ios_step5');
            echo __('sign.ios_step6');
            echo __('sign.ios_step7');
            echo __('sign.ios_step8');
            return;
        }

        // ─── FLUJO ANDROID ───
        $androidDir = getcwd() . DIRECTORY_SEPARATOR . 'android' . DIRECTORY_SEPARATOR . 'app';
        if (!is_dir($androidDir)) {
            echo __('sign.error_no_android', ['dir' => $androidDir]);
            return;
        }

        $keystorePath = $androidDir . DIRECTORY_SEPARATOR . 'release.jks';

        // 1. Generar Keystore si no existe
        if (!file_exists($keystorePath)) {
            echo __('sign.generating');
            $keytoolCmd = 'keytool -genkey -v -keystore "' . $keystorePath . '" -keyalg RSA -keysize 2048 -validity 10000 -alias kie-release-key -storepass phphone123 -keypass phphone123 -dname "CN=Phphone Developer, OU=Phphone, O=K2, L=Tech, S=Web, C=US" 2>&1';
            
            exec($keytoolCmd, $output, $returnVar);

            if ($returnVar !== 0) {
                echo __('sign.error_keytool');
                echo implode("\n", $output) . "\n";
                return;
            }
            echo __('sign.generated_success', ['path' => $keystorePath]);
        } else {
            echo __('sign.already_exists');
        }

        // 2. Inyectar en build.gradle.kts
        $gradlePath = $androidDir . DIRECTORY_SEPARATOR . 'build.gradle.kts';
        if (!file_exists($gradlePath)) {
            echo __('sign.error_no_gradle');
            return;
        }

        $gradleContent = file_get_contents($gradlePath);

        // Si ya está inyectado, no hacer nada
        if (strpos($gradleContent, 'signingConfigs {') !== false) {
            echo __('sign.already_configured');
        } else {
            echo __('sign.configuring');
            
            $signingConfigBlock = <<<GRADLE
    signingConfigs {
        create("release") {
            storeFile = file("release.jks")
            storePassword = "phphone123"
            keyAlias = "kie-release-key"
            keyPassword = "phphone123"
        }
    }

    buildTypes {
GRADLE;

            // Reemplazar la declaración normal de buildTypes
            $gradleContent = str_replace('buildTypes {', $signingConfigBlock, $gradleContent);
            
            // Reemplazar el bloque de release para añadir el signingConfig
            $releaseBlock = <<<GRADLE
        release {
            signingConfig = signingConfigs.getByName("release")
GRADLE;
            $gradleContent = str_replace('release {', $releaseBlock, $gradleContent);

            file_put_contents($gradlePath, $gradleContent);
            echo __('sign.configured_success');
        }

        echo __('sign.finish');
    }
}
