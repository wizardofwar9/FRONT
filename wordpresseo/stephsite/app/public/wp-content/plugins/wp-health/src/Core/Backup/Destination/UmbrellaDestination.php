<?php
namespace WPUmbrella\Core\Backup\Destination;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Models\Backup\BackupDestination;
use WPUmbrella\Models\Backup\BackupProcessedData;

class UmbrellaDestination implements BackupDestination
{
    /**
     * @param Namer $namer
     */
    public function __construct($namer = [])
    {
        $this->namer = $namer;
    }

    public function send($extension, BackupProcessedData $model)
    {
        try {
            $backupApi = wp_umbrella_get_service('BackupApi');
            $filename = $this->getName($extension);

            $uploadBySignedUrl = apply_filters('wp_umbrella_backup_upload_by_signed_url', true);
            if ($uploadBySignedUrl) {
                $signedUrl = $backupApi->getSignedUrlForUpload($filename);

                if (empty($signedUrl) || $signedUrl === null) {
                    wp_umbrella_get_service('BackupApi')->postBackup($filename, $model);
                } else {
                    wp_umbrella_get_service('BackupApi')->postBackupBySignedUrl($signedUrl, $filename);
                }
            } else {
                wp_umbrella_get_service('BackupApi')->postBackup($filename, $model);
            }
        } catch (\Exception $e) {
            \wp_umbrella_get_service('Logger')->error($e->getMessage());
            return null;
        }
    }

    /**
     *
     * @param string $extension
     * @return string
     */
    public function getName($extension)
    {
        return sprintf('%s.%s', $this->namer->getName(), $extension);
    }
}
