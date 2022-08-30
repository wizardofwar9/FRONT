<?php
namespace WPUmbrella\Controller;

use WPUmbrella\Core\Models\AbstractController;

if (!defined('ABSPATH')) {
    exit;
}

class Snapshot extends AbstractController
{
    public function executeGet($params)
    {
        $plugins = wp_umbrella_get_service('PluginsProvider')->getPlugins();
        $wordpressData = wp_umbrella_get_service('WordPressProvider')->get();
        $themes = wp_umbrella_get_service('ThemesProvider')->getThemes();

        return $this->returnResponse([
            'plugins' => $plugins,
            'warnings' => $wordpressData,
            'themes' => $themes
        ]);
    }
}
