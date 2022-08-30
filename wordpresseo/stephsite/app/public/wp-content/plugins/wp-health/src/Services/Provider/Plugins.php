<?php
namespace WPUmbrella\Services\Provider;

if (!defined('ABSPATH')) {
    exit;
}

use Morphism\Morphism;

class Plugins
{
    const NAME_SERVICE = 'PluginsProvider';

    public function getPlugins()
    {
        if (defined('ABSPATH')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
            require_once ABSPATH . 'wp-admin/includes/update.php';
        }

        wp_umbrella_get_service('ManagePlugin')->clearUpdates();

        $plugins = get_plugins();

        $i = 0;
        $data = [];
        foreach ($plugins as $key => $plugin) {
            $data[$i] = $plugin;
            $data[$i]['key'] = $key;

            $slugExplode = explode('/', $key);
            if (isset($slugExplode[0])) {
                $data[$i]['slug'] = $slugExplode[0];
            }

            $data[$i]['active'] = is_plugin_active($key);
            ++$i;
        }

        $needUpdates = [];
        $needUpdates = get_plugin_updates();

        if (!empty($needUpdates)) {
            foreach ($needUpdates as $plugin) {
                $index = array_search($plugin->Name, array_column($data, 'Name'));
                $data[$index]['update'] = $plugin->update;
            }
        }

        $schema = [
            'name' => 'Name',
            'slug' => 'slug',
            'is_active' => 'active',
            'key' => 'key',
            'version' => 'Version',
            'require_wp_version' => 'RequiresWP',
            'require_php_version' => 'RequiresPHP',
            'title' => 'Title',
            'need_update' => (object) [
                'path' => 'update',
                'fn' => function ($data) {
                    if (!$data || !\is_object($data)) {
                        return false;
                    }

                    return [
                        'id' => \property_exists($data, 'id') ? $data->id : '',
                        'slug' => \property_exists($data, 'slug') ? $data->slug : '',
                        'plugin' => \property_exists($data, 'plugin') ? $data->plugin : '',
                        'new_version' => \property_exists($data, 'new_version') ? $data->new_version : '',
                        'url' => \property_exists($data, 'url') ? $data->url : '',
                        'package' => \property_exists($data, 'package') ? $data->package : '',
                        'tested' => \property_exists($data, 'tested') ? $data->tested : '',
                        'requires_php' => \property_exists($data, 'requires_php') ? $data->requires_php : '',
                        'compatibility' => \property_exists($data, 'compatibility') ? $data->compatibility : '',
                    ];
                },
            ],
        ];

        Morphism::setMapper('WPUmbrella\DataTransferObject\Plugin', $schema);

        return Morphism::map('WPUmbrella\DataTransferObject\Plugin', $data);
    }

    /**
     *
     * @param string $file
     * @return DTOPlugin
     */
    public function getPluginByFile($file)
    {
        $plugins = $this->getPlugins();

        $plugin = null;
        foreach ($plugins as $key => $item) {
            if ($item->key !== $file) {
                continue;
            }

            $plugin = $item;
            break;
        }

        return $plugin;
    }

    public function getPluginTags($slug)
    {
        $url = sprintf('https://api.wordpress.org/plugins/info/1.0/%s.json', $slug);
        $response = wp_remote_get($url);

        if (wp_remote_retrieve_response_code($response) !== 200) {
            return [];
        }

        return json_decode(wp_remote_retrieve_body($response), true);
    }
}
