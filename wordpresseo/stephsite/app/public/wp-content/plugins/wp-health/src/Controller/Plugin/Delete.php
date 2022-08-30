<?php
namespace WPUmbrella\Controller\Plugin;

use WPUmbrella\Core\Models\AbstractController;

if (!defined('ABSPATH')) {
    exit;
}

class Delete extends AbstractController
{
    public function executeDelete($params)
    {
        $plugin = isset($params['plugin']) ? $params['plugin'] : null;

        $skipUninstallHook = isset($params['skip_uninstall_hook']) ? $params['skip_uninstall_hook'] : false;
        $managePlugin = \wp_umbrella_get_service('ManagePlugin');

        try {
            $data = $managePlugin->delete($plugin, [
                'skip_uninstall_hook' => $skipUninstallHook
            ]);

            if ($data['status'] === 'error') {
                return $this->returnResponse($data, 500);
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
