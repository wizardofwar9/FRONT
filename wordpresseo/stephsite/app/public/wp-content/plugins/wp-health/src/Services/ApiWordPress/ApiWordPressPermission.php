<?php

namespace WPUmbrella\Services\ApiWordPress;

if (!defined('ABSPATH')) {
    exit;
}

class ApiWordPressPermission
{
	/**
	 * @param WP_REST_Request $request
	 * @return array
	 */
    public function isAuthorized($request)
    {
        $token = $request->get_header('X-Umbrella');
		$projectId = $request->get_header('X-Project');
        $data = $this->isAuthorizedToken($token);

		if(!$data['authorized']){
			return $data;
		}

		return $this->isAuthorizedProjectId($projectId);
    }

	/**
	 *
	 * @param int $projectId
	 * @return array
	 */
	public function isAuthorizedProjectId($projectId){
		$projectIdOption = wp_umbrella_get_project_id();
		if(!$projectIdOption){
			return ['authorized' => false, 'code' => 'no_project_id', 'message' => 'Project ID not found'];
		}

		if((int) $projectIdOption !== (int) $projectId){
			return ['authorized' => false, 'code' => 'project_id_not_match', 'message' => 'Project ID not match'];
		}

		return ['authorized' => true];
	}

	/**
	 *
	 * @param string $token
	 * @return array
	 */
    public function isAuthorizedToken($token)
    {
        $allow = apply_filters('wp_umbrella_allow_access_api', true);
        if (!$allow) {
            return ['authorized' => false, 'code' => 'not_allowed', 'message' => 'Not authorize access data'];
        }

        if (!$token || empty($token)) {
            return ['authorized' => false, 'code' => 'api_key_empty', 'message' => 'API Key is empty'];
        }

        $apiKeySave = wp_umbrella_get_option('api_key');

        if ($token !== $apiKeySave) {
            return ['authorized' => false, 'code' => 'not_authorize', 'message' => 'API Key not authorize'];
        }

        return ['authorized' => true];
    }
}
