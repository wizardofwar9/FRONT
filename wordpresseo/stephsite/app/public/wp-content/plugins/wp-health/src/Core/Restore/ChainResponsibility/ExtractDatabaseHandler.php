<?php
namespace WPUmbrella\Core\Restore\ChainResponsibility;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Restore\ChainResponsibility\RestoreProcessHandler;
use WPUmbrella\Core\Restore\Memento\CaretakerHandler;
use Coderatio\SimpleBackup\SimpleBackup;
use ZipArchive;
use WP_Error;

class ExtractDatabaseHandler extends RestoreProcessHandler implements CaretakerHandler
{
    public function handle($data)
    {
        $originator = $this->getOriginatorByData($data);

        $path = $originator->getValueInState('zip_database_path');

        if ($path === null || !file_exists($path)) {
            $this->setFailHandler($data, [
                'error_code' => 'restore_database_handler',
            ]);
            return false;
        }

        if (!function_exists('unzip_file') && \file_exists(ABSPATH . 'wp-admin/includes/file.php')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            WP_Filesystem();
        }

        global $wp_filysystem;

        try {
            $result = unzip_file($path, WP_UMBRELLA_DIR_TEMP_RESTORE);

            if ($result instanceof WP_Error) {
                $this->setFailHandler($data, [
                    'error_code' => 'restore_database_handler',
                    'error_message' => $result->get_error_message()
                ]);
                return false;
            }

            $files = @scandir(sprintf('%s/%s', untrailingslashit(WP_UMBRELLA_DIR_TEMP_RESTORE), 'tables'));
            if (empty($files)) {
                $this->setFailHandler($data, [
                    'error_code' => 'restore_database_handler',
                    'error_message' => 'No tables found in the backup'
                ]);
                return false;
            }

            $tables = [];
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                $tables[] = sprintf('%s/%s/%s', untrailingslashit(WP_UMBRELLA_DIR_TEMP_RESTORE), 'tables', $file);
            }

            $originator->setValueInSate('batch_database', [
                'tables' => $tables,
                'iterator' => 0
            ]);

            $runner = wp_umbrella_get_service('RestoreRunner');

            if ($runner->hasScheduledBatchInProcess()) {
                $this->setFailHandler($data, [
                    'error_code' => 'restore_database_handler',
                    'error_message' => 'A restore is already is in process'
                ]);
            }

            $runner->scheduledRestoreDatabase();
        } catch (\Exception $e) {
            $this->setFailHandler($data, [
                'error_code' => 'restore_database_handler',
                'error_message' => $e->getMessage()
            ]);
            return false;
        }

        $data['originator'] = $originator;

        return parent::handle($data);
    }
}
