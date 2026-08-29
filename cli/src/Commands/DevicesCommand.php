<?php
namespace Phphone\Cli\Commands;

class DevicesCommand implements CommandInterface
{
    public function execute(array $args): void
    {
        echo __('devices.start');
        echo __('devices.divider');

        $foundAny = false;

        // ── Android ────────────────────────────────────────────────
        $devNull = Platform::devNull();
        exec('adb devices -l 2>' . $devNull, $adbOutput, $adbRc);

        if ($adbRc === 0) {
            foreach ($adbOutput as $line) {
                $line = trim($line);
                if (empty($line) || strpos($line, 'List of devices') !== false) continue;

                $parts = preg_split('/\s+/', $line);
                if (count($parts) >= 2) {
                    $id     = $parts[0];
                    $status = $parts[1];

                    $icon       = ($status === 'device') ? '🟢' : '🔴';
                    $statusText = ($status === 'device') ? __('devices.status_active') : __('devices.status_offline');

                    $model = __('devices.default_name');
                    foreach ($parts as $part) {
                        if (strpos($part, 'model:') === 0) {
                            $model = str_replace('_', ' ', substr($part, 6));
                        }
                    }

                    echo "$icon " . __('devices.android_prefix') . " $model\n";
                    echo __('devices.info_id',     ['id'     => $id]);
                    echo __('devices.info_status', ['status' => $statusText]);
                    echo __('devices.divider');
                    $foundAny = true;
                }
            }
        }

        // ── iOS Simulators (macOS only) ────────────────────────────
        if (Platform::isMac()) {
            $booted = Platform::getIosSimulators();

            foreach ($booted as $sim) {
                echo __('devices.ios_booted_prefix', ['name' => $sim['name']]);
                echo __('devices.info_id',     ['id'     => $sim['udid']]);
                echo __('devices.info_status', ['status' => __('devices.status_active')]);
                echo __('devices.divider');
                $foundAny = true;
            }

            // Also show available (not booted) simulators
            if (empty($booted)) {
                $available = Platform::getAvailableIosSimulators();
                $iphones   = array_values(array_filter($available, fn($d) => stripos($d['name'], 'iPhone') !== false));
                $list      = !empty($iphones) ? array_slice($iphones, 0, 5) : array_slice($available, 0, 5);

                foreach ($list as $sim) {
                    echo __('devices.ios_not_booted_prefix', ['name' => $sim['name']]);
                    echo __('devices.info_id', ['id' => $sim['udid']]);
                    echo __('devices.status_available');
                    echo __('devices.divider');
                    $foundAny = true;
                }
            }
        }

        // ── Summary ────────────────────────────────────────────────
        if (!$foundAny) {
            echo __('devices.not_found');
            echo __('devices.hint');
        } else {
            echo __('devices.success_hint');
        }
    }
}
