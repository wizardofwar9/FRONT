<?php
namespace WPUmbrella\Services\Backup\QueueRunner;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Backup\Builder\BackupBuilder;
use WPUmbrella\Models\Backup\BackupProcessedData;
use WPUmbrella\Core\Backup\Source\FinderBatchSource;
use WPUmbrella\Services\Backup\BackupBatchData;

class BackupQueueRunnerDatabase extends AbstractBackupQueue
{
    const TYPE = 'database';

    public function zipDatabase(BackupProcessedData $dataModel): BackupProcessedData
	{
        wp_umbrella_get_service('Logger')->info('ZIP database');

        $backupDirector = wp_umbrella_get_service('BackupDirector');
        $builder = new BackupBuilder();

        $profile = $backupDirector->constructBackupProfileProcessor($builder, $dataModel, self::TYPE);

        $backupExecutor = wp_umbrella_get_service('BackupExecutor');
        $backupExecutor->zip($profile);

        /**
         * @var BackupBatchData
         */
        $batchData = wp_umbrella_get_service('BackupBatchData');
        $batchData->setDataOption($dataModel->getData(), self::TYPE);

        return $dataModel;
    }

    public function run()
    {
        $this->setupProcess();

        /**
         * @var BackupBatchData
         */
        $batchData = wp_umbrella_get_service('BackupBatchData');
        $dataModel = $batchData->getData(self::TYPE);

        $backupDirector = wp_umbrella_get_service('BackupDirector');
        $builder = new BackupBuilder();

        $profile = $backupDirector->constructBackupProfileOnlySQL($builder, $dataModel);
        $dataModel->createDefaultName('', true);

        $backupExecutor = wp_umbrella_get_service('BackupExecutor');

        $result = $backupExecutor->backupSources($profile);
        $response = $result[0];
        $newPosition = $response['iterator_position'];
        $dataModel = $response['processed_data'];

        if (!$response['success']) {
            $batchData->setDataOption($dataModel->getData(), self::TYPE);
            $this->saveCurrentDataModel($dataModel, self::TYPE);
            wp_umbrella_get_service('Logger')->error('Backup database failed - Position: ' . $response['iterator_position']);
            return;
        }

        wp_umbrella_get_service('Logger')->info('Iterator after process: ' . $newPosition);

        if ($newPosition >= $dataModel->getTotalTables()) { // Finish backup database
            $dataModel = $this->zipDatabase($dataModel);

            $dataModel->setTimestampEndDate(time());
            $batchData->setDataOption($dataModel->getData(), self::TYPE);

            // Send to destinations
            $dataModel = $this->sendToDestinations($dataModel, self::TYPE);
            $this->finishBackupAndSaveDataModel($dataModel, self::TYPE);
            return;
        }

        // Continue
        $batchData->setDataOption($dataModel->getData(), self::TYPE);
        $this->saveCurrentDataModel($dataModel, self::TYPE);

        wp_umbrella_get_service('Logger')->info('Prepare new batch : ' . $newPosition);

        wp_umbrella_get_service('BackupRunner')->scheduledBatchDatabase();
    }
}
