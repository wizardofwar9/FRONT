<?php
namespace WPUmbrella\Services\Manage;

if (!defined('ABSPATH')) {
    exit;
}

use Automatic_Upgrader_Skin;
use Exception;
use Plugin_Upgrader;
use WP_Error;

class ManagePlugin
{
    public function clearUpdates()
    {
        $key = 'update_plugins';
        $response = get_site_transient($key);

        if (!is_object($response)) {
            $response = new \stdClass();
        }
        $response->last_checked = 0;
        set_transient($key, $response);
        if (!function_exists('wp_update_plugins')) {
            include_once ABSPATH . '/wp-includes/update.php';
        }

        wp_update_plugins();
    }

    public function install($pluginUri, $overwrite = true)
    {
        $response = \wp_umbrella_get_service('PluginInstall')->install($pluginUri);
        return $response;
    }

    /**
     *
     * @param string $plugin
     * @return array
     */
    public function update($plugin, $options = [])
    {
        $tryAjax = isset($options['try_ajax']) ? $options['try_ajax'] : true;

        $pluginItem = \wp_umbrella_get_service('PluginsProvider')->getPluginByFile($plugin);

        if (!$pluginItem) {
            \wp_umbrella_get_service('Logger')->error([
                'plugin' => $plugin,
                'error' => 'update_plugin_not_exist'
            ]);
            return [
                'code' => 'plugin_not_exist',
                'message' => sprintf(__('Plugin %s not exist', 'wp-umbrella'), $plugin)
            ];
        }

        $isActive = wp_umbrella_get_service('PluginActivate')->isActive($plugin);

        update_site_option('wp_umbrella_current_update_plugin', $plugin);

        $data = wp_umbrella_get_service('PluginUpdate')->update($plugin);

        if ($data['status'] === 'error' && $tryAjax) {
            $response = wp_umbrella_get_service('PluginUpdate')->tryPremiumRequestUpgrade($plugin, 'plugin');
            if ($response['status'] === 'error') {
                \wp_umbrella_get_service('Logger')->error([
                    'plugin' => $plugin,
                    'error' => $data
                ]);

                return $data;
            }
        }

        delete_site_option('wp_umbrella_current_update_plugin');

        if (!$isActive && $plugin !== 'wp-health/wp-health.php') {
            \wp_umbrella_get_service('PluginDeactivate')->deactivate($plugin);
        } elseif ($isActive || $plugin === 'wp-health/wp-health.php') {
            \wp_umbrella_get_service('PluginActivate')->activate($plugin);
        }

        \wp_umbrella_get_service('Logger')->info([
            'plugin' => $plugin,
            'status' => 'success',
        ]);

        return [
            'status' => 'success',
            'code' => 'success',
            'message' => sprintf('The %s plugin successfully updated', $plugin),
            'data' => isset($data['data']) ?? []
        ];
    }

    /**
     *
     * @param string $pluginFile
     * @param array $options [version, is_active]
     * @return array
     */
    public function rollback($pluginFile, $options = [])
    {
        if (!isset($options['version'])) {
            return [
                'status' => 'error',
                'code' => 'rollback_missing_version',
                'message' => 'Missing version parameter',
                'data' => null
            ];
        }

        $isActive = false;
        if (!isset($options['is_active'])) {
            $isActive = wp_umbrella_get_service('PluginActivate')->isActive($pluginFile);
        } else {
            $isActive = $options['is_active'];
        }

        $plugin = \wp_umbrella_get_service('PluginsProvider')->getPluginByFile($pluginFile);

        if (!$plugin) {
            return [
                'status' => 'error',
                'code' => 'rollback_plugin_not_exist',
                'message' => 'Plugin not exist',
                'data' => null
            ];
        }

        $data = wp_umbrella_get_service('PluginRollback')->rollback([
            'name' => $plugin->name,
            'slug' => $plugin->slug,
            'version' => $options['version'],
            'plugin_file' => $pluginFile
        ]);

        if ($data !== true) {
            return [
                'status' => 'error',
                'code' => 'rollback_version_not_exist',
                'message' => sprintf('Version %s not exist', $options['version']),
                'data' => null
            ];
        }

        if ($isActive) {
            wp_umbrella_get_service('PluginActivate')->activate($pluginFile);
        } else {
            wp_umbrella_get_service('PluginDeactivate')->deactivate($pluginFile);
        }

        return [
            'status' => 'success',
            'code' => 'rollback_success',
            'message' => 'Plugin rollback successful',
            'data' => null
        ];
    }

    public function delete($plugin, $options)
    {
        $pluginItem = \wp_umbrella_get_service('PluginsProvider')->getPluginByFile($plugin);

        if (!$pluginItem) {
            \wp_umbrella_get_service('Logger')->error([
                'plugin' => $plugin,
                'error' => 'delete_plugin_not_exist'
            ]);

            return [
                'code' => 'plugin_not_exist',
                'message' => sprintf(__('Plugin %s not exist', 'wp-umbrella'), $plugin)
            ];
        }

        return wp_umbrella_get_service('PluginDelete')->delete($pluginItem->key, $options);
    }

    /**
     *
     * @param string $action
     * @return boolean|array
     */
    protected function checkSiteErrors($action = '')
    {
        $scrape_key = md5(rand());
        $transient = 'scrape_key_' . $scrape_key;
        $scrape_nonce = strval(rand());
        set_transient($transient, $scrape_nonce, 60); // It shouldn't take more than 60 seconds to make one loopback request.

        $needle_start = "###### wp_scraping_result_start:$scrape_key ######";
        $needle_end = "###### wp_scraping_result_end:$scrape_key ######";
        $scrape_params = [
            'wp_scrape_key' => $scrape_key,
            'wp_scrape_nonce' => $scrape_nonce,
        ];
        if (!empty($action)) {
            $scrape_params['wp_scrape_action'] = $action;
        }

        $headers = [
            'Cache-Control' => 'no-cache',
        ];

        // Make sure PHP process doesn't die before loopback requests complete.
        @set_time_limit(300);

        // Time to wait for loopback requests to finish.
        $timeout = 30;

        // Setup some failure variables
        $loopback_request_failure = [
            'code' => 'loopback_request_failed',
            'message' => __('Unable to communicate back with site to check for fatal errors. You will want to update your assets manually.', 'stops-core-theme-and-plugin-updates'),
        ];
        $json_parse_failure = [
            'code' => 'json_parse_error',
        ];

        // Set and perform loopback
        $url = home_url('/');
        $url = add_query_arg($scrape_params, $url);
        $r = wp_remote_get($url, compact('headers', 'timeout'));
        $body = wp_remote_retrieve_body($r);
        $scrape_result_position = strpos($body, $needle_start);

        // Check for scrape variables
        $result = null;
        if (false === $scrape_result_position) {
            $result = $loopback_request_failure;
        } else {
            $error_output = substr($body, $scrape_result_position + strlen($needle_start));
            $error_output = substr($error_output, 0, strpos($error_output, $needle_end));
            $result = json_decode(trim($error_output), true);
            if (empty($result)) {
                $result = $json_parse_failure;
            }
        }
        delete_transient($transient);

        return $result;
    }
}
