<?php
namespace WPUmbrella\Services\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

use Automatic_Upgrader_Skin;
use Exception;
use Plugin_Upgrader;
use WP_Error;

class Delete
{
    const NAME_SERVICE = 'PluginDelete';

    public function delete($plugin, $options = [])
    {
        wp_umbrella_get_service('ManagePlugin')->clearUpdates();

        $skipUninstallHook = isset($options['skip_uninstall_hook']) ? $options['skip_uninstall_hook'] : false;

        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        include_once ABSPATH . 'wp-admin/includes/file.php';

        // Check that it's a valid plugin
        $valid = validate_plugin($plugin);
        if (is_wp_error($valid)) {
            return [
                'status' => 'error',
                'code' => 'plugin_not_valid',
            ];
        }

        if (wp_umbrella_get_service('PluginActivate')->isActive($plugin)) {
            if (is_multisite()) {
                return [
                    'status' => 'error',
                    'code' => 'plugin_active_on_subsite_network',
                ];
            } else {
                return [
                    'status' => 'error',
                    'code' => 'plugin_is_active',
                ];
            }

            return false;
        }

        if (is_multisite() && is_plugin_active_for_network($plugin)) {
            return [
                'status' => 'error',
                'code' => 'plugin_active_on_network',
            ];
        }

        $url = wp_nonce_url('plugins.php?action=delete-selected&verify-delete=1&checked[]=' . $plugin, 'bulk-plugins');
        ob_start();
        $credentials = request_filesystem_credentials($url);
        ob_end_clean();

        if (false === $credentials || !WP_Filesystem($credentials)) {
            global $wp_filesystem;

            $code = 'unable_to_connect_to_filesystem';

            // Pass through the error from WP_Filesystem if one was raised.
            if ($wp_filesystem instanceof WP_Filesystem_Base && is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code()) {
                $code = $wp_filesystem->errors->get_error_code();
                $error = esc_html($wp_filesystem->errors->get_error_message());
            }

            return [
                'status' => 'error',
                'code' => $code,
                'message' => $error
            ];
        }

        // skip uninstall hook if asked to
        if ($skipUninstallHook) {
            // uninstall hook available
            if (is_uninstallable_plugin($plugin)) {
                /**
                 * @see is_uninstallable_plugin()
                 */
                $uninstallable_plugins = (array) get_option('uninstall_plugins');
                if (isset($uninstallable_plugins[$plugin])) {
                    unset($uninstallable_plugins[$plugin]);
                    update_option('uninstall_plugins', $uninstallable_plugins);
                }

                if (file_exists(WP_PLUGIN_DIR . '/' . dirname($plugin) . '/uninstall.php')) {
                    /** @var WP_Filesystem_Base $wp_filesystem */
                    global $wp_filesystem;
                    if ($wp_filesystem instanceof WP_Filesystem_Base) {
                        $wp_filesystem->delete(WP_PLUGIN_DIR . '/' . dirname($plugin) . '/uninstall.php', false, 'f');
                    }
                }
            }

            if (is_uninstallable_plugin($plugin)) {
                return [
                    'status' => 'error',
                    'code' => 'plugin_uninstall_hook_not_found',
                ];
            }
        }

        $result = delete_plugins([$plugin]);

        if (is_wp_error($result)) {
            return [
                'status' => 'error',
                'code' => $result->get_error_code(),
                'message' => $result->get_error_message(),
            ];
        }

        if (true === $result) {
            wp_clean_plugins_cache(false);
            return [
                'status' => 'success',
                'code' => 'success'
            ];
        }

        return [
            'status' => 'error',
            'code' => 'unknown_error',
        ];
    }
}
