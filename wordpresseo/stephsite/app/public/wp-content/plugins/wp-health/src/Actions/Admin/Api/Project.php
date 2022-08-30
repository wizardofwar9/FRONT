<?php
namespace WPUmbrella\Actions\Admin\Api;

if (!defined('ABSPATH')) {
    exit;
}
use WPUmbrella\Core\Hooks\ExecuteHooks;

class Project implements ExecuteHooks
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
        register_rest_route('wp-umbrella-admin/v1', '/projects', [
            'methods' => 'POST',
            'callback' => [$this, 'post'],
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

    public function post(\WP_REST_Request $request)
    {
        $params = $request->get_params();
        $token = $request->get_header('x-access-token');
        $name = get_bloginfo('name');
        $hosting = wp_umbrella_get_service('Host')->getHost();

        $data = [
            'base_url' => site_url(),
            'home_url' => home_url(),
            'rest_url' => rest_url(),
            'backdoor_url' => plugins_url(),
            'admin_url' => get_admin_url(),
            'wp_umbrella_url' => WP_UMBRELLA_DIRURL,
            'is_multisite' => is_multisite(),
            'name' => empty($name) ? site_url() : $name,
            'hosting' => $hosting,
        ];

        $response = wp_umbrella_get_service('Projects')->createProjectOnApplication($data, $token);

        if (!is_array($response)) {
            return new \WP_REST_Response(['success' => false, 'code' => 'failed_connect_api']);
        }

        if (!isset($response['result'])) {
            return new \WP_REST_Response($response);
        }

        $newOptions = wp_umbrella_get_service('Option')->getOptions();

        $newOptions['api_key'] = $token;
        $newOptions['allowed'] = true;
        $newOptions['project_id'] = $response['result']['id'];
        wp_umbrella_get_service('Option')->setOptions($newOptions);

        return new \WP_REST_Response($response);
    }
}
