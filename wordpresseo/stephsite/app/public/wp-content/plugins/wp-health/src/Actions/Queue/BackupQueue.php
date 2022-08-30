<?php
namespace WPUmbrella\Actions\Queue;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Hooks\ExecuteHooks;
use WPUmbrella\Core\Backup\Builder\BackupBuilder;
use WPUmbrella\Services\Backup\BackupRunner;

class BackupQueue implements ExecuteHooks
{
    public function hooks()
    {
        add_action(BackupRunner::ACTION_BACKUP_FILES, [$this, 'runFiles']);
        add_action(BackupRunner::ACTION_BACKUP_DATABASE, [$this, 'runDatabase']);
        add_action('action_scheduler_failed_action', [$this, 'cleanupBackupIfFailed']);
        add_action('action_scheduler_failed_execution', [$this, 'cleanupBackupIfFailed']);
    }

    public function cleanupBackupIfFailed($actionId)
    {
        try {
            $action = \ActionScheduler::store()->fetch_action($actionId);
            $hook = $action->get_hook();
            wp_umbrella_get_service('Logger')->info("Hook: $hook");

            switch ($hook) {
                case 'wp_umbrella_backup_files_batch':
                case 'wp_umbrella_backup_database_batch':
                    $backupDirector = wp_umbrella_get_service('BackupDirector');
                    $builder = new BackupBuilder();

                    wp_umbrella_get_service('Logger')->info(sprintf('[cleanup backup failed] : %s', $hook));

                    $type = $hook === 'wp_umbrella_backup_files_batch' ? 'file' : 'database';

                    $dataModel = wp_umbrella_get_service('BackupBatchData')->getData($type);
                    $profile = $backupDirector->constructBackupProfileDestination($builder, $dataModel);

                    wp_umbrella_get_service('BackupExecutor')->cleanup($profile);

                    if ($hook === 'wp_umbrella_backup_files_batch') {
                        wp_umbrella_get_service('Logger')->info(sprintf('[mode] : %s', $dataModel->getMode()));
                    }

                    if ($hook === 'wp_umbrella_backup_files_batch' && $dataModel->getMode() === 'overload') {
                        wp_umbrella_get_service('BackupApi')->postFinishBackup($dataModel, $type);
                    } else {
                        wp_umbrella_get_service('BackupApi')->postErrorBackup($dataModel, $type);
                    }

                    break;
            }
        } catch (\Exception $e) {
            wp_umbrella_get_service('Logger')->error($e->getMessage());
        }
    }

    public function runFiles()
    {
        $objData = wp_umbrella_get_service('BackupBatchData')->getData('file');
        if (!$objData) {
            \wp_umbrella_get_service('Logger')->info('Object data is empty for run backup');
            wp_umbrella_get_service('BackupBatchData')->deleteDataOption();
            return;
        }

        wp_umbrella_get_service('BackupQueueRunnerFiles')->run();
    }

    public function runDatabase()
    {
        $objData = wp_umbrella_get_service('BackupBatchData')->getData('database');
        if (!$objData) {
            \wp_umbrella_get_service('Logger')->info('Object data is empty for run backup');
            wp_umbrella_get_service('BackupBatchData')->deleteDataOption();
            return;
        }

        wp_umbrella_get_service('BackupQueueRunnerDatabase')->run();
    }
}
