<?php

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

add_action('wp_umbrella_snapshot_data', 'callback_wp_umbrella_snapshot_data');

function callback_wp_umbrella_snapshot_data()
{
    $plugins = wp_umbrella_get_service('PluginsProvider')->getPlugins();
    $wordpressData = wp_umbrella_get_service('WordPressProvider')->get();
    $themes = wp_umbrella_get_service('ThemesProvider')->getThemes();

    wp_umbrella_get_service('Projects')->snapshotData([
        'plugins' => $plugins,
        'warnings' => $wordpressData,
        'themes' => $themes
    ]);
}
