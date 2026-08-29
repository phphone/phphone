<?php
namespace Phphone\Cli\Commands;

interface CommandInterface {
    /**
     * Ejecuta el comando
     * 
     * @param array $args Los argumentos pasados por la CLI
     */
    public function execute(array $args): void;
}
