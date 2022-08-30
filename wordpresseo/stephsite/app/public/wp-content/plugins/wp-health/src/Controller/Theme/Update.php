<?php
namespace WPUmbrella\Controller\Theme;

use WPUmbrella\Core\Models\AbstractController;

if (!defined('ABSPATH')) {
    exit;
}

class Update extends AbstractController
{
    public function executePost($params)
    {
        $theme = isset($params['theme']) ? $params['theme'] : null;

        if (!$theme) {
            return $this->returnResponse(['code' => 'missing_parameters', 'message' => 'No theme'], 400);
        }

        $manageTheme = \wp_umbrella_get_service('ManageTheme');

        try {
            wp_update_themes();

            $data = $manageTheme->update($theme);

            if (isset($data['status']) && $data['status'] === 'error') {
                return $this->returnResponse($data, 403);
            }

            wp_update_themes();

            return $this->returnResponse($data);
        } catch (\Exception $e) {
            \wp_umbrella_get_service('Logger')->error($e->getMessage());

            return $this->returnResponse([
                'code' => 'unknown_error',
                'messsage' => $e->getMessage()
            ]);
        }
    }
}
