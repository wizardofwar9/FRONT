<?php
namespace WPUmbrella\Services;

if (!defined('ABSPATH')) {
    exit;
}

class Register
{
    public function __construct()
    {
        $this->optionService = wp_umbrella_get_service('Option');
    }

    public function getHost()
    {
        if (isset($_SERVER['KINSTA_CACHE_ZONE'])) {
            return 'kinsta';
        }

        return 'other';
    }

    /**
     * @param array $data
     *
     * @return User|null
     */
    public function register($data)
    {
        $hosting = wp_umbrella_get_service('Host')->getHost();

        $response = wp_remote_post(WP_UMBRELLA_API_URL . '/v1/register', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'email' => $data['email'],
                'password' => $data['password'],
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'hosting' => $hosting,
                'newsletters' => $data['newsletters'],
                'with_project' => true,
                'base_url' => site_url(),
                'home_url' => home_url(),
                'project_name' => get_bloginfo('name'),
                'terms' => true,
            ]),
            'timeout' => 50,
        ]);

        if (is_wp_error($response)) {
            return null;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (!$body['success']) {
            return $body;
        }

        $user = $body['result'];

        $options = $this->optionService->getOptions();
        if (isset($user['token']['accessToken'])) {
            $options['api_key'] = $user['token']['accessToken'];
            $options['allowed'] = true;
        }

        if (isset($user['project'])) {
            $options['project_id'] = $user['project']['id'];
        }

        $this->optionService->setOptions($options);

        return $user;
    }
}
