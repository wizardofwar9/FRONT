<?php
namespace WPUmbrella\Actions\Api;

if (!defined('ABSPATH')) {
    exit;
}

use WP_REST_Server;
use WPUmbrella\Core\Hooks\ExecuteHooks;
use WPUmbrella\Models\AbstractApiWordPress;

class Options extends AbstractApiWordPress implements ExecuteHooks
{
    public function hooks()
    {
        add_action('rest_api_init', [$this, 'register']);
    }

    public function register()
    {
        register_rest_route('wp-umbrella/v1', '/options', [
            'methods' => WP_REST_Server::EDITABLE,
            'args' => [
                'project_id' => [
                    'required' => true,
                ],
            ],
            'callback' => [$this, 'process'],
            'permission_callback' => [$this, 'authorizeApplication'],
        ]);
    }

    public function process(\WP_REST_Request $request)
    {
        $projectId = $request->get_param('project_id');

        \wp_umbrella_get_service('Option')->setOptionByKey('project_id', $projectId);

        $restResponse = new \WP_REST_Response(['success' => true]);
        $restResponse->set_headers(['Cache-Control' => 'no-cache']);

        return $restResponse;
    }
}
