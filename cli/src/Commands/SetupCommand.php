<?php
namespace Phphone\Cli\Commands;

class SetupCommand implements CommandInterface {

    private const ANDROID_SIZES = [
        'mdpi'    => 48,
        'hdpi'    => 72,
        'xhdpi'   => 96,
        'xxhdpi'  => 144,
        'xxxhdpi' => 192,
    ];

    private const IOS_SIZES = [
        ['size' => 20, 'idiom' => 'iphone', 'scale' => '2x'],
        ['size' => 20, 'idiom' => 'iphone', 'scale' => '3x'],
        ['size' => 20, 'idiom' => 'ipad', 'scale' => '1x'],
        ['size' => 20, 'idiom' => 'ipad', 'scale' => '2x'],
        ['size' => 29, 'idiom' => 'iphone', 'scale' => '2x'],
        ['size' => 29, 'idiom' => 'iphone', 'scale' => '3x'],
        ['size' => 29, 'idiom' => 'ipad', 'scale' => '1x'],
        ['size' => 29, 'idiom' => 'ipad', 'scale' => '2x'],
        ['size' => 40, 'idiom' => 'iphone', 'scale' => '2x'],
        ['size' => 40, 'idiom' => 'iphone', 'scale' => '3x'],
        ['size' => 40, 'idiom' => 'ipad', 'scale' => '1x'],
        ['size' => 40, 'idiom' => 'ipad', 'scale' => '2x'],
        ['size' => 60, 'idiom' => 'iphone', 'scale' => '2x'],
        ['size' => 60, 'idiom' => 'iphone', 'scale' => '3x'],
        ['size' => 76, 'idiom' => 'ipad', 'scale' => '1x'],
        ['size' => 76, 'idiom' => 'ipad', 'scale' => '2x'],
        ['size' => 83.5, 'idiom' => 'ipad', 'scale' => '2x'],
        ['size' => 1024, 'idiom' => 'ios-marketing', 'scale' => '1x'],
    ];

    public function execute(array $args): void {
        $translator = \Phphone\Cli\Lang\Translator::getInstance();
        echo $translator->get('setup.start');

        $bgColor = '#FFFFFF';
        $wantsBackgroundAudio = false;
        foreach ($args as $arg) {
            if (strpos($arg, '--bg=') === 0) {
                $bgColor = strtoupper(trim(substr($arg, 5)));
            }
            if ($arg === '--background-audio') {
                $wantsBackgroundAudio = true;
            }
        }
        if (!preg_match('/^#[A-F0-9]{6}$/', $bgColor) && !preg_match('/^#[A-F0-9]{3}$/', $bgColor)) {
            echo $translator->get('setup.invalid_bg', ['color' => $bgColor]);
            $bgColor = '#FFFFFF';
        }
        if (strlen($bgColor) === 4) {
            $bgColor = '#' . $bgColor[1] . $bgColor[1] . $bgColor[2] . $bgColor[2] . $bgColor[3] . $bgColor[3];
        }

        $projectRoot = getcwd();
        $setupDir = $projectRoot . '/setup';
        $iconPath = $setupDir . '/icon.png';
        
        if (!is_dir($setupDir) || !file_exists($iconPath)) {
            echo $translator->get('setup.error_no_setup_folder');
            echo $translator->get('setup.tip');
            return;
        }

        echo $translator->get('setup.found');

        $iconImg = $this->loadImage($iconPath);
        if (!$iconImg) {
            echo $translator->get('setup.error_invalid_image');
            return;
        }

        // Detectar si hay un splash dedicado estático
        $splashPath = $setupDir . '/splash.png';
        $hasDedicatedSplash = file_exists($splashPath);
        $splashImg = $hasDedicatedSplash ? $this->loadImage($splashPath) : $iconImg;

        // --- LÓGICA ANDROID ---
        if (is_dir($projectRoot . '/android')) {
            echo $translator->get('setup.android_start');
            $this->setupAndroid($projectRoot, $iconImg, $splashImg, $hasDedicatedSplash, $bgColor);
            $this->setupAndroidBackgroundAudio($projectRoot, $wantsBackgroundAudio);
        } else {
            echo $translator->get('setup.android_skip');
        }

        // --- LÓGICA IOS ---
        if (is_dir($projectRoot . '/ios')) {
            echo $translator->get('setup.ios_start');
            $this->setupIos($projectRoot, $iconImg, $splashImg, $hasDedicatedSplash, $bgColor);
            $this->setupIosBackgroundAudio($projectRoot, $wantsBackgroundAudio);
        } else {
            echo $translator->get('setup.ios_skip');
        }

        // imagedestroy($iconImg); // Deprecated in PHP 8.5
        if ($hasDedicatedSplash) {
            // imagedestroy($splashImg); // Deprecated in PHP 8.5
        }

        echo $translator->get('setup.success');
    }

