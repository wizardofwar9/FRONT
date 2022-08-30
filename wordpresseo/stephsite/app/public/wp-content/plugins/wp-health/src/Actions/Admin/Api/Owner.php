<?php

namespace WPUmbrella\Actions\Admin\Api;

if (!defined('ABSPATH')) {
    exit;
}
use WPUmbrella\Core\Hooks\ExecuteHooks;

class Owner implements ExecuteHooks
{
    public function hooks()
    {
        add_action('rest_api_init', [$this, 'init']);
    }

    /**
     * @return void
     */
    public function init()
    {
        register_rest_route('wp-umbrella-admin/v1', '/me', [
            'methods'             => 'GET',
            'callback'            => [$this, 'get'],
            'permission_callback' => function ($request) {
                $nonce = $request->get_header('x-wp-nonce');
                if (!wp_verify_nonce($nonce, 'wp_rest')) {
                    return false;
                }

                if (!current_user_can('edit_posts')) {
                    return false;
                }


                return true;
            },
        ]);
    }

    public function get(\WP_REST_Request $request)
    {
        $response = wp_umbrella_get_service('Owner')->getOwnerImplicitApiKey();

        if (!isset($response['result'])) {
            return new \WP_REST_Response($response);
        }

        return new \WP_REST_Response($response);
    }
}
