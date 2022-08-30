<?php
namespace WPUmbrella\Actions\Api;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Hooks\ExecuteHooks;
use WPUmbrella\Models\AbstractApiWordPress;

class UmbrellaNonceLogin extends AbstractApiWordPress implements ExecuteHooks
{
    public function hooks()
    {
        add_action('rest_api_init', [$this, 'register']);
    }

    public function register()
    {
        register_rest_route('wp-umbrella/v1', '/umbrella-nonce-login', [
            'methods' => 'POST',
            'callback' => [$this, 'process'],
            'permission_callback' => [$this, 'authorize'],
        ]);
    }

    public function process(\WP_REST_Request $request)
    {
        $hash = md5((new \DateTime())->format('Y-m-d H:m:s'));
        update_option('wp_umbrella_login', $hash, false);

        $restResponse = new \WP_REST_Response([
            'nonce' => $hash
        ]);
        $restResponse->set_headers(['Cache-Control' => 'no-cache']);

        return $restResponse;
    }
}
