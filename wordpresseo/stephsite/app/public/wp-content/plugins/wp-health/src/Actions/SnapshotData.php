<?php
namespace WPUmbrella\Actions;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Hooks\ExecuteHooks;
use WPUmbrella\Core\Hooks\DeactivationHook;

class SnapshotData implements ExecuteHooks, DeactivationHook
{
    public function hooks()
    {
        add_action('init', [$this, 'init']);
    }

    public function init()
    {
        if (!function_exists('as_schedule_recurring_action')) {
            return;
        }

        if (function_exists('as_has_scheduled_action') && false === \as_has_scheduled_action('wp_umbrella_snapshot_data')) {
            \as_schedule_recurring_action(strtotime('now'), MINUTE_IN_SECONDS * 120, 'wp_umbrella_snapshot_data');
        } elseif (function_exists('as_next_scheduled_action') && false === \as_next_scheduled_action('wp_umbrella_snapshot_data')) {
            \as_schedule_recurring_action(strtotime('now'), MINUTE_IN_SECONDS * 120, 'wp_umbrella_snapshot_data');
        }
    }

    public function deactivate()
    {
        as_unschedule_action('wp_umbrella_snapshot_data');
    }
}
