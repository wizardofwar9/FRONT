<?php
namespace WPUmbrella\Core\Models;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Helpers\Controller;
use WP_REST_Request;
use WP_REST_Response;

trait TraitApiController
{
    public function getCallbackApi()
    {
        $method = $this->getMethod();

        switch ($method) {
            case 'GET':
                return 'getApi';
                break;
            case 'POST':
                return 'postApi';
                break;
            case 'PUT':
                return 'putApi';
                break;
            case 'DELETE':
                return 'deleteApi';
                break;
        }
    }

    protected function executeApi()
    {
        $route = $this->getRoute();
        $method = $this->getMethod();
        $callback = $this->getCallbackApi();

        register_rest_route('wp-umbrella/v1', $route, [
            'methods' => $method,
            'callback' => [$this, $callback],
            'permission_callback' => [$this, 'permissionApi'],
        ]);
    }

    public function getApi(WP_REST_Request $request)
    {
        $params = $request->get_params();
        return $this->executeGet($params);
    }

    public function postApi(WP_REST_Request $request)
    {
        $params = $request->get_params();
        return $this->executePost($params);
    }

    public function putApi(WP_REST_Request $request)
    {
        $params = $request->get_params();
        return $this->executePut($params);
    }

    public function deleteApi(WP_REST_Request $request)
    {
        $params = $request->get_params();
        return $this->executeDelete($params);
    }

    public function permissionApi($request)
    {
        $permission = $this->getPermission();

        if (empty($permission)) {
            return true;
        }

        switch ($permission) {
            case Controller::PERMISSION_AUTHORIZE:
                return wp_umbrella_get_service('RequestPermissions')->authorize($request);
                break;
            case Controller::PERMISSION_AUTHORIZE_APPLICATION:
                return wp_umbrella_get_service('RequestPermissions')->authorizeApplication($request);
                break;
        }
    }

    public function getReponseApi($data, $status = 200)
    {
        $restResponse = new WP_REST_Response($data, $status);
        $restResponse->set_headers(['Cache-Control' => 'no-cache']);
        return $restResponse;
    }
}
