<?php
namespace WPUmbrella\Services;

if (!defined('ABSPATH')) {
    exit;
}

class Host
{
    public function getHost()
    {
        if (isset($_SERVER['KINSTA_CACHE_ZONE'])) {
            return 'kinsta';
        }

        if ((defined('DB_HOST') && strpos(DB_HOST, '.wpserveur.net') !== false)) {
            return 'wp-server';
        }

        if (class_exists('FlywheelNginxCompat')) {
            return 'flywheel';
        }

        return 'other';
    }
}
