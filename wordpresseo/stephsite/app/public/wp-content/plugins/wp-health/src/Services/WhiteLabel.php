<?php
namespace WPUmbrella\Services;

if (!defined('ABSPATH')) {
    exit;
}

class WhiteLabel
{
    protected $data = null;

    public function hideMenu()
    {
        $data = $this->getData();
        return apply_filters('wp_umbrella_white_label_hide_menu', $data['hide_plugin']);
    }

    public function getData()
    {
        $key = 'wp_umbrella_white_label_data_cache';
        $cacheData = get_transient($key);

        if ($cacheData && apply_filters($key . '_active', true)) {
            return $cacheData;
        }

        $default = [
            'hide_plugin' => false,
            'plugin_name' => __('WP Umbrella', 'wp-health'),
            'plugin_description' => __('WP Umbrella is the ultimate all-in-one solution to manage, maintain and monitor one, or multiple WordPress websites.', 'wp-health'),
            'plugin_author' => 'WP Umbrella',
            'plugin_author_url' => 'https://wp-umbrella.com/',
            'catchphrase' => __('Helping Agencies and Freelancers with their WordPress Maintenance Business 🚀', 'wp-health'),
            'view_company_details' => false,
            'email_support' => ''
        ];

        if ($this->data === null) {
            $owner = wp_umbrella_get_service('Owner')->getOwnerImplicitApiKey();

            if (!isset($owner['white_label'])) {
                $owner['white_label'] = $default;
            }

            $this->data = $owner['white_label'];
        }

        set_transient($key, $this->data, apply_filters($key . '_duration', 120));

        return apply_filters('wp_umbrella_white_label_data', $this->data);
    }
}
