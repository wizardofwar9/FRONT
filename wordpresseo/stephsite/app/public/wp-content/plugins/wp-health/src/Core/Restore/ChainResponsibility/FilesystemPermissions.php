<?php
namespace WPUmbrella\Core\Restore\ChainResponsibility;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Restore\ChainResponsibility\RestoreProcessHandler;
use WPUmbrella\Core\Restore\Memento\CaretakerHandler;
use WPUmbrella\Core\Restore\Memento\RestoreOriginator;

class FilesystemPermissions extends RestoreProcessHandler implements CaretakerHandler
{
    protected function isWritable($path)
    {
        if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) {
            return win_is_writable($path);
        } else {
            return @is_writable($path);
        }
    }

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

            $value = [
                'abspath' => $this->isWritable(ABSPATH),
                'wp_content_dir' => $this->isWritable(WP_CONTENT_DIR),
                'upload_dir' => $this->isWritable($upload_dir['basedir']),
                'wp_plugin_dir' => $this->isWritable(WP_PLUGIN_DIR),
                'template_directory' => $this->isWritable(get_theme_root(get_template())),
            ];
        } catch (\Exception $e) {
            $this->setFailHandler($data, [
                'error_code' => 'filesystem_permissions',
                'error_message' => $e->getMessage(),
            ]);

            return false;
        }

        $originator->setValueInSate('filesystem_permissions', $value);

        if (!$value['abspath'] || !$value['wp_content_dir'] || !$value['upload_dir'] || !$value['wp_plugin_dir'] || !$value['template_directory']) {
            $this->setFailHandler($data, [
                'error_code' => 'filesystem_permissions',
                'error_message' => '',
            ]);

            return false;
        }

        $data['originator'] = $originator;

        return parent::handle($data);
    }
}
