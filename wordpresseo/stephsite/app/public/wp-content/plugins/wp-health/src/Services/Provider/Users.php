<?php
namespace WPUmbrella\Services\Provider;

if (!defined('ABSPATH')) {
    exit;
}

use Morphism\Morphism;

class Users
{
    const NAME_SERVICE = 'UsersProvider';

    public function get($requestArgs = [])
    {
        $argKeys = [
            'include',
            'exclude',
            'search',
            'orderby',
            'order',
            'offset',
            'number',
        ];
        $args = [];
        foreach ($argKeys as $argKey) {
            if (isset($requestArgs[$argKey])) {
                $args[$argKey] = $requestArgs[$argKey];
            }
        }

        $data = (array) array_map(function ($item) {
            $value['id'] = $item->ID;
            $value['data'] = (array) $item->data;
            $value['caps'] = (array) $item->caps;
            $value['roles'] = (array) $item->roles;
            return (array) $value;
        }, get_users($args));

        $schema = [
            'id' => 'id',
            'user_login' => 'data.user_login',
            'user_nicename' => 'data.user_nicename',
            'user_email' => 'data.user_email',
            'user_url' => 'data.user_url',
            'user_registered' => 'data.user_registered',
            'user_activation_key' => 'data.user_activation_key',
            'user_status' => 'data.user_status',
            'display_name' => 'data.display_name',
            'caps' => 'caps',
            'roles' => 'roles',

        ];

        Morphism::setMapper('WPUmbrella\DataTransferObject\User', $schema);

        return Morphism::map('WPUmbrella\DataTransferObject\User', $data);
    }

    public function getUserAdminCanBy($capabilities = ['update_plugins'])
    {
        $usersAdmin = get_users([
            'role' => 'administrator'
        ]);

        $validUser = null;
        foreach ($usersAdmin as $user) {
            if ($validUser !== null) {
                break;
            }

            foreach ($capabilities as $key => $cap) {
                if (!user_can($user, $cap)) {
                    $validUser = null;
                    break;
                }

                $validUser = $user;
            }
        }

        return $validUser;
    }
}
