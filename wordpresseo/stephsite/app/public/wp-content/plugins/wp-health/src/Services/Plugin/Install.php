<?php
namespace WPUmbrella\Services\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

use Automatic_Upgrader_Skin;
use Exception;
use Plugin_Upgrader;
use WP_Error;
use WP_Upgrader_Skin;
use WP_Ajax_Upgrader_Skin;

class Install
{
    const NAME_SERVICE = 'PluginInstall';

    public function install($urlToInstall, $overwrite = true)
    {
        wp_umbrella_get_service('ManagePlugin')->clearUpdates();

        try {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';

            $skin = new WP_Ajax_Upgrader_Skin();
            $upgrader = new Plugin_Upgrader($skin);

            add_filter('upgrader_package_options', function ($options) use ($overwrite) {
                $options['clear_destination'] = $overwrite;
                return $options;
            });

            $result = $upgrader->install($urlToInstall);
            if ($result !== true) {
                return [
                    'status' => 'error',
                    'code' => 'install_fail_may_not_exist',
                    'message' => '',
                    'data' => [
                        'uri' => $urlToInstall
                    ]
                ];
            }

            if (is_wp_error($result)) {
                return [
                    'status' => 'error',
                    'code' => 'install_fail',
                    'message' => is_wp_error($result) ? $result->get_error_message() : '',
                    'data' => $result->plugin_info()
                ];
            }

            return [
                'status' => 'success',
                'code' => 'success',
                'data' => $result
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code' => 'install_fail',
                'data' => $result
            ];
        }
    }
}
