<?php
namespace WPUmbrella\Services\Manage;

if (!defined('ABSPATH')) {
    exit;
}

use Automatic_Upgrader_Skin;
use Exception;
use Theme_Upgrader;
use WP_Error;

class Theme
{
    const NAME_SERVICE = 'ManageTheme';

    public function update($theme)
    {
        $nonce = 'upgrade-theme' . $theme;
        $url = 'update.php?action=update-theme&theme=' . urlencode($theme);

        try {
            include_once ABSPATH . 'wp-admin/includes/admin.php';
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

            $skin = new \Automatic_Upgrader_Skin(compact('nonce', 'url', 'theme'));
            $upgrader = new Theme_Upgrader($skin);

            $response = $upgrader->upgrade($theme);

            if ($response instanceof \WP_Error) {
                return [
                    'status' => 'error',
                    'code' => 'update_theme_error',
                    'message' => $response->get_error_messages(),
                    'data' => $response
                ];
            }

            if (!$response) {
                return [
                    'status' => 'error',
                    'code' => 'update_theme_error',
                    'message' => 'We have not been able to update the theme. Please contact Umbrella support for more information.',
                    'data' => $response
                ];
            }

            $data = [
                'status' => 'success',
                'code' => 'success',
                'message' => sprintf('The %s theme successfully updated', $theme),
                'data' => $response
            ];

            return $data;
        } catch (\Exception $e) {
            $data['message'] = $e->getMessage();

            return [
                'status' => 'error',
                'code' => 'unknown_error',
                'message' => $e->getMessage(),
                'data' => ''
            ];
        }
    }

    public function activate($theme)
    {
        if (!wp_get_theme($theme)->exists()) {
            return [
                'status' => 'error',
                'code' => 'theme_not_installed',
                'message' => 'Theme is not installed.',
                'data' => []
            ];
        }

        $result = switch_theme($theme);

        return [
            'status' => 'success',
            'data' => $result
        ];
    }
}
