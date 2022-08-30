<?php
namespace WPUmbrella\Core\Restore\ChainResponsibility;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Restore\ChainResponsibility\RestoreProcessHandler;
use WPUmbrella\Core\Restore\Memento\CaretakerHandler;

class CleanUpHandler extends RestoreProcessHandler implements CaretakerHandler
{
    public function handle($data)
    {
        $originator = $this->getOriginatorByData($data);
        try {
            if (!file_exists(WP_UMBRELLA_DIR_TEMP_RESTORE) || !is_dir(WP_UMBRELLA_DIR_TEMP_RESTORE)) {
                return parent::handle($data);
            }

            if (!function_exists('unzip_file') && \file_exists(ABSPATH . 'wp-admin/includes/file.php')) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
                WP_Filesystem();
            }

            global $wp_filesystem;
            $wp_filesystem->rmdir(WP_UMBRELLA_DIR_TEMP_RESTORE, true);
        } catch (\Exception $e) {
            $this->setFailHandler($data, [
                'error_code' => 'clean_up',
                'error_message' => $e->getMessage()
            ]);
            return false;
        }

        $data['originator'] = $originator;

        return parent::handle($data);
    }
}
