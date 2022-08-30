<?php
namespace WPUmbrella\Core\Restore\ChainResponsibility;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Restore\ChainResponsibility\RestoreProcessHandler;
use WPUmbrella\Core\Restore\Memento\CaretakerHandler;
use ZipArchive;
use WP_Error;

class RestoreFilesHandler extends RestoreProcessHandler implements CaretakerHandler
{
    public function handle($data)
    {
        $originator = $this->getOriginatorByData($data);

        $path = $originator->getValueInState('zip_files_path');

        if ($path === null || !file_exists($path)) {
            $this->setFailHandler($data, [
                'error_code' => 'restore_files_handler',
            ]);
            return false;
        }

        if (!function_exists('unzip_file') && \file_exists(ABSPATH . 'wp-admin/includes/file.php')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            WP_Filesystem();
        }

        global $wp_filysystem;

        try {
            $result = unzip_file($path, ABSPATH);

            if ($result instanceof WP_Error) {
                $this->setFailHandler($data, [
                    'error_code' => 'restore_files_handler',
                    'error_message' => $result->get_error_message()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            $this->setFailHandler($data, [
                'error_code' => 'restore_files_handler',
                'error_message' => $e->getMessage()
            ]);
            return false;
        }

        $dirTable = sprintf('%s/%s', untrailingslashit(ABSPATH), 'tables');
        // Remove old build backup
        if (\file_exists($dirTable) && is_dir($dirTable)) {
            @rmdir($dirTable);
        }

        $data['originator'] = $originator;

        return parent::handle($data);
    }
}
