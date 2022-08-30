<?php
namespace WPUmbrella\Controller\Plugin;

use WPUmbrella\Core\Models\AbstractController;

if (!defined('ABSPATH')) {
    exit;
}

class Install extends AbstractController
{
    public function executePost($params)
    {
        $pluginUri = isset($params['plugin_uri']) ? $params['plugin_uri'] : null;

        if (!$pluginUri) {
            return $this->returnResponse(['code' => 'missing_parameters', 'message' => 'No plugin'], 400);
        }

        $managePlugin = wp_umbrella_get_service('ManagePlugin');

        try {
            $data = $managePlugin->install($pluginUri);

            if ($data['status'] === 'error') {
                return $this->returnResponse($data, 403);
            }

            return $this->returnResponse($data);
        } catch (\Exception $e) {
            return $this->returnResponse([
                'code' => 'unknown_error',
                'messsage' => $e->getMessage()
            ]);
        }
    }
}