    private function setupAndroid($projectRoot, $iconImg, $splashImg, $hasDedicatedSplash, $bgColor)
    {
        $translator = \Phphone\Cli\Lang\Translator::getInstance();
        $resPath = $projectRoot . '/android/app/src/main/res';
        if (!is_dir($resPath)) {
            echo $translator->get('setup.android_error_res', ['path' => $resPath]);
            return;
        }

        $iconWidth = imagesx($iconImg);
        $iconHeight = imagesy($iconImg);

        // Generar iconos
        foreach (self::ANDROID_SIZES as $dpi => $size) {
            $folder = $resPath . '/mipmap-' . $dpi;
            if (!is_dir($folder)) mkdir($folder, 0777, true);
            
            // Limpiar iconos anteriores
            foreach (glob($folder . '/ic_launcher*.*') as $oldFile) unlink($oldFile);

            // Eliminar vectoriales adaptativos para forzar el uso del PNG
            $anyDpiFolder = $resPath . '/mipmap-anydpi-v26';
            if (is_dir($anyDpiFolder)) {
                foreach (glob($anyDpiFolder . '/*') as $file) unlink($file);
                rmdir($anyDpiFolder);
            }

            $resized = $this->createTransparentImage($size, $size);
            imagecopyresampled($resized, $iconImg, 0, 0, 0, 0, $size, $size, $iconWidth, $iconHeight);
            
            imagepng($resized, $folder . '/ic_launcher.png');
            imagepng($resized, $folder . '/ic_launcher_round.png');
            // imagedestroy($resized);
        }
        echo $translator->get('setup.android_icons_success');

        // Generar Splash
        $drawableFolder = $resPath . '/drawable';
        if (!is_dir($drawableFolder)) mkdir($drawableFolder, 0777, true);

        // Tamaño del lienzo completo
        $canvasSize = $hasDedicatedSplash ? 1024 : 256; 
        
        // Área segura (65% del lienzo) para evitar que el recorte circular de Android corte las esquinas de una imagen cuadrada
        $safeArea = (int)($canvasSize * 0.65);
        
        $splashWidth = imagesx($splashImg);
        $splashHeight = imagesy($splashImg);
        
        // Mantener relación de aspecto ajustándose al área segura
        $ratio = min($safeArea / $splashWidth, $safeArea / $splashHeight);
        $newW = (int)($splashWidth * $ratio);
        $newH = (int)($splashHeight * $ratio);

        // Crear lienzo cuadrado transparente del tamaño total para centrar la imagen
        $resizedSplash = $this->createTransparentImage($canvasSize, $canvasSize);
        $dstX = (int)(($canvasSize - $newW) / 2);
        $dstY = (int)(($canvasSize - $newH) / 2);
        imagecopyresampled($resizedSplash, $splashImg, $dstX, $dstY, 0, 0, $newW, $newH, $splashWidth, $splashHeight);
        
        imagepng($resizedSplash, $drawableFolder . '/splash_icon.png');
        // imagedestroy($resizedSplash);

        // Guardar el color de fondo en colors.xml
        $valuesFolder = $resPath . '/values';
        if (!is_dir($valuesFolder)) mkdir($valuesFolder, 0777, true);
        $colorsXml = '<?xml version="1.0" encoding="utf-8"?>
<resources>
    <color name="splash_bg_color">' . $bgColor . '</color>
</resources>';
        file_put_contents($valuesFolder . '/colors.xml', $colorsXml);

        $splashXml = '<?xml version="1.0" encoding="utf-8"?>
<layer-list xmlns:android="http://schemas.android.com/apk/res/android">
    <item android:drawable="@color/splash_bg_color" />
    <item>
        <bitmap android:gravity="center" android:src="@drawable/splash_icon" />
    </item>
</layer-list>';
        file_put_contents($drawableFolder . '/splash_background.xml', $splashXml);

        $themesFile = $resPath . '/values/themes.xml';
        if (file_exists($themesFile)) {
            $themesContent = file_get_contents($themesFile);
            // Reemplazar color por defecto si existe, o inyectar las reglas si no existen
            if (strpos($themesContent, 'android:windowBackground') === false) {
                $themesContent = str_replace('</style>', "    <item name=\"android:windowBackground\">@drawable/splash_background</item>\n        <item name=\"android:windowSplashScreenBackground\">@color/splash_bg_color</item>\n        <item name=\"android:windowSplashScreenAnimatedIcon\">@drawable/splash_icon</item>\n    </style>", $themesContent);
                file_put_contents($themesFile, $themesContent);
            } else {
                $themesContent = str_replace('@android:color/white', '@color/splash_bg_color', $themesContent);
                file_put_contents($themesFile, $themesContent);
            }
        }
        echo $translator->get('setup.android_splash_success');
    }

