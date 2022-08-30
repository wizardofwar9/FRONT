<?php
namespace WPUmbrella\Actions\Admin;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Hooks\ExecuteHooksBackend;

class Enqueue implements ExecuteHooksBackend
{
    public function __construct()
    {
        $this->getOwnerService = wp_umbrella_get_service('Owner');
    }

    public function hooks()
    {
        add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
        add_action('admin_enqueue_scripts', [$this, 'adminEnqueueCSS']);
        add_filter('admin_body_class', [$this, 'bodyClass'], 100);
    }

    public function bodyClass($classes)
    {
        if (!isset($_GET['page'])) {
            return $classes;
        }

        $pages = [
            'wp-umbrella-settings' => true,
        ];

        if (isset($pages[$_GET['page']])) {
            $classes .= ' wp-umbrella-styles ';
        }

        return $classes;
    }

    public function adminEnqueueCSS($page)
    {
        if (!in_array($page, ['settings_page_wp-umbrella-settings'], true) && false === strpos($page, 'wp-umbrella')) {
            return;
        }

        wp_enqueue_style('wp-umbrella-tw', WP_UMBRELLA_URL_DIST . '/css/wp-umbrella-tw.css', [], WP_UMBRELLA_VERSION);
    }

    /**
     * @see admin_enqueue_scripts
     *
     * @param string $page
     */
    public function adminEnqueueScripts($page)
    {
        if (!in_array($page, ['settings_page_wp-umbrella-settings'], true) && false === strpos($page, 'wp-umbrella')) {
            return;
        }

        $owner = null;
        if (wp_umbrella_allowed()) {
            $owner = $this->getOwnerService->getOwnerImplicitApiKey();
        }

        $allowTracking = get_option('wp_health_allow_tracking');

        $projectId = wp_umbrella_get_option('project_id');

        $projects = [];
        if (\wp_umbrella_allowed()) {
            $projects = wp_umbrella_get_service('Projects')->getProjects();
        }

        $whiteLabel = wp_umbrella_get_service('WhiteLabel')->getData();

        $data = [
            'API_KEY' => wp_umbrella_get_option('api_key'),
            'API_URL' => WP_UMBRELLA_API_URL,
            'APP_URL' => WP_UMBRELLA_APP_URL,
            'SITE_URL' => site_url(),
            'WP_UMBRELLA_URL_DIST' => WP_UMBRELLA_URL_DIST,
            'ADMIN_AJAX' => admin_url('admin-ajax.php'),
            'USER' => $owner,
            'ALLOW_TRACKING' => $allowTracking && !empty($allowTracking) ? 'true' : 'false',
            'PROJECT_ID' => $projectId,
            'PROJECTS' => $projects,
            'REST_URL' => rest_url(),
            'NONCE' => wp_create_nonce('wp_rest'),
            'WHITE_LABEL' => $whiteLabel
        ];

        wp_register_script('wp-umbrella-app', WP_UMBRELLA_URL_DIST . '/settings.js', ['wp-i18n'], WP_UMBRELLA_VERSION, true);
        wp_enqueue_script('wp-umbrella-app');

        if (function_exists('wp_set_script_translations')) {
            wp_set_script_translations('wp-umbrella-app', 'wp-health', WP_UMBRELLA_LANGUAGES);
        }
        wp_localize_script('wp-umbrella-app', 'WP_UMBRELLA_DATA', $data);
    }
}
