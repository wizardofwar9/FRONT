<?php

namespace WPUmbrella\Actions;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Hooks\ExecuteHooks;
use WPUmbrella\Helpers\GodTransient;

class TrackingError implements ExecuteHooks
{
    public function hooks()
    {
        add_action('init', [$this, 'init']);
    }

    public function init()
    {
        $data = get_transient(GodTransient::ERRORS_SAVE);

        if (!$data || empty($data)) {
            return;
        }

        delete_transient(GodTransient::ERRORS_SAVE);

        update_option(GodTransient::ERRORS_SAVE, $data, false);

        as_schedule_single_action(time(), 'action_wp_umbrella_send_errors_v2', [], 'umbrella_errors');
    }
}
