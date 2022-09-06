<?php

namespace WPUmbrella\Models;

if (!defined('ABSPATH')) {
    exit;
}

use WP_REST_Response;

/**
 * @abstract
 */
abstract class AbstractApiWordPress
{

	public function getRestResponse($data, $status = 200){
		$restResponse = new WP_REST_Response($data, $status);
		$restResponse->set_headers(['Cache-Control' => 'no-cache']);
		return $restResponse;
	}

	/**
	 * @param WP_REST_Request $request
	 * @return boolean
	 */
    public function authorize($request)
    {
        $response = wp_umbrella_get_service('ApiWordPressPermission')->isAuthorized($request);

        if (!isset($response['authorized'])) {
            header('Cache-Control: no-cache');

            return new \WP_Error($response['code'], $response['message']);
        }

        return $response['authorized'];
    }

	/**
	 * @param WP_REST_Request $request
	 * @return boolean
	 */
    public function authorizeApplication($request)
    {
		$token = $request->get_header('X-Umbrella');
        $response = wp_umbrella_get_service('ApiWordPressPermission')->isAuthorizedToken($token);

        if (!isset($response['authorized'])) {
            header('Cache-Control: no-cache');

            return new \WP_Error($response['code'], $response['message']);
        }

        return $response['authorized'];
    }
}