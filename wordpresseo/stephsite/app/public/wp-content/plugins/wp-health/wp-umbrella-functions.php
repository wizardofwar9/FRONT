<?php

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Kernel;

function wp_umbrella_init_defined()
{
    if (defined('WP_UMBRELLA_NAME')) {
        return;
    }

    define('WP_UMBRELLA_NAME', 'WP Umbrella');
    define('WP_UMBRELLA_SLUG', 'wp-health');
    define('WP_UMBRELLA_OPTION_GROUP', 'group-wp-health');
    define('WP_UMBRELLA_VERSION', '2.2.4');
    define('WP_UMBRELLA_GOD_HANDLER_VERSION', '1.0.1');
    define('WP_UMBRELLA_PHP_MIN', '7.2');

    define('WP_UMBRELLA_BNAME', plugin_basename(__DIR__ . '/wp-health.php'));
    define('WP_UMBRELLA_DIR', __DIR__);
    define('WP_UMBRELLA_DIR_SCRATCH_BACKUP', WP_UMBRELLA_DIR . '/scratch-backup');
    define('WP_UMBRELLA_DIR_TEMP_RESTORE', WP_UMBRELLA_DIR . '/temp-restore');
    define('WP_UMBRELLA_LANGUAGES', WP_UMBRELLA_DIR . '/languages/');
    define('WP_UMBRELLA_DIR_DIST', WP_UMBRELLA_DIR . '/dist');
    define('WP_UMBRELLA_SITE_URL', 'https://wp-umbrella.com');

    $local = ['wp-health.local', 'umbrella.local'];
    if (isset($_SERVER['HTTP_HOST']) && in_array($_SERVER['HTTP_HOST'], $local, true)) {
        define('WP_UMBRELLA_API_URL', 'http://localhost:3001');
        define('WP_UMBRELLA_APP_URL', 'http://localhost:3000');
    } else {
        define('WP_UMBRELLA_APP_URL', 'https://app.wp-umbrella.com');
        define('WP_UMBRELLA_API_URL', 'https://api.wp-umbrella.com');
    }

    define('WP_UMBRELLA_DIRURL', plugin_dir_url(__FILE__));
    define('WP_UMBRELLA_URL_DIST', WP_UMBRELLA_DIRURL . 'dist');

    define('WP_UMBRELLA_TEMPLATES', WP_UMBRELLA_DIR . '/templates');
    define('WP_UMBRELLA_TEMPLATES_ADMIN', WP_UMBRELLA_TEMPLATES . '/admin');
    define('WP_UMBRELLA_TEMPLATES_ADMIN_NOTICES', WP_UMBRELLA_TEMPLATES_ADMIN . '/notices');
    define('WP_UMBRELLA_TEMPLATES_ADMIN_PAGES', WP_UMBRELLA_TEMPLATES_ADMIN . '/pages');
}

/**
 * Check compatibility this WP Umbrella with WordPress config.
 */
function wp_umbrella_is_compatible()
{
    // Check php version.
    if (version_compare(PHP_VERSION, WP_UMBRELLA_PHP_MIN) < 0) {
        add_action('admin_notices', 'wp_umbrella_php_min_compatibility');

        return false;
    }

    return true;
}

/**
 * Admin notices if wp_umbrella not compatible.
 */
function wp_umbrella_php_min_compatibility()
{
    if (!file_exists(WP_UMBRELLA_TEMPLATES_ADMIN_NOTICES . '/php-min.php')) {
        return;
    }

    include_once WP_UMBRELLA_TEMPLATES_ADMIN_NOTICES . '/php-min.php';
}

/**
 * Get a service.
 *
 * @param string $service
 *
 * @return object
 */
function wp_umbrella_get_service($service)
{
    return Kernel::getContainer()->get($service);
}

/**
 * Get all options.
 *
 * @return array
 */
function wp_umbrella_get_options()
{
    return wp_umbrella_get_service('Option')->getOptions();
}

/**
 * Get option.
 *
 * @param string $key
 *
 * @return any
 */
function wp_umbrella_get_option($key)
{
    return wp_umbrella_get_service('Option')->getOption($key);
}

/**
 * @return bool
 */
function wp_umbrella_allowed()
{
    return wp_umbrella_get_option('allowed');
}

/**
 * Get API KEY.
 *
 * @return string | false
 */
function wp_umbrella_get_api_key()
{
    return wp_umbrella_get_option('api_key');
}

function wp_umbrella_get_project_id()
{
    return wp_umbrella_get_option('project_id');
}
