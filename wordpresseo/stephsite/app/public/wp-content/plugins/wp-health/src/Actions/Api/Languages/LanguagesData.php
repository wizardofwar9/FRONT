<?php
namespace WPUmbrella\Actions\Api\Languages;

use Exception;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WPUmbrella\Core\Hooks\ExecuteHooks;
use WPUmbrella\Models\AbstractApiWordPress;

if (!defined('ABSPATH')) {
    exit;
}

class LanguagesData extends AbstractApiWordPress implements ExecuteHooks
{
    public function hooks()
    {
        add_action('rest_api_init', [$this, 'register']);
    }

    public function register()
    {
        register_rest_route(
            'wp-umbrella/v1',
            '/languages',
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'get'],
                'permission_callback' => [$this, 'authorize'],
            ]
        );
    }

    /**
     * @return WP_Error|WP_REST_Response
     */
    public function get(WP_REST_Request $request)
    {
        try {
            $data = wp_umbrella_get_service('LanguagesProvider')->getData();

            return $this->getRestResponse($data);
        } catch (\Exception $e) {
            \wp_umbrella_get_service('Logger')->error($e->getMessage());
            $data['message'] = $e->getMessage();

            $restResponse = new WP_Error('unknown_error', $e->getMessage());
            $restResponse->set_headers(['Cache-Control' => 'no-cache']);

            return $restResponse;
        }
    }
}
