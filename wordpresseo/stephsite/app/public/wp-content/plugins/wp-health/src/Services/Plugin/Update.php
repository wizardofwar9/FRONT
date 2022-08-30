<?php
namespace WPUmbrella\Services\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

use Automatic_Upgrader_Skin;
use Exception;
use Plugin_Upgrader;
use WP_Error;
use WP_Ajax_Upgrader_Skin;

class Update
{
    const NAME_SERVICE = 'PluginUpdate';

    public function update($plugin)
    {
        wp_umbrella_get_service('ManagePlugin')->clearUpdates();

        $nonce = 'upgrade-plugin_' . $plugin;
        $url = 'update.php?action=upgrade-plugin&plugin=' . rawurlencode($plugin);

        try {
            include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';

            $skin = new WP_Ajax_Upgrader_Skin();
            $upgrader = new Plugin_Upgrader($skin);
            $response = $upgrader->upgrade($plugin);

            if (is_wp_error($skin->result)) {
                if (in_array($skin->result->get_error_code(), ['remove_old_failed', 'mkdir_failed_ziparchive'], true)) {
                    return [
                        'status' => 'error',
                        'code' => 'remove_old_failed_or_mkdir_failed_ziparchive_error',
                        'message' => $skin->get_error_messages(),
                        'data' => $response
                    ];
                } else {
                    return [
                        'status' => 'error',
                        'code' => 'plugin_upgrader_error',
                        'message' => $skin->result->get_error_message(),
                        'data' => $response
                    ];
                }

                return  [
                    'status' => 'error',
                    'code' => 'plugin_upgrader_error',
                    'message' => '',
                    'data' => $response
                ];
            } elseif (in_array($skin->get_errors()->get_error_code(), ['remove_old_failed', 'mkdir_failed_ziparchive'], true)) {
                return [
                    'status' => 'error',
                    'code' => 'remove_old_failed_or_mkdir_failed_ziparchive_error',
                    'message' => $skin->get_error_messages(),
                    'data' => $response
                ];
            } elseif ($skin->get_errors()->get_error_code()) {
                return [
                    'status' => 'error',
                    'code' => 'plugin_upgrader_skin_error',
                    'message' => $skin->get_error_messages(),
                    'data' => $response
                ];
            } elseif (false === $response) {
                global $wp_filesystem;

                $message = '';

                // Pass through the error from WP_Filesystem if one was raised.
                if ($wp_filesystem instanceof \WP_Filesystem_Base && is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code()) {
                    $message = esc_html($wp_filesystem->errors->get_error_message());
                }

                return [
                    'status' => 'error',
                    'code' => 'unable_connect_filesystem',
                    'message' => $message,
                    'data' => $response
                ];
            }

            $data = [
                'status' => 'success',
                'code' => 'success',
                'message' => sprintf('The %s plugin successfully updated', $plugin),
                'data' => $response
            ];

            return $data;
        } catch (\Exception $e) {
            \wp_umbrella_get_service('Logger')->error($e->getMessage());
            $data['message'] = $e->getMessage();

            return [
                'status' => 'error',
                'code' => 'unknown_error',
                'message' => $e->getMessage(),
                'data' => ''
            ];
        }
    }

    public function tryPremiumRequestUpgrade($file, $type)
    {
        // Make post request.
        $response = $this->sendAdminRequest(
            [
                'action' => 'upgrade',
                'from' => 'upgrader',
                'file' => $file,
                'type' => $type,
            ]
        );

        // If request not failed.
        if (!empty($response)) {
            // Get response body.
            $response = json_decode($response, true);

            if (isset($response['success'])) {
                if (empty($response['error'])) {
                    return [
                        'status' => 'success',
                        'code' => 'success',
                        'message' => sprintf('The %s plugin successfully updated', $file),
                        'data' => $response
                    ];
                } else {
                    return [
                        'status' => 'error',
                        'code' => 'error_try_premium_request',
                        'message' => $response['error'],
                        'data' => $response
                    ];
                }
            }
        }

        return [
            'status' => 'error',
            'code' => 'update_plugin_error',
            'message' => '',
            'data' => $response
        ];
    }

    protected function sendAdminRequest($data = [])
    {
        // Create a random hash.
        $hash = md5(wp_generate_password());
        // Create nonce.
        $nonce = wp_create_nonce('wp_umbrella_dashboard_admin_request');

        // Set data in cache.
        set_site_transient(
            $hash,
            $data,
            120 // Expire it after 2 minutes in case we couldn't delete it.
        );

        // Request arguments.
        $args = [
            'blocking' => true,
            'timeout' => 45,
            'sslverify' => false,
            'cookies' => [],
            'body' => [
                'action' => 'wp_umbrella_dashboard_admin_request',
                'nonce' => $nonce,
                'hash' => $hash,
            ],
        ];

        // Set cookies if required.
        if (!empty($_COOKIE)) {
            foreach ($_COOKIE as $name => $value) {
                $args['cookies'][] = new \WP_Http_Cookie(compact('name', 'value'));
            }
        }

        // Make post request.
        $response = wp_remote_post(admin_url('admin-ajax.php'), $args);

        // Delete data after getting response.
        delete_site_transient($hash);

        // If request not failed.
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            // Get response body.
            return wp_remote_retrieve_body($response);
        }

        return false;
    }
}
