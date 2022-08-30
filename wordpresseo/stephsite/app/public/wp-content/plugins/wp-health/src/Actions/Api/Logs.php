<?php

namespace WPUmbrella\Actions\Api;

use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WPUmbrella\Core\Hooks\ExecuteHooks;
use WPUmbrella\Models\AbstractApiWordPress;

if (!defined('ABSPATH')) {
    exit;
}

class Logs extends AbstractApiWordPress implements ExecuteHooks
{
    public function hooks()
    {
        add_action('rest_api_init', [$this, 'register']);
    }

    public function register()
    {
        register_rest_route('wp-umbrella/v1', '/logs',
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get'],
                'permission_callback' => [$this, 'authorize'],
            ]
        );
    }

    /**
     * @since 1.4.0
     *
     * @return void
     */
    public function get(WP_REST_Request $request)
    {
		$data = wp_umbrella_get_service('Logger')->getLogs();
        $restResponse = new WP_REST_Response($data, 200);

        $restResponse->set_headers(['Cache-Control' => 'no-cache']);

        return $restResponse;
    }
}
