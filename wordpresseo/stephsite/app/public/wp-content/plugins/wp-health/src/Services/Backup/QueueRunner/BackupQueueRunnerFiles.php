<?php
namespace WPUmbrella\Services\Backup\QueueRunner;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Backup\Builder\BackupBuilder;
use WPUmbrella\Core\Backup\Source\FinderBatchSouce;
use WPUmbrella\Services\Backup\BackupBatchData;
use WPUmbrella\Services\Backup\BackupDirector;
use WPUmbrella\Helpers\DataTemporary;

class BackupQueueRunnerFiles extends AbstractBackupQueue
{
    const TYPE = 'file';

    public function run()
    {
        $this->setupProcess();

		/**
		 * @var BackupBatchData
		 */
        $batchDataService = wp_umbrella_get_service('BackupBatchData');
        $dataModel = $batchDataService->getData(self::TYPE);
        $dataModel->createDefaultName($dataModel->getSuffix());

		/**
		 * @var BackupDirector
		 */
        $backupDirector = wp_umbrella_get_service('BackupDirector');
        $builder = new BackupBuilder();

        $time_start = microtime(true);

        $currentIterator = $dataModel->getCurrentBatchProcessor();

        wp_umbrella_get_service('Logger')->info('Current Iterator: ' . $currentIterator);

        // Only backup files
        $profile = $backupDirector->constructBackupProfileOnlyFiles($builder, $dataModel);
        $backupExecutor = wp_umbrella_get_service('BackupExecutor');
        $result = $backupExecutor->backupSources($profile);
        if (!isset($result[0])) {
            wp_umbrella_get_service('Logger')->error('Default source not exists');
            return;
        }
        wp_umbrella_get_service('Logger')->info('Total execution time in seconds zip files: ' . (microtime(true) - $time_start));

        $response = $result[0];

        if (!$response['success']) {
            wp_umbrella_get_service('Logger')->error('Backup files failed - Position: ' . $response['iterator_position']);
            return;
        }

        // Send to destinations
        $time_start = microtime(true);
        $dataModel = $this->sendToDestinations($dataModel, self::TYPE);
        wp_umbrella_get_service('Logger')->info('Total execution time in seconds send files: ' . (microtime(true) - $time_start));

        $iteratorException = DataTemporary::getDataByKey('iterator_exception');
        $newPosition = $response['iterator_position'];
        wp_umbrella_get_service('Logger')->info('Iterator after process: ' . $response['iterator_position']);
        if ($iteratorException !== null) {
            $newPosition += $iteratorException;
        }

        // Prevent while true
        if ($newPosition === $currentIterator) {
            $newPosition++;
        } elseif ($newPosition < $currentIterator) {
            $newPosition = $currentIterator + 2;
        }

        wp_umbrella_get_service('Logger')->info('New Position Iterator: ' . $newPosition);

        $dataModel->setIteratorPosition($newPosition);

        if ($newPosition >= $dataModel->getTotalFiles()) { // Finish backup files
            wp_umbrella_get_service('Logger')->info('Backup files - Finish');

            // Need backup overload files
            if ($dataModel->getTotalFilesOverload() != 0 && $dataModel->getMode() === 'normal') {
                // Reset new process for overload batch files
                $dataModel->setMode('overload')
                        ->setPartBatchProcessor((int) $dataModel->getPartBatchProcessor() + 1)
                        ->setBatchSize($dataModel->getSizeMoFileSourceOverload())
                        ->setFileSourceType('finder-by-file')
                        ->setIteratorPosition(0);

                $batchDataService->setDataOption($dataModel->getData(), self::TYPE);
                $this->saveCurrentDataModel($dataModel, self::TYPE);
                wp_umbrella_get_service('BackupRunner')->scheduledBatchFiles();
                return;
            }

            // Finish
            $dataModel->setTimestampEndDate(time());
            $batchDataService->setDataOption($dataModel->getData(), self::TYPE);

            $this->finishBackupAndSaveDataModel($dataModel, self::TYPE);
            return;
        }

        // Continue
        $dataModel->setPartBatchProcessor((int) $dataModel->getPartBatchProcessor() + 1);

        $batchDataService->setDataOption($dataModel->getData(), self::TYPE);
        $this->saveCurrentDataModel($dataModel, self::TYPE);

        wp_umbrella_get_service('Logger')->info('Prepare new batch : ' . $newPosition);

        wp_umbrella_get_service('BackupRunner')->scheduledBatchFiles();
    }
}
