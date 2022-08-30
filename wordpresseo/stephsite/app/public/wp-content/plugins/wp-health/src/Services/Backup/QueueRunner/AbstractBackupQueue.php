<?php
namespace WPUmbrella\Services\Backup\QueueRunner;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Backup\Builder\BackupBuilder;
use WPUmbrella\Models\Backup\BackupProcessedData;
use WPUmbrella\Core\Backup\Source\FinderBatchSource;

class AbstractBackupQueue
{
    protected function setupProcess()
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(900);
        }
    }

    protected function sendToDestinations(BackupProcessedData $dataModel, $type): BackupProcessedData
	{
        $backupDirector = wp_umbrella_get_service('BackupDirector');
        $builder = new BackupBuilder();

        wp_umbrella_get_service('Logger')->info('Send zip');

        $profile = $backupDirector->constructBackupProfileDestination($builder, $dataModel, $type);

        $backupExecutor = wp_umbrella_get_service('BackupExecutor');
        $backupExecutor->sendToDestinations($profile, $dataModel);
        $backupExecutor->cleanup($profile);

        $dataModel->addFilenameZipSent($dataModel->getNameWithExtension($type), $type);
        return $dataModel;
    }

    protected function saveCurrentDataModel(BackupProcessedData $dataModel, $type)
    {
        wp_umbrella_get_service('BackupApi')->putUpdateBackupData($dataModel, $type);
    }

    protected function finishBackupAndSaveDataModel(BackupProcessedData $dataModel, $type)
    {
        wp_umbrella_get_service('BackupApi')->postFinishBackup($dataModel, $type);
    }
}
