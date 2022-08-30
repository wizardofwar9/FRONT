<?php
namespace WPUmbrella\Actions;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Hooks\ExecuteHooks;

class Migration implements ExecuteHooks
{
    public function hooks()
    {
        add_action('admin_init', [$this, 'upgrader']);
    }

    public function upgrader()
    {
        $currentVersion = get_option('wphealth_version');

        if (version_compare($currentVersion, WP_UMBRELLA_VERSION, '<')) {
            update_option('wphealth_version', WP_UMBRELLA_VERSION, false);
        }

        if (version_compare($currentVersion, '1.8.1', '<=')) {
            as_unschedule_action('wp_umbrella_snapshot_data');
        }
    }
}
