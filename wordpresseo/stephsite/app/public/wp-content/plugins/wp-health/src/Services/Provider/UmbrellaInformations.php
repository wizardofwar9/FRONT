<?php
namespace WPUmbrella\Services\Provider;

if (!defined('ABSPATH')) {
    exit;
}

class UmbrellaInformations
{
    public function getData()
    {
        if (!function_exists('is_plugin_active') && defined('ABSPATH')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $diskFreeSpace = null;
        if (function_exists('disk_free_space')) {
            $diskFreeSpace = @disk_free_space(ABSPATH);
        }

        try {
            $memoryLimit = @ini_get('memory_limit');
        } catch (\Exception $e) {
            $memoryLimit = null;
        }

        return [
            'site_url' => site_url(),
            'rest_url' => rest_url(),
            'home_url' => home_url(),
            'backdoor_url' => plugins_url(),
            'version' => WP_UMBRELLA_VERSION,
            'god_version' => WP_UMBRELLA_GOD_HANDLER_VERSION,
            'is_plugin_active' => is_plugin_active('wp-health/wp-health.php'),
            'curl_exist' => function_exists('curl_init'),
            'class_exists_zip_archive' => class_exists('ZipArchive'),
            'logs_writable' => \wp_umbrella_get_service('Logger')->isWritableDirectoryLogger(),
            'mu_plugins' => [
                'exist' => file_exists(WPMU_PLUGIN_DIR),
                'writable' => is_writable(dirname(WPMU_PLUGIN_DIR)),
                'exist_handler' => file_exists(WPMU_PLUGIN_DIR . '/_WPHealthHandlerMU.php')
            ],
            'allow_tracking_error' => get_option('wp_health_allow_tracking'),
            'options' => wp_umbrella_get_options(),
            'disk_free_space' => $diskFreeSpace,
            'memory_limit' => $memoryLimit
        ];
    }
}
