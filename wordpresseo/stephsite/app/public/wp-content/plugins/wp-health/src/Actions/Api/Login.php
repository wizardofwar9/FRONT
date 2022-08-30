<?php
namespace WPUmbrella\Actions\Api;

if (!defined('ABSPATH')) {
    exit;
}

use WP_REST_Server;
use WPUmbrella\Core\Hooks\ExecuteHooks;
use WPUmbrella\Models\AbstractApiWordPress;

class Login extends AbstractApiWordPress implements ExecuteHooks
{
    public function hooks()
    {
        add_action('rest_api_init', [$this, 'register']);
    }

    public function register()
    {
        register_rest_route('wp-umbrella/v1', '/login', [
            'methods' => WP_REST_Server::READABLE,
            'args' => [
                'x-project' => [
                    'required' => true,
                ],
                'x-umbrella' => [
                    'required' => true,
                ],
                '_nonce' => [
                    'required' => true
                ],
                'user_id' => [
                    'required' => true
                ]
            ],
            'callback' => [$this, 'process'],
            'permission_callback' => '__return_true'
        ]);
    }

    public function process(\WP_REST_Request $request)
    {
        $params = $request->get_params();

        if (!isset($params['user_id']) || !isset($params['_nonce']) || !isset($params['x-umbrella'])) {
            return $this->getRestResponse([
                'code' => 'missing_parameters'
            ], 400);
        }

        $tokenUmbrella = $params['x-umbrella'];
        $projectIdUmbrella = $params['x-project'];
        $response = wp_umbrella_get_service('ApiWordPressPermission')->isAuthorizedToken($tokenUmbrella);

        if (!isset($response['authorized']) || !$response['authorized']) {
            return $this->getRestResponse(['code' => $response['code'], 'message' => $response['message']]);
        }

        $response = wp_umbrella_get_service('ApiWordPressPermission')->isAuthorizedProjectId($projectIdUmbrella);
        if (!isset($response['authorized']) || !$response['authorized']) {
            return $this->getRestResponse(['code' => $response['code'], 'message' => $response['message']]);
        }

        $transient = get_option('wp_umbrella_login');
        delete_option('wp_umbrella_login');

        if (!$transient || $params['_nonce'] !== $transient) {
            return $this->getRestResponse([
                'code' => 'not_authorized_nonce'
            ], 401);
        }

        $user = get_userdata($params['user_id']);
        if (!$user) {
            $this->getRestResponse([
                'code' => 'user_not_exist'
            ], 401);
            return;
        }

        wp_set_current_user($user->ID, $user->user_login);
        wp_set_auth_cookie($user->ID);

        wp_redirect(admin_url('index.php'));
        exit;
    }
}
