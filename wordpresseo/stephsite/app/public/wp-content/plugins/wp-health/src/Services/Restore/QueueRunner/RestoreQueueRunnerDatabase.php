<?php
namespace WPUmbrella\Services\Restore\QueueRunner;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Restore\Memento\RestoreOriginator;
use WPUmbrella\Core\Restore\Builder\RestoreBuilder;

class RestoreQueueRunnerDatabase
{
    public function run()
    {
        $director = wp_umbrella_get_service('RestoreDirector');

        $builder = new RestoreBuilder();
        $director->setBuilder($builder);

        $director->buildRestoreDatabaseHandlers();
        $kernel = $builder->getKernel();

        $originator = new RestoreOriginator();
        $originator->setState([
            'zip_database_path' => sprintf('%s/%s', untrailingslashit(WP_UMBRELLA_DIR_TEMP_RESTORE), 'database.zip'),
        ]);

        $kernel->execute($originator);

        if ($kernel->hasError()) {
            error_log(serialize($kernel->getError()));
        }
    }
}
