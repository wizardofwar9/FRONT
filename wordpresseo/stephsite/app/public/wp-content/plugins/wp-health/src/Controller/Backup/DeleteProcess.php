<?php
namespace WPUmbrella\Controller\Backup;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Models\AbstractController;

class DeleteProcess extends AbstractController
{
    public function executeDelete($params)
    {
        wp_umbrella_get_service('BackupBatchData')->deleteDataOption();

        return $this->returnResponse(['code' => 'success', 'message' => 'Backup unscheduled']);
    }
}
