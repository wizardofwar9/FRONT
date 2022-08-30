<?php
namespace WPUmbrella\Actions\Admin;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Hooks\ExecuteHooksBackend;

class WhiteLabel implements ExecuteHooksBackend
{
    protected $isActiveWhiteLabel = null;

    public function __construct()
    {
        $this->getOwnerService = wp_umbrella_get_service('Owner');
    }

    public function hooks()
    {
        add_filter('plugin_action_links', [$this, 'pluginLinks'], 10, 2);
        add_filter('all_plugins', [$this, 'pluginInfoFilter'], 10, 2);
        add_filter('plugin_row_meta', [$this, 'pluginRowMeta'], 10, 2);
        add_action('admin_enqueue_scripts', [$this, 'adminEnqueueCSS']);
    }

    public function adminEnqueueCSS($page)
    {
        if ($page !== 'plugins.php') {
            return;
        }

        if (isset($_GET['plugin_status']) && $_GET['plugin_status'] !== 'mustuse') {
            return;
        }

        $data = wp_umbrella_get_service('WhiteLabel')->getData();
        if (!$data['hide_plugin']) {
            return;
        }

        echo '<style>
		table.plugins [data-plugin="_WPHealthHandlerMU.php"],
		table.plugins [data-plugin="InitUmbrella.php"] {
			display: none;
		}
	  </style>';
    }

    /**
     * @wp_filter all_plugins
     */
    public function pluginInfoFilter($plugins)
    {
        if (!isset($plugins[WP_UMBRELLA_BNAME])) {
            return $plugins;
        }

        $data = wp_umbrella_get_service('WhiteLabel')->getData();
        if ($data['hide_plugin']) {
            unset($plugins[WP_UMBRELLA_BNAME]);
            return $plugins;
        }

        $plugins[WP_UMBRELLA_BNAME]['Name'] = $data['plugin_name'];
        $plugins[WP_UMBRELLA_BNAME]['Title'] = $data['plugin_name'];
        $plugins[WP_UMBRELLA_BNAME]['Description'] = $data['plugin_description'];
        $plugins[WP_UMBRELLA_BNAME]['AuthorURI'] = $data['plugin_author_url'];
        $plugins[WP_UMBRELLA_BNAME]['Author'] = $data['plugin_author'];
        $plugins[WP_UMBRELLA_BNAME]['AuthorName'] = $data['plugin_author'];
        $plugins[WP_UMBRELLA_BNAME]['PluginURI'] = '';

        return $plugins;
    }

    public function pluginRowMeta($meta, $slug)
    {
        if ($slug !== WP_UMBRELLA_BNAME) {
            return $meta;
        }

        if (isset($meta[2])) {
            unset($meta[2]);
        }

        return $meta;
    }

    public function pluginLinks($links, $file)
    {
        if (WP_UMBRELLA_BNAME !== $file) {
            return $links;
        }

        $settings = sprintf('<a href="%s">%s</a>', admin_url('options-general.php?page=wp-umbrella-settings'), __('Settings'));
        array_unshift($links, $settings);

        return $links;
    }
}
