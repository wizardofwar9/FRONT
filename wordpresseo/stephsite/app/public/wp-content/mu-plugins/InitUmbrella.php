<?php

/*
Plugin Name: WP Umbrella
Plugin URI: https://wp-umbrella.com
Description: This is generated by the WP Umbrella plugin to enhance performance. It will be disabled if you disable WP Umbrella.
Author: WP Umbrella
Version: 1.0.0
Author URI: https://wp-umbrella.com
License: GPL2
Network: true
*/

use WPUmbrella\Core\Kernel;

if (!function_exists('untrailingslashit') || !defined('WP_PLUGIN_DIR')) {
    // WordPress is probably not bootstrapped.
    exit;
}

if (file_exists(untrailingslashit(WP_PLUGIN_DIR) . '/wp-health/wp-umbrella-functions.php')) {
    if (in_array('wp-health/wp-health.php', (array) get_option('active_plugins')) ||
        (function_exists('get_site_option') && array_key_exists('wp-health/wp-health.php', (array) get_site_option('active_sitewide_plugins')))) {
        define('WP_UMBRELLA_IS_INIT', true);

        try {
            $basename = untrailingslashit(WP_PLUGIN_DIR) . '/wp-health';
            require_once $basename . '/wp-umbrella-functions.php';
            require_once $basename . '/wp-umbrella-request-functions.php';

            wp_umbrella_init_defined();

            require_once $basename . '/vendor/autoload.php';

            Kernel::execute([
                'file' => $basename . '/wp-health.php',
                'slug' => 'wp-health',
                'main_file' => 'wp-health',
                'root' => $basename,
            ]);
        } catch (\Exception $e) {
        }
    }
}
