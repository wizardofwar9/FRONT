<?php
namespace WPUmbrella\Services\Backup;

if (!defined('ABSPATH')) {
    exit;
}

use Symfony\Component\Filesystem\Filesystem;
use WPUmbrella\Core\Backup\Profile;
use WPUmbrella\Models\Backup\BackupProcessedData;

class BackupExecutor
{
    /**
     * @param Profile $profile
     */
    public function backupSources(Profile $profile): array
    {
        $scratchDir = $profile->getScratchDirectory();

        $filesystem = new Filesystem();

        if (!is_dir($scratchDir)) {
            $filesystem->mkdir($scratchDir);
        }

        $response = [];
        foreach ($profile->getSources() as $key => $source) {
            $result = $source->fetch($scratchDir);
            if (!$result) {
                continue;
            }
            $response[$key] = $result;
        }

        return $response;
    }

    /**
     *
     * @param Profile $profile
     * @return BackupExecutor
     */
    public function zip(Profile $profile): BackupExecutor
    {
        $scratchDir = $profile->getScratchDirectory();
        $processor = $profile->getProcessor();

        $processor->process($scratchDir, sprintf('%s/', WP_UMBRELLA_DIR_SCRATCH_BACKUP));

        return $this;
    }

	/**
	 * @param Profile $profile
	 * @param BackupProcessedData $dataModel
	 */
    public function sendToDestinations(Profile $profile, BackupProcessedData $dataModel)
    {
        foreach ($profile->getDestinations() as $destination) {
            $destination->send($profile->getProcessor()->getExtension(), $dataModel);
        }
    }

	/**
	 * @param Profile $profile
	 * @return void
	 */
    public function cleanup(Profile $profile)
    {
        do_action('wp_umbrella_end_backup_cleanup', $profile);

        $filename = $profile->getProcessor()->getFilename(sprintf('%s/', WP_UMBRELLA_DIR_SCRATCH_BACKUP));
        wp_umbrella_get_service('Logger')->info('Filename cleanup: ' . $filename);
        $profile->getProcessor()->cleanup($filename); // Zip
        $pathinfoData = pathinfo($filename);

        if (isset($pathinfoData['filename']) && \is_dir(sprintf('%s/%s', WP_UMBRELLA_DIR_SCRATCH_BACKUP, $pathinfoData['filename']))) {
            $this->destroyDir(sprintf('%s/%s', WP_UMBRELLA_DIR_SCRATCH_BACKUP, $pathinfoData['filename']));
        }
    }

    public function cleanupScratchBackup()
    {
        $this->destroyDir(WP_UMBRELLA_DIR_SCRATCH_BACKUP, [WP_UMBRELLA_DIR_SCRATCH_BACKUP . '/index.php']); // Dir temp
    }

    protected function destroyDir($dir, $excludes = [])
    {
        if (!\file_exists($dir)) {
            return;
        }

        if (!is_dir($dir) || is_link($dir)) {
            if (in_array($dir, $excludes, true)) {
                return true;
            }

            return @unlink($dir);
        }
        foreach (scandir($dir) as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (!$this->destroyDir($dir . DIRECTORY_SEPARATOR . $file, $excludes)) {
                chmod($dir . DIRECTORY_SEPARATOR . $file, 0777);
                if (!$this->destroyDir($dir . DIRECTORY_SEPARATOR . $file, $excludes)) {
                    return false;
                }
            };
        }

        return @rmdir($dir);
    }
}
