<?php
/*
Plugin Name: WP Umbrella
Description: WP Umbrella is the ultimate all-in-one solution to manage, maintain and monitor one, or multiple WordPress websites.
Author: WP Umbrella
Author URI: https://wp-umbrella.com/
Text Domain: wp-health
Domain Path: /languages/
Requires at least: 5.0
Requires PHP: 7.2
Version: 2.2.4
License: GPLv2
*/

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Kernel;

require_once __DIR__ . '/wp-umbrella-functions.php';
require_once __DIR__ . '/wp-umbrella-request-functions.php';

wp_umbrella_init_defined();

function wp_umbrella_load_plugin()
{
    try {
        require_once __DIR__ . '/vendor/autoload.php';

        Kernel::execute([
            'file' => __FILE__,
            'slug' => 'wp-health',
            'main_file' => 'wp-health',
            'root' => __DIR__,
        ]);
    } catch (\Exception $e) {
    }
}

if (!defined('WP_UMBRELLA_IS_INIT') && wp_umbrella_is_compatible()) {
    wp_umbrella_load_plugin();
}
