<?php
namespace WPUmbrella\Actions\Admin\Ajax;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Hooks\ExecuteHooksBackend;

class ValidationApiKey implements ExecuteHooksBackend
{
    public function __construct()
    {
        $this->optionService = \wp_umbrella_get_service('Option');
        $this->getOwnerService = wp_umbrella_get_service('Owner');
    }

    public function hooks()
    {
        add_action('wp_ajax_wp_umbrella_valid_api_key', [$this, 'validate']);
        add_action('wp_ajax_wp_umbrella_check_api_key', [$this, 'check']);
    }

    public function validate()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'wp_umbrella_valid_api_key')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        if (!isset($_POST['api_key'])) {
            wp_send_json_error([
                'code' => 'missing_parameters',
            ]);
            exit;
        }

        $apiKey = sanitize_text_field($_POST['api_key']);

        $options['allowed'] = false;
        if (empty($apiKey)) {
            wp_send_json_error([
                'code' => 'missing_parameters',
            ]);
            exit;
        }

        $optionsBdd = $this->optionService->getOptions();
        $newOptions = wp_parse_args($options, $optionsBdd);

        try {
            $data = $this->getOwnerService->validateApiKeyOnApplication($apiKey);

            if (isset($data['code'])) {
                $newOptions['allowed'] = false;
                $newOptions['api_key'] = '';
                $this->optionService->setOptions($newOptions);

                wp_send_json_error([
                    'code' => 'api_key_invalid',
                ]);
                return;
            }

            $owner = $data['result'];

            if ($data && !isset($data['code']) && isset($data['result']['project']['id'])) {
                $newOptions['allowed'] = true;
                $newOptions['api_key'] = $apiKey;
                $newOptions['project_id'] = $data['result']['project']['id'];

                $this->optionService->setOptions($newOptions);

                $projects = wp_umbrella_get_service('Projects')->getProjects();

                wp_send_json_success([
                    'user' => $owner,
                    'api_key' => $apiKey,
                    'project_id' => $newOptions['project_id']
                ]);

                return;
            } elseif (!isset($data['result']['project']['id'])) {
                $newOptions['allowed'] = false;
                $newOptions['api_key'] = $apiKey;

                $this->optionService->setOptions($newOptions);
                $projects = wp_umbrella_get_service('Projects')->getProjects($apiKey);

                wp_send_json_success([
                    'user' => $owner,
                    'api_key' => $apiKey,
                    'projects' => $projects,
                    'code' => 'project_not_exist',
                ]);
            }
        } catch (\Exception $e) {
            \wp_umbrella_get_service('Logger')->error($e->getMessage());
            wp_send_json_error([
                'code' => 'unknown_error',
            ]);
            exit;
        }
    }

    public function check()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'wp_umbrella_check_api_key')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        if (!isset($_POST['api_key'])) {
            wp_send_json_error([
                'code' => 'missing_parameters',
            ]);
            exit;
        }

        $apiKey = sanitize_text_field($_POST['api_key']);

        if (empty($apiKey)) {
            wp_send_json_error([
                'code' => 'missing_parameters',
            ]);
            exit;
        }

        try {
            $data = $this->getOwnerService->validateApiKeyOnApplication($apiKey);

            if (isset($data['code'])) {
                wp_send_json_error([
                    'code' => 'api_key_invalid',
                ]);
                return;
            }

            $owner = $data['result'];

            if ($data && !isset($data['code']) && isset($data['result']['project']['id'])) {
                wp_send_json_success([
                    'code' => 'success',
                    'project_id' => $data['result']['project']['id']
                ]);

                return;
            } elseif (!isset($data['result']['project']['id'])) {
                wp_send_json_success([
                    'code' => 'project_not_exist',
                    'project_id' => null
                ]);
            }
        } catch (\Exception $e) {
            \wp_umbrella_get_service('Logger')->error($e->getMessage());
            wp_send_json_error([
                'code' => 'unknown_error',
            ]);
            exit;
        }
    }
}
