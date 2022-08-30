<?php
namespace WPUmbrella\Services\Restore;

use ActionScheduler;
use ActionScheduler_Store;

if (!defined('ABSPATH')) {
    exit;
}

class RestoreRunner
{
    const GROUP = 'wp-umbrella';

    const ACTION_RESTORE_DATABASE = 'wp_umbrella_restore_database_batch';

    public function hasScheduledBatchInProcess(): bool
    {
        if (function_exists('as_has_scheduled_action')) {
            return \as_has_scheduled_action(self::ACTION_RESTORE_DATABASE, [], self::GROUP);
        } elseif (function_exists('as_next_scheduled_action')) {
            return \as_next_scheduled_action(self::ACTION_RESTORE_DATABASE, [], self::GROUP);
        }

        return false;
    }

    public function hasRunningBatchInProcess(): bool
    {
        return $this->hasRunningHookInProcess(self::ACTION_RESTORE_DATABASE);
    }

    public function hasRunningHookInProcess($hook, $args = []): bool
    {
        if (!ActionScheduler::is_initialized(__FUNCTION__)) {
            return false;
        }

        $query_args = [
            'hook' => $hook,
            'status' => [ActionScheduler_Store::STATUS_RUNNING],
            'group' => self::GROUP,
            'orderby' => 'none',
        ];

        if (null !== $args) {
            $query_args['args'] = $args;
        }

        $store = ActionScheduler::store();
        if (!method_exists($store, 'query_action')) {
            $store = wp_umbrella_get_service('StoreRetrocompatibility');
        }

        return $action_id !== null;
    }

    public function scheduledRestoreDatabase()
    {
        as_schedule_single_action(time() + 1, self::ACTION_RESTORE_DATABASE, [], self::GROUP);
    }

    public function unscheduledBatch()
    {
        as_unschedule_action(self::ACTION_RESTORE_DATABASE, [], self::GROUP);
    }
}
