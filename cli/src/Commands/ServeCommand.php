<?php
namespace Phphone\Cli\Commands;

class ServeCommand implements CommandInterface {
    
    public function execute(array $args): void {
        $requestedPort = isset($args[2]) ? (int)$args[2] : 3000;
        $host = '127.0.0.1';

        $port = $this->findAvailablePort($host, $requestedPort);

        if ($port !== $requestedPort) {
            echo "⚠️  El puerto $requestedPort estaba ocupado. Usando el puerto libre $port...\n\n";
        }

        echo __('serve.start');
        echo __('serve.url', ['host' => $host, 'port' => $port]);
        echo __('serve.hint');
        echo __('serve.warning');

        $routerPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'router.php';
        $cwd = getcwd();
        $srcDir = is_dir($cwd . DIRECTORY_SEPARATOR . 'src') ? $cwd . DIRECTORY_SEPARATOR . 'src' : $cwd;

        // Abrir el navegador por defecto mágicamente (Soporte Multi-OS)
        $url = "http://$host:$port";
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            pclose(popen("start $url", "r"));
        } else if (strtoupper(PHP_OS) === 'DARWIN') {
            exec("open $url");
        } else {
            exec("xdg-open $url");
        }

        // Levantar el servidor interno de PHP de forma síncrona
        $cmd = sprintf('php -S %s:%s -t "%s" "%s"', $host, $port, $srcDir, $routerPath);
        passthru($cmd);
    }

    private function findAvailablePort(string $host, int $startPort): int {
        $port = $startPort;
        while ($port < $startPort + 100) {
            $connection = @fsockopen($host, $port, $errno, $errstr, 0.2);
            if (!is_resource($connection)) {
                return $port;
            }
            fclose($connection);
            $port++;
        }
        return $startPort;
    }
}
