<?php

use WPUmbrella\Core\Kernel;
use WPUmbrella\Models\Backup\BackupProcessedData;
use WPUmbrella\Helpers\Controller;
use WPUmbrella\Helpers\Directory;
use WPUmbrella\Core\Exceptions\BackupNotCreated;

function wp_umbrella_response($data, $status = 200)
{
    header('Cache-Control: no-cache');
    header('Content-Type: application/json');
    http_response_code($status);
    echo json_encode($data);
    exit;
}

$method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null;

if (!$method) {
    wp_umbrella_response([
        'code' => 'not_authorized'
    ]);
    return;
}

require_once __DIR__ . '/../wp-umbrella-request-functions.php';

function wp_umbrella_prevent_not_active()
{
    if (is_plugin_active('wp-health/wp-health.php')) {
        return;
    }

    wp_umbrella_response([
        'code' => 'not_authorized'
    ]);
    return;
}

$tokenUmbrella = null;
$projectIdUmbrella = null;
$actionUmbrella = null;

$headers = wp_umbrella_get_headers();

if ($method === 'GET' && isset($_GET['x-action']) && $_GET['x-action'] === '/v1/login') {
    if (!isset($_GET['x-action']) || !isset($_GET['x-umbrella'])) {
        wp_umbrella_response([
            'code' => 'not_authorized'
        ]);
        return;
    }
    $tokenUmbrella = $_GET['x-umbrella'];
    $actionUmbrella = $_GET['x-action'];
    $projectIdUmbrella = isset($_GET['x-project']) ? $_GET['x-project'] : null;
} else {
    if (!isset($headers['x-umbrella']) || !isset($headers['x-action'])) {
        if (!isset($_POST['X-Action']) || !isset($_POST['X-Umbrella'])) {
            wp_umbrella_response([
                'code' => 'not_authorized'
            ]);
        }

        $tokenUmbrella = $_POST['X-Umbrella'];
        $actionUmbrella = $_POST['X-Action'];
        if (isset($_POST['X-Project'])) {
            $projectIdUmbrella = $_POST['X-Project'];
        }
    } else {
        $tokenUmbrella = $headers['x-umbrella'];
        $actionUmbrella = $headers['x-action'];
        if (isset($headers['x-project'])) {
            $projectIdUmbrella = $headers['x-project'];
        }
    }
}

if (!defined('ABSPATH')) {
    $fileWpConfig = wp_umbrella_search_wp_config(__DIR__ . '/../../../../', 'wp-config.php');
    require_once $fileWpConfig;
}

if (!function_exists('is_plugin_active') && defined('ABSPATH')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

try {
    require_once __DIR__ . '/../vendor/autoload.php';
    require_once __DIR__ . '/../wp-umbrella-functions.php';

    wp_umbrella_init_defined();

    Kernel::setSettings([
        'file' => __DIR__ . '/../wp-health.php',
        'slug' => 'wp-health',
        'main_file' => 'wp-health',
        'root' => __DIR__ . '/../',
    ]);
    Kernel::buildContainer();
} catch (\Exception $e) {
    wp_umbrella_response([
        'code' => 'error'
    ]);
    return;
}

$controllers = Kernel::getControllers();
$isAlreadyExecuted = false;
foreach ($controllers as $key => $item) {
    if (!isset($item['route']) || empty($item['route'])) {
        continue;
    }

    foreach ($item['methods'] as $keyMethod => $data) {
        $route = $item['route'];
        $methodKernel = $data['method'];

        if ($methodKernel !== $method) {
            continue;
        }

        if ($actionUmbrella !== $key) {
            continue;
        }

        if ($isAlreadyExecuted) {
            continue;
        }

        $isAlreadyExecuted = true;

        $options = isset($data['options']) ? $data['options'] : [];
        $options['from'] = Controller::PHP;
        $options['route'] = $route;
        $options['method'] = $method;

        $controller = new $data['class']($options);
        $controller->execute();
    }
}

if ($isAlreadyExecuted) {
    return;
}

$response = wp_umbrella_get_service('ApiWordPressPermission')->isAuthorizedToken($tokenUmbrella);

if (!isset($response['authorized']) || !$response['authorized']) {
    wp_umbrella_response(['code' => $response['code'], 'message' => $response['message']]);
    return;
}

$whitelistAuthorizeWithouProjectId = ['/v1/options'];

if (!in_array($actionUmbrella, $whitelistAuthorizeWithouProjectId, true)) {
    $response = wp_umbrella_get_service('ApiWordPressPermission')->isAuthorizedProjectId($projectIdUmbrella);

    if (!isset($response['authorized']) || !$response['authorized']) {
        wp_umbrella_response(['code' => $response['code'], 'message' => $response['message']]);
        return;
    }
}

try {
    switch ($actionUmbrella) {
        case '/v1/logs':
            $data = wp_umbrella_get_service('Logger')->getLogs();

            wp_umbrella_response($data);

            break;
        case '/v1/login':
            wp_umbrella_prevent_not_active();

            if ($method !== 'GET') {
                wp_umbrella_response([
                    'code' => 'method_not_exist'
                ], 405);
                return;
            }

            $data = wp_umbrella_get_parameters('GET');

            if (!isset($data['user_id']) || !isset($data['_nonce'])) {
                wp_umbrella_response([
                    'code' => 'missing_parameters'
                ], 400);
                return;
            }

            $transient = get_option('wp_umbrella_login');
            delete_option('wp_umbrella_login');
            if (!$transient || $data['_nonce'] !== $transient) {
                wp_umbrella_response([
                    'code' => 'not_authorized_nonce'
                ], 401);
                return;
            }

            $user = get_userdata($data['user_id']);
            if (!$user) {
                wp_umbrella_response([
                    'code' => 'user_not_exist'
                ], 401);
                return;
            }

            wp_set_current_user($user->ID, $user->user_login);
            wp_set_auth_cookie($user->ID);

            wp_redirect(admin_url('index.php'));
            break;
        case '/v1/umbrella-nonce-login':
            wp_umbrella_prevent_not_active();

            if ($method !== 'POST') {
                wp_umbrella_response([
                    'code' => 'method_not_exist'
                ], 405);
                return;
            }

            $hash = md5((new DateTime())->format('Y-m-d H:m:s'));
            update_option('wp_umbrella_login', $hash, false);

            wp_umbrella_response([
                'nonce' => $hash
            ]);
            break;
        case '/v1/options':
            if ($method !== 'PUT') {
                wp_umbrella_response([
                    'code' => 'method_not_exist'
                ], 405);
                return;
            }

            $data = wp_umbrella_get_parameters();

            if (!isset($data['project_id'])) {
                wp_umbrella_response([
                    'code' => 'missing_parameters'
                ], 400);
                return;
            }

            \wp_umbrella_get_service('Option')->setOptionByKey('project_id', $data['project_id']);

            wp_umbrella_response(['code' => 'success']);

            break;
        case '/v1/languages':
            wp_umbrella_prevent_not_active();

            if ($method !== 'GET') {
                wp_umbrella_response([
                    'code' => 'method_not_exist'
                ], 405);
                return;
            }

            $data = wp_umbrella_get_service('LanguagesProvider')->getData();
            wp_umbrella_response($data);
            break;
        default:
            wp_umbrella_response([
                'code' => 'no_action'
            ], 405);
            return;
    }
} catch (\Exception $e) {
    wp_umbrella_response([
        'code' => 'unknown'
    ]);
    return;
}
