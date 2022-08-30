<?php

namespace WPUmbrella\Models;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Abstract class for manage admin notices.
 *
 * @abstract
 */
abstract class AbstractNotice
{
    /**
     * Get template file for admin notice.
     *
     * @static
     *
     * @return string
     */
    public static function get_template_file()
    {
        return '';
    }

    /**
     * Callback for admin_notice hook.
     *
     * @static
     *
     * @return string
     */
    public static function admin_notice()
    {
        $class_call = get_called_class();
        if (!file_exists($class_call::get_template_file())) {
            return;
        }

        include_once $class_call::get_template_file();
    }
}
