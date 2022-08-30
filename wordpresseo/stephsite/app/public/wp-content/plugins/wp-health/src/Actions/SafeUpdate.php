<?php
namespace WPUmbrella\Actions;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Hooks\ExecuteHooks;

class SafeUpdate implements ExecuteHooks
{
    public function hooks()
    {
        if (!defined('WP_SANDBOX_SCRAPING')) {
            return;
        }

        if (
            WP_SANDBOX_SCRAPING &&
            isset($_REQUEST['wp_scrape_key']) &&
            isset($_REQUEST['wp_scrape_nonce']) &&
            isset($_REQUEST['wp_scrape_action']) && 'wpu_activate_plugins' === $_REQUEST['wp_scrape_action']
        ) {
            if (get_transient('scrape_key_' . substr(sanitize_key(wp_unslash($_REQUEST['wp_scrape_key'])), 0, 32)) !== wp_unslash($_REQUEST['wp_scrape_nonce'])) {
                return;
            }

            $currentPluginUpdate = get_site_option('wp_umbrella_current_update_plugin');
            if (!$currentPluginUpdate) {
                return;
            }

            \wp_umbrella_get_service('PluginActivate')->activate($currentPluginUpdate);

            return;
        }
    }
}
