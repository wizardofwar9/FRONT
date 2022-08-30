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

class RestoreDatabaseHandler extends RestoreProcessHandler implements CaretakerHandler
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
            $databaseData = $originator->getValueInState('wordpress_data_sql');
            $batch = $originator->getValueInState('batch_database');

            if (!isset($batch['tables']) || empty($batach['tables'])) {
                $this->setFailHandler($data, [
                    'error_code' => 'restore_database_handler',
                    'error_message' => 'No batch data'
                ]);
                return false;
            }

            $tables = $batch['tables'];
            $iterator = $batch['iterator'];

            if (!isset($tables[$iterator])) {
                $this->setFailHandler($data, [
                    'error_code' => 'restore_database_handler',
                    'error_message' => 'No tables found for iterator'
                ]);
                return false;
            }

            $path = sprintf('%s/%s/%s', untrailingslashit(WP_UMBRELLA_DIR_TEMP_RESTORE), 'tables', $tables[$iterator]);
            if (!file_exists($path)) {
                $this->setFailHandler($data, [
                    'error_code' => 'restore_database_handler',
                    'error_message' => 'Table file not found'
                ]);
                return false;
            }

            $simpleBackup = SimpleBackup::setDatabase([
                $databaseData['dbname'],
                $databaseData['user'],
                $databaseData['password'],
                $databaseData['host'],
            ])->importFrom($path);
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
