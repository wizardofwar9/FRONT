<?php
namespace WPUmbrella\Controller\Backup;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Models\AbstractController;


class CurrentProcess extends AbstractController
{
    public function executeGet($params)
    {
        $runner = wp_umbrella_get_service('BackupRunner');

        $isRunning = $runner->hasScheduledBatchInProcess();
        $processedDataFile = wp_umbrella_get_service('BackupBatchData')->getData('file');
        $processedDataSql = wp_umbrella_get_service('BackupBatchData')->getData('database');

        if (!$processedDataFile && !$processedDataSql) {
            return $this->returnResponse([
                'is_running' => $isRunning,
                'data' => null
            ]);
        }

        return $this->returnResponse([
            'is_running' => $isRunning,
            'data' => [
                'file' => $processedDataFile ? $processedDataFile->getData() : null,
                'database' => $processedDataSql ? $processedDataSql->getData() : null,
            ]
        ]);
    }
}
