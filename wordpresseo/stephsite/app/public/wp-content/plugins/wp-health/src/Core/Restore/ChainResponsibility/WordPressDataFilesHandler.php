<?php
namespace WPUmbrella\Core\Restore\ChainResponsibility;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Restore\ChainResponsibility\RestoreProcessHandler;
use WPUmbrella\Core\Restore\Memento\CaretakerHandler;
use WPUmbrella\Core\Restore\Memento\RestoreOriginator;

class WordPressDataFilesHandler extends RestoreProcessHandler implements CaretakerHandler
{
    public function handle($data)
    {
        $originator = $this->getOriginatorByData($data);

        $value = [
            'abspath' => null,
            'wp_content_dir' => null,
            'upload_dir' => null,
            'wp_plugin_dir' => null,
            'template_directory' => null,
        ];
        try {
            $upload_dir = wp_upload_dir();
            global $wpdb;

            $value = [
                'abspath' => ABSPATH,
                'wp_content_dir' => WP_CONTENT_DIR,
                'upload_dir' => $upload_dir['basedir'],
                'wp_plugin_dir' => WP_PLUGIN_DIR,
                'template_directory' => get_theme_root(get_template()),

            ];
        } catch (\Exception $e) {
            $this->setFailHandler($data, [
                'error_code' => 'wordpress_data_files',
                'error_message' => $e->getMessage(),
            ]);

            return false;
        }

        $originator->setValueInSate('wordpress_data_files', $value);

        $data['originator'] = $originator;

        return parent::handle($data);
    }
}
