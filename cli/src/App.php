<?php
namespace Phphone\Cli;

use Phphone\Cli\Commands\CreateCommand;
use Phphone\Cli\Commands\RenameCommand;
use Phphone\Cli\Commands\RunCommand;
use Phphone\Cli\Commands\StopCommand;
use Phphone\Cli\Commands\BuildCommand;
use Phphone\Cli\Commands\CleanCommand;
use Phphone\Cli\Commands\DoctorCommand;
use Phphone\Cli\Commands\DevicesCommand;
use Phphone\Cli\Commands\ServeCommand;
use Phphone\Cli\Commands\LogsCommand;
use Phphone\Cli\Commands\UninstallCommand;
use Phphone\Cli\Commands\ScreenshotCommand;
use Phphone\Cli\Commands\SignCommand;
use Phphone\Cli\Commands\SetupCommand;
use Phphone\Cli\Commands\ConfigCommand;

if (file_exists(__DIR__ . '/Commands/SyncPublicCommand.php')) {
    require_once __DIR__ . '/Commands/SyncPublicCommand.php';
}

/**
 * Phphone Orchestrator Core
 * Handles CLI routing to respective command classes.
 */
class App {
    public function run(array $args) {
        $commandName = $args[1] ?? 'help';
        
        switch ($commandName) {
            case 'create':
                $command = new CreateCommand();
                $command->execute($args);
                break;
            case 'rename':
                $command = new RenameCommand();
                $command->execute($args);
                break;
            case 'run':
                $command = new RunCommand();
                $command->execute($args);
                break;
            case 'stop':
                $command = new StopCommand();
                $command->execute($args);
                break;
            case 'clean':
                $command = new CleanCommand();
                $command->execute($args);
                break;
            case 'build':
                $command = new BuildCommand();
                $command->execute($args);
                break;
            case 'doctor':
                $command = new DoctorCommand();
                $command->execute($args);
                break;
            case 'devices':
                $command = new DevicesCommand();
                $command->execute($args);
                break;
            case 'serve':
                $command = new ServeCommand();
                $command->execute($args);
                break;
            case 'logs':
                $command = new LogsCommand();
                $command->execute($args);
                break;
            case 'uninstall':
                $command = new UninstallCommand();
                $command->execute($args);
                break;
            case 'screenshot':
                $command = new ScreenshotCommand();
                $command->execute($args);
                break;
            case 'sign':
                $command = new SignCommand();
                $command->execute($args);
                break;
            case 'setup':
                $command = new SetupCommand();
                $command->execute($args);
                break;
            case 'config':
                $command = new ConfigCommand();
                $args_for_command = array_slice($args, 2);
                $command->execute($args_for_command);
                break;
            case 'sync-public':
                if (class_exists('\\Phphone\\Cli\\Commands\\SyncPublicCommand')) {
                    $command = new \Phphone\Cli\Commands\SyncPublicCommand();
                    $command->execute($args);
                } else {
                    $this->showHelp();
                }
                break;
            default:
                $this->showHelp();
                break;
        }
    }

    private function showHelp() {
        echo "====================================\n";
        echo __('app.help.title') . "\n";
        echo "====================================\n";
        echo __('app.help.commands') . "\n";
        echo __('app.help.cmd.create') . "\n";
        echo __('app.help.cmd.rename') . "\n";
        echo __('app.help.cmd.run') . "\n";
        echo __('app.help.cmd.serve') . "\n";
        echo __('app.help.cmd.stop') . "\n";
        echo __('app.help.cmd.build') . "\n";
        echo __('app.help.cmd.clean') . "\n";
        echo __('app.help.cmd.doctor') . "\n";
        echo __('app.help.cmd.devices') . "\n";
        echo __('app.help.cmd.logs') . "\n";
        echo __('app.help.cmd.uninstall') . "\n";
        echo __('app.help.cmd.screenshot') . "\n";
        echo __('app.help.cmd.sign') . "\n";
        echo __('app.help.cmd.setup') . "\n";
        echo __('app.help.cmd.config') . "\n";
        echo "\n";
    }
}
