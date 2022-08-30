<?php
namespace WPUmbrella\Actions\Queue;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Hooks\ExecuteHooks;
use WPUmbrella\Services\Restore\RestoreRunner;

class RestoreDatabaseQueue implements ExecuteHooks
{
    public function hooks()
    {
        add_action(RestoreRunner::ACTION_RESTORE_DATABASE, [$this, 'run']);
    }

    public function run()
    {
        wp_umbrella_get_service('RestoreQueueRunnerDatabase')->run();
    }
}
