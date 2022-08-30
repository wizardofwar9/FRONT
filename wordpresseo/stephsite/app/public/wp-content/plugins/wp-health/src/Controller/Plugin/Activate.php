<?php
namespace WPUmbrella\Controller\Plugin;

use WPUmbrella\Core\Models\AbstractController;

if (!defined('ABSPATH')) {
    exit;
}

class Activate extends AbstractController
{
    public function executePost($params)
    {
        $plugin = isset($params['plugin']) ? $params['plugin'] : null;

        if (!$plugin) {
            return $this->returnResponse(['code' => 'missing_parameters', 'message' => 'No plugin'], 400);
        }

        $managePluginActivate = \wp_umbrella_get_service('PluginActivate');

        try {
            $data = $managePluginActivate->activate($plugin);

            if ($data['status'] === 'error') {
                return $this->returnResponse($data['code'], $data['message']);
            }

            return $this->returnResponse($data);
        } catch (\Exception $e) {
            return $this->returnResponse([
                'code' => 'unknown_error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
