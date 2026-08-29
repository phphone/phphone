<?php
namespace Phphone\Cli\Lang;

class Translator {
    private static ?Translator $instance = null;
    private array $messages = [];
    private string $locale = 'en';

    private function __construct() {
        $this->detectLanguage();
        $this->loadMessages();
    }

    public static function getInstance(): Translator {
        if (self::$instance === null) {
            self::$instance = new Translator();
        }
        return self::$instance;
    }

    private function detectLanguage(): void {
        // Allow forcing language for testing
        $envLang = getenv('PHPHONE_LANG');
        if ($envLang) {
            $this->locale = substr($envLang, 0, 2);
            return;
        }

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            exec('powershell -Command "Get-Culture | Select-Object -ExpandProperty TwoLetterISOLanguageName"', $output, $return_var);
            if ($return_var === 0 && !empty($output[0])) {
                $this->locale = strtolower(trim($output[0]));
            }
        } else {
            $localeEnv = getenv('LANG') ?: getenv('LC_ALL');
            if ($localeEnv) {
                $this->locale = strtolower(substr($localeEnv, 0, 2));
            }
        }

        if (!in_array($this->locale, ['en', 'es'])) {
            $this->locale = 'en';
        }
    }

    private function loadMessages(): void {
        $file = __DIR__ . DIRECTORY_SEPARATOR . $this->locale . '.php';
        if (file_exists($file)) {
            $this->messages = require $file;
        } else {
            // Fallback to English if file is missing
            $fallbackFile = __DIR__ . DIRECTORY_SEPARATOR . 'en.php';
            if (file_exists($fallbackFile)) {
                $this->messages = require $fallbackFile;
            }
        }
    }

    public function get(string $key, array $replacements = []): string {
        $text = $this->messages[$key] ?? $key;
        foreach ($replacements as $search => $replace) {
            $text = str_replace(':' . $search, $replace, $text);
        }
        return $text;
    }
}
