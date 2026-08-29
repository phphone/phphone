<?php
use Phphone\Cli\Lang\Translator;

if (!function_exists('__')) {
    /**
     * Translate a given message.
     *
     * @param string $key
     * @param array $replacements
     * @return string
     */
    function __(string $key, array $replacements = []): string {
        return Translator::getInstance()->get($key, $replacements);
    }
}
