<?php
namespace WPUmbrella\Services\Provider;

if (!defined('ABSPATH')) {
    exit;
}

use Morphism\Morphism;

class Themes
{
    const NAME_SERVICE = 'ThemesProvider';

    public function getThemes()
    {
        require_once ABSPATH . '/wp-admin/includes/theme.php';

        $themes = wp_get_themes();

        $active = get_option('current_theme');
        // Force a theme update check
        wp_update_themes();

        if (function_exists('get_site_transient') && $transient = get_site_transient('update_themes')) {
            $current = $transient;
        } elseif ($transient = get_transient('update_themes')) {
            $current = $transient;
        } else {
            $current = get_option('update_themes');
        }

        foreach ((array) $themes as $key => $theme) {
            $new_version = isset($current->response[$theme->get_stylesheet()]) ? $current->response[$theme->get_stylesheet()]['new_version'] : null;

            $theme_array = [
                'name' => $theme->get('Name'),
                'active' => $active == $theme->get('Name'),
                'template' => $theme->get_template(),
                'stylesheet' => $theme->get_stylesheet(),
                'screenshot' => $theme->get_screenshot(),
                'author_uri' => $theme->get('AuthorURI'),
                'author' => $theme->get('Author'),
                'latest_version' => $new_version ? $new_version : $theme->get('Version'),
                'version' => $theme->get('Version'),
                'theme_uri' => $theme->get('ThemeURI'),
                'require_wp' => $theme->get('RequiresWP'),
                'requires_php' => $theme->get('RequiresPHP'),
            ];

            $themes[$key] = $theme_array;
        }

        return array_values($themes);
    }

    public function getCurrentTheme()
    {
        require_once ABSPATH . '/wp-admin/includes/theme.php';

        $theme = wp_get_theme();

        if ($theme instanceof \WP_Theme) {
            return  [
                'name' => $theme->get('Name'),
                'active' => true,
                'template' => $theme->get_template(),
                'stylesheet' => $theme->get_stylesheet(),
                'screenshot' => $theme->get_screenshot(),
                'author_uri' => $theme->get('AuthorURI'),
                'author' => $theme->get('Author'),
                'version' => $theme->get('Version'),
                'theme_uri' => $theme->get('ThemeURI'),
                'require_wp' => $theme->get('RequiresWP'),
                'requires_php' => $theme->get('RequiresPHP'),
            ];
        }

        return get_option('current_theme');
    }
}
