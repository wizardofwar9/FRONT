<?php
namespace WPUmbrella\Actions\Api;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Kernel;
use WPUmbrella\Core\Hooks\ExecuteHooks;
use WPUmbrella\Helpers\Controller;

class Bootstrap implements ExecuteHooks
{
    public function hooks()
    {
        add_action('rest_api_init', [$this, 'register']);
    }

    public function register()
    {
        $controllers = Kernel::getControllers();

        foreach ($controllers as $key => $item) {
            if (!isset($item['route']) || empty($item['route'])) {
                continue;
            }

            foreach ($item['methods'] as $key => $data) {
                $options = isset($data['options']) ? $data['options'] : [];
                $options['from'] = Controller::API;
                $options['route'] = $item['route'];
                $options['method'] = $data['method'];

                $controller = new $data['class']($options);

                $controller->execute();
            }
        }
    }
}
