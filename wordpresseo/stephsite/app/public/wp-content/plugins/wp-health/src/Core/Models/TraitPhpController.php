<?php
namespace WPUmbrella\Core\Models;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Helpers\Controller;
use WPUmbrella\Core\Models\TraitApiController;
use WPUmbrella\Core\Models\TraitPhpController;

trait TraitPhpController
{
    protected $whitelistWithouProjectId = ['/v1/options'];

    public function getCallbackPhp()
    {
        $method = $this->getMethod();

        switch ($method) {
            case 'GET':
                return 'getPhp';
                break;
            case 'POST':
                return 'postPhp';
                break;
            case 'PUT':
                return 'putPhp';
                break;
            case 'DELETE':
                return 'deletePhp';
                break;
        }
    }

    protected function executePhp()
    {
        $route = $this->getRoute();
        $callback = $this->getCallbackPhp();

        try {
            $this->permissionPhp();
        } catch (\Exception $e) {
        }

        if (!\method_exists($this, $callback)) {
            return;
        }

        $this->$callback();
    }

    protected function getParameters()
    {
        $method = $this->getMethod();

        if ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            if ($data === null) {
                return $_POST;
            }
            return $data;
        } elseif ($method === 'GET') {
            return $_GET;
        }
    }

    public function getPhp()
    {
        $params = $this->getParameters();
        return $this->executeGet($params);
    }

    public function postPhp()
    {
        $params = $this->getParameters();
        return $this->executePost($params);
    }

    public function putPhp()
    {
        $params = $this->getParameters();
        return $this->executePut($params);
    }

    public function deletePhp()
    {
        $params = $this->getParameters();
        return $this->executeDelete($params);
    }

    protected function getHeaders()
    {
        $function = 'getallheaders';
        $headers = [];

        if (function_exists($function)) {
            $headers = $function();
        } else {
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $name = substr($name, 5);
                    $name = str_replace('_', ' ', $name);
                    $name = strtolower($name);
                    $name = ucwords($name);
                    $name = str_replace(' ', '-', $name);

                    $headers[$name] = $value;
                } elseif ($function === 'apache_request_headers') {
                    $headers[$name] = $value;
                }
            }
        }

        return array_change_key_case($headers, CASE_LOWER);
    }

    public function permissionPhp()
    {
        $method = $this->getMethod();

        $token = null;
        $projectId = null;
        $action = null;

        $headers = $this->getHeaders();

        if ($method === 'GET' && isset($_GET['x-action']) && $_GET['x-action'] === '/v1/login') {
            if (!isset($_GET['x-action']) || !isset($_GET['x-umbrella'])) {
                $this->returnResponse([
                    'code' => 'not_authorized'
                ], 403);
                return;
            }
            $token = $_GET['x-umbrella'];
            $action = $_GET['x-action'];
            $projectId = isset($_GET['x-project']) ? $_GET['x-project'] : null;
        } else {
            if (!isset($headers['x-umbrella']) || !isset($headers['x-action'])) {
                if (!isset($_POST['X-Action']) || !isset($_POST['X-Umbrella'])) {
                    $this->returnResponse([
                        'code' => 'not_authorized'
                    ], 403);
                    return;
                }

                $token = $_POST['X-Umbrella'];
                $action = $_POST['X-Action'];
                if (isset($_POST['X-Project'])) {
                    $projectId = $_POST['X-Project'];
                }
            } else {
                $token = $headers['x-umbrella'];
                $action = $headers['x-action'];
                if (isset($headers['x-project'])) {
                    $projectId = $headers['x-project'];
                }
            }
        }

        $response = wp_umbrella_get_service('ApiWordPressPermission')->isAuthorizedToken($token);

        if (!isset($response['authorized']) || !$response['authorized']) {
            $this->returnResponse(['code' => $response['code'], 'message' => $response['message']], 403);
            return;
        }

        if (!in_array($action, $this->whitelistWithouProjectId, true)) {
            $response = wp_umbrella_get_service('ApiWordPressPermission')->isAuthorizedProjectId($projectId);

            if (!isset($response['authorized']) || !$response['authorized']) {
                $this->returnResponse(['code' => $response['code'], 'message' => $response['message']], 403);
                return;
            }
        }

        if (isset($this->options['prevent_active']) && $this->options['prevent_active']) {
            $this->preventNotActive();
        }

        return true;
    }

    protected function preventNotActive()
    {
        if (!function_exists('is_plugin_active') && defined('ABSPATH')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if (is_plugin_active('wp-health/wp-health.php')) {
            return;
        }

        $this->returnResponse([
            'code' => 'not_authorized'
        ], 403);
        return;
    }

    public function getResponsePhp($data, $status = 200)
    {
        header('Cache-Control: no-cache');
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}