    private function setupIos($projectRoot, $iconImg, $splashImg, $hasDedicatedSplash, $bgColor)
    {
        $translator = \Phphone\Cli\Lang\Translator::getInstance();
        $assetsPath = $projectRoot . '/ios/App/Assets.xcassets';
        if (!is_dir($assetsPath)) {
            mkdir($assetsPath, 0777, true);
        }

        $iconWidth = imagesx($iconImg);
        $iconHeight = imagesy($iconImg);

        // 1. AppIcon.appiconset
        $appIconSetPath = $assetsPath . '/AppIcon.appiconset';
        if (!is_dir($appIconSetPath)) mkdir($appIconSetPath, 0777, true);

        $contentsJson = [
            'images' => [],
            'info' => ['author' => 'xcode', 'version' => 1]
        ];

        foreach (self::IOS_SIZES as $cfg) {
            $scaleNum = (int)str_replace('x', '', $cfg['scale']);
            $pixelSize = (int)($cfg['size'] * $scaleNum);
            
            $filename = "icon_{$cfg['size']}x{$cfg['size']}@{$cfg['scale']}.png";
            
            $resized = $this->createTransparentImage($pixelSize, $pixelSize);
            imagecopyresampled($resized, $iconImg, 0, 0, 0, 0, $pixelSize, $pixelSize, $iconWidth, $iconHeight);
            imagepng($resized, $appIconSetPath . '/' . $filename);
            // imagedestroy($resized);

            $contentsJson['images'][] = [
                'size' => "{$cfg['size']}x{$cfg['size']}",
                'idiom' => $cfg['idiom'],
                'filename' => $filename,
                'scale' => $cfg['scale']
            ];
        }

        file_put_contents($appIconSetPath . '/Contents.json', json_encode($contentsJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        echo $translator->get('setup.ios_icons_success');

        // 2. Splash Screen
        $splashSetPath = $assetsPath . '/Splash.imageset';
        if (!is_dir($splashSetPath)) mkdir($splashSetPath, 0777, true);

        $splashWidth = imagesx($splashImg);
        $splashHeight = imagesy($splashImg);

        $splashContentsJson = [
            'images' => [],
            'info' => ['author' => 'xcode', 'version' => 1]
        ];

        // Para iOS, el splash suele estar en 1x, 2x, 3x
        foreach ([1, 2, 3] as $scale) {
            // Si es fallback, no lo hacemos gigante, si es dedicado, respetamos su tamaño o lo escalamos.
            $baseWidth = $hasDedicatedSplash ? ($splashWidth / 3) : 300;
            $baseHeight = $hasDedicatedSplash ? ($splashHeight / 3) : 300;
            
            // Mantener aspecto para fallback
            if (!$hasDedicatedSplash) {
                $ratio = min($baseWidth / $splashWidth, $baseHeight / $splashHeight);
                $baseWidth = $splashWidth * $ratio;
                $baseHeight = $splashHeight * $ratio;
            }

            // Usamos un lienzo cuadrado para el splash de iOS para evitar estiramientos en cualquier pantalla
            $squareSize = max($baseWidth, $baseHeight);
            $targetSquareSize = (int)($squareSize * $scale);
            $targetW = (int)($baseWidth * $scale);
            $targetH = (int)($baseHeight * $scale);

            $filename = "splash@{$scale}x.png";
            $resizedSplash = $this->createTransparentImage($targetSquareSize, $targetSquareSize);

            $dstX = (int)(($targetSquareSize - $targetW) / 2);
            $dstY = (int)(($targetSquareSize - $targetH) / 2);

            imagecopyresampled($resizedSplash, $splashImg, $dstX, $dstY, 0, 0, $targetW, $targetH, $splashWidth, $splashHeight);
            imagepng($resizedSplash, $splashSetPath . '/' . $filename);
            // imagedestroy($resizedSplash);

            $splashContentsJson['images'][] = [
                'idiom' => 'universal',
                'scale' => "{$scale}x",
                'filename' => $filename
            ];
        }

        file_put_contents($splashSetPath . '/Contents.json', json_encode($splashContentsJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        echo $translator->get('setup.ios_splash_success');

        // 3. Generar Color Set para el fondo del Splash en iOS
        $hex = ltrim($bgColor, '#');
        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        $r = number_format(hexdec(substr($hex, 0, 2)) / 255, 3, '.', '');
        $g = number_format(hexdec(substr($hex, 2, 2)) / 255, 3, '.', '');
        $b = number_format(hexdec(substr($hex, 4, 2)) / 255, 3, '.', '');

        $colorSetPath = $assetsPath . '/SplashBackgroundColor.colorset';
        if (!is_dir($colorSetPath)) mkdir($colorSetPath, 0777, true);
        $colorJson = [
            'colors' => [
                [
                    'idiom' => 'universal',
                    'color' => [
                        'color-space' => 'srgb',
                        'components' => [
                            'red' => $r,
                            'green' => $g,
                            'blue' => $b,
                            'alpha' => '1.000'
                        ]
                    ]
                ]
            ],
            'info' => ['author' => 'xcode', 'version' => 1]
        ];
        file_put_contents($colorSetPath . '/Contents.json', json_encode($colorJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        echo $translator->get('setup.ios_colorset_success');
    }

    private function setupAndroidBackgroundAudio($projectRoot, $wantsBackgroundAudio)
    {
        $translator = \Phphone\Cli\Lang\Translator::getInstance();
        $mainActivityPath = $projectRoot . '/android/app/src/main/java/com/example/phphone/MainActivity.kt';
        if (!file_exists($mainActivityPath)) return;

        $content = file_get_contents($mainActivityPath);

        // Ensure onPause / onResume exist in MainActivity.kt
        if (strpos($content, 'override fun onPause()') === false) {
            $injection = "
    override fun onPause() {
        super.onPause()
        // PHPHONE_INJECT:BACKGROUND_AUDIO_PAUSE
    }

    override fun onResume() {
        super.onResume()
        // PHPHONE_INJECT:BACKGROUND_AUDIO_RESUME
    }
";
            $content = str_replace("override fun onDestroy()", $injection . "\n    override fun onDestroy()", $content);
        }

        if ($wantsBackgroundAudio) {
            // Remove webView.onPause() and webView.onResume()
            $content = preg_replace('/(\/\/ PHPHONE_INJECT:BACKGROUND_AUDIO_PAUSE\n)(\s*webView\.onPause\(\))?/', '$1', $content);
            $content = preg_replace('/(\/\/ PHPHONE_INJECT:BACKGROUND_AUDIO_RESUME\n)(\s*webView\.onResume\(\))?/', '$1', $content);
            echo $translator->get('setup.android_audio_enabled');
        } else {
            // Inject webView.onPause() and webView.onResume() if not present
            if (strpos($content, 'webView.onPause()') === false) {
                $content = preg_replace('/(\/\/ PHPHONE_INJECT:BACKGROUND_AUDIO_PAUSE\n)/', '$1        webView.onPause()' . "\n", $content);
            }
            if (strpos($content, 'webView.onResume()') === false) {
                $content = preg_replace('/(\/\/ PHPHONE_INJECT:BACKGROUND_AUDIO_RESUME\n)/', '$1        webView.onResume()' . "\n", $content);
            }
            echo $translator->get('setup.android_audio_disabled');
        }

        file_put_contents($mainActivityPath, $content);
    }

    private function setupIosBackgroundAudio($projectRoot, $wantsBackgroundAudio)
    {
        $translator = \Phphone\Cli\Lang\Translator::getInstance();
        // 1. Modificar AppDelegate.swift
        $appDelegatePath = $projectRoot . '/ios/App/AppDelegate.swift';
        if (file_exists($appDelegatePath)) {
            $content = file_get_contents($appDelegatePath);

            // Add marker if not exists
            if (strpos($content, '// PHPHONE_INJECT:AUDIO_SESSION') === false) {
                $content = str_replace('window = UIWindow(frame: UIScreen.main.bounds)', "// PHPHONE_INJECT:AUDIO_SESSION\n        window = UIWindow(frame: UIScreen.main.bounds)", $content);
            }

            if ($wantsBackgroundAudio) {
                if (strpos($content, 'AVAudioSession.sharedInstance()') === false) {
                    $content = preg_replace('/(\/\/ PHPHONE_INJECT:AUDIO_SESSION\n)/', '$1        try? AVAudioSession.sharedInstance().setCategory(.playback, mode: .default, options: [])' . "\n        try? AVAudioSession.sharedInstance().setActive(true)\n", $content);
                }
            } else {
                $content = preg_replace('/(\/\/ PHPHONE_INJECT:AUDIO_SESSION\n)(\s*try\? AVAudioSession\.sharedInstance\(\)\.setCategory.*\n\s*try\? AVAudioSession\.sharedInstance\(\)\.setActive\(true\)\n)?/', '$1', $content);
            }
            file_put_contents($appDelegatePath, $content);
        }

        // 2. Modificar Info.plist
        $infoPlistPath = $projectRoot . '/ios/App/Info.plist';
        if (file_exists($infoPlistPath)) {
            $content = file_get_contents($infoPlistPath);
            $hasBackgroundModes = strpos($content, '<key>UIBackgroundModes</key>') !== false;

            if ($wantsBackgroundAudio) {
                if (!$hasBackgroundModes) {
                    $insertion = "\t<key>UIBackgroundModes</key>\n\t<array>\n\t\t<string>audio</string>\n\t</array>\n";
                    $content = str_replace("</dict>\n</plist>", $insertion . "</dict>\n</plist>", $content);
                }
                echo $translator->get('setup.ios_audio_enabled');
            } else {
                if ($hasBackgroundModes) {
                    // This regex removes UIBackgroundModes and its following <array> block
                    $content = preg_replace('/[ \t]*<key>UIBackgroundModes<\/key>\s*<array>\s*<string>audio<\/string>\s*<\/array>\s*/', '', $content);
                }
                echo $translator->get('setup.ios_audio_disabled');
            }
            file_put_contents($infoPlistPath, $content);
        }
    }

    private function createTransparentImage($w, $h) {
        $img = imagecreatetruecolor($w, $h);
        imagealphablending($img, false);
        imagesavealpha($img, true);
        $transparent = imagecolorallocatealpha($img, 255, 255, 255, 127);
        imagefilledrectangle($img, 0, 0, $w, $h, $transparent);
        return $img;
    }

    private function loadImage($path) {
        $info = getimagesize($path);
        if ($info === false) return false;

        switch ($info[2]) {
            case IMAGETYPE_JPEG: return imagecreatefromjpeg($path);
            case IMAGETYPE_PNG:  return imagecreatefrompng($path);
            case IMAGETYPE_WEBP: return imagecreatefromwebp($path);
            default: return false;
        }
    }
}
