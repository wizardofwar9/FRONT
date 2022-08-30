<?php
namespace WPUmbrella\Controller\Backup;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Models\AbstractController;
use WPUmbrella\Core\Exceptions\BackupNotCreated;

class Init extends AbstractController
{
    public function executePost($params)
    {
        $runner = wp_umbrella_get_service('BackupRunner');

        if ($runner->hasScheduledBatchInProcess()) {
            return $this->returnResponse(['code' => 'backup_already_process', 'message' => 'A backup is already in process'], 400);
        }

        try {
            wp_umbrella_get_service('BackupInitProcess')->init($params);
        } catch (BackupNotCreated $e) {
            return $this->returnResponse(['code' => 'error', 'message' => $e->getMessage()], 400);
        }

        return $this->returnResponse(['code' => 'success', 'message' => 'Backup scheduled']);
    }
}
