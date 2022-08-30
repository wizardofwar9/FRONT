<?php
namespace WPUmbrella\Core;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');
use Coderatio\SimpleBackup\SimpleBackup;
use WPUmbrella\Core\Container\ContainerSkypress;
use WPUmbrella\Core\Container\ManageContainer;
use WPUmbrella\Core\Hooks\ActivationHook;
use WPUmbrella\Core\Hooks\DeactivationHook;
use WPUmbrella\Core\Hooks\ExecuteHooks;
use WPUmbrella\Core\Hooks\ExecuteHooksBackend;
use WPUmbrella\Core\Hooks\ExecuteHooksFrontend;
use WPUmbrella\Helpers\Controller;

abstract class Kernel
{
    protected static $container = null;

    protected static $data = ['slug' => null, 'main_file' => null, 'file' => null, 'root' => ''];

    public static function setSettings($data)
    {
        self::$data = array_merge(self::$data, $data);
    }

    public static function setContainer(ManageContainer $container)
    {
        self::$container = self::getDefaultContainer();
    }

    protected static function getDefaultContainer()
    {
        return new ContainerSkypress();
    }

    public static function getContainer()
    {
        if (null === self::$container) {
            self::$container = self::getDefaultContainer();
        }

        return self::$container;
    }

    public static function handleHooksPlugin()
    {
        require_once WP_UMBRELLA_DIR . '/src/Async/ActionSchedulerSendErrors.php';
        require_once WP_UMBRELLA_DIR . '/src/Async/ActionSchedulerSnapshotData.php';

        switch (current_filter()) {
            case 'plugins_loaded':
                load_plugin_textdomain('wp-health', false, WP_UMBRELLA_LANGUAGES);

                foreach (self::getContainer()->getActions() as $key => $class) {
                    if (!class_exists($class)) {
                        continue;
                    }

                    $class = new $class();

                    switch (true) {
                        case $class instanceof ExecuteHooksBackend:
                            if (is_admin()) {
                                $class->hooks();
                            }
                            break;

                        case $class instanceof ExecuteHooksFrontend:
                            if (!is_admin()) {
                                $class->hooks();
                            }
                            break;

                        case $class instanceof ExecuteHooks:
                            $class->hooks();
                            break;
                    }
                }
                break;
            case 'activate_' . self::$data['slug'] . '/' . self::$data['slug'] . '.php':
                foreach (self::getContainer()->getActions() as $key => $class) {
                    if (!class_exists($class)) {
                        continue;
                    }

                    $class = new $class();
                    if ($class instanceof ActivationHook) {
                        $class->activate();
                    }
                }
                break;
            case 'deactivate_' . self::$data['slug'] . '/' . self::$data['slug'] . '.php':
                foreach (self::getContainer()->getActions() as $key => $class) {
                    if (!class_exists($class)) {
                        continue;
                    }

                    $class = new $class();
                    if ($class instanceof DeactivationHook) {
                        $class->deactivate();
                    }
                }
                break;
        }
    }

    /**
     * @static
     *
     * @param string $path
     * @param string $type
     * @param string $namespace
     *
     * @return void
     */
    public static function buildClasses($path, $type, $namespace = '')
    {
        try {
            $files = array_diff(scandir($path), ['..', '.']);
            foreach ($files as $filename) {
                $pathCheck = $path . '/' . $filename;

                if (is_dir($pathCheck)) {
                    self::buildClasses($pathCheck, $type, $namespace . $filename . '\\');
                    continue;
                }

                $pathinfo = pathinfo($filename);
                if (isset($pathinfo['extension']) && 'php' !== $pathinfo['extension']) {
                    continue;
                }

                $data = '\\WPUmbrella\\' . $namespace . str_replace('.php', '', $filename);

                switch ($type) {
                    case 'services':
                        self::getContainer()->setService($data);
                        break;
                    case 'actions':
                        self::getContainer()->setAction($data);
                        break;
                }
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * Build Container.
     */
    public static function buildContainer()
    {
        self::buildClasses(self::$data['root'] . '/src/Services', 'services', 'Services\\');
        self::buildClasses(self::$data['root'] . '/src/Actions', 'actions', 'Actions\\');
    }

    public static function getControllers()
    {
        return [
            '/v1/plugins' => [
                'route' => '/plugins',
                'methods' => [
                    [
                        'method' => 'GET',
                        'class' => \WPUmbrella\Controller\Plugin\Data::class,
                        'options' => [
                            'prevent_active' => true,
                            'permission' => Controller::PERMISSION_AUTHORIZE,
                        ]
                    ],
                ]
            ],
            '/v1/update-plugin' => [
                'route' => '/update-plugin',
                'methods' => [
                    [
                        'method' => 'POST',
                        'class' => \WPUmbrella\Controller\Plugin\Update::class,
                        'options' => [
                            'prevent_active' => true,
                            'permission' => Controller::PERMISSION_AUTHORIZE,
                        ]
                    ],
                ]
            ],
            '/v1/delete-plugin' => [
                'route' => '/plugin-rollback',
                'methods' => [
                    [
                        'method' => 'DELETE',
                        'class' => \WPUmbrella\Controller\Plugin\Delete::class,
                        'options' => [
                            'prevent_active' => true,
                            'permission' => Controller::PERMISSION_AUTHORIZE,
                        ]
                    ],
                ]
            ],
            '/v1/install-plugin' => [
                'route' => '/install-plugin',
                'methods' => [
                    [
                        'method' => 'POST',
                        'class' => \WPUmbrella\Controller\Plugin\Delete::class,
                        'options' => [
                            'prevent_active' => true,
                            'permission' => Controller::PERMISSION_AUTHORIZE,
                        ]
                    ],
                ]
            ],
            '/v1/activate-plugin' => [
                'route' => '/activate-plugin',
                'methods' => [
                    [
                        'method' => 'POST',
                        'class' => \WPUmbrella\Controller\Plugin\Activate::class,
                        'options' => [
                            'prevent_active' => false,
                            'permission' => Controller::PERMISSION_AUTHORIZE,
                        ]
                    ],
                ]
            ],
            '/v1/deactivate-plugin' => [
                'route' => '/deactivate-plugin',
                'methods' => [
                    [
                        'method' => 'POST',
                        'class' => \WPUmbrella\Controller\Plugin\Deactivate::class,
                        'options' => [
                            'prevent_active' => true,
                            'permission' => Controller::PERMISSION_AUTHORIZE,
                        ]
                    ],
                ]
            ],
            '/v1/rollback-plugin' => [
                'route' => '/rollback-plugin',
                'methods' => [
                    [
                        'method' => 'POST',
                        'class' => \WPUmbrella\Controller\Plugin\Rollback::class,
                        'options' => [
                            'prevent_active' => true,
                            'permission' => Controller::PERMISSION_AUTHORIZE,
                        ]
                    ],
                ]
            ],
            '/v1/themes' => [
                'route' => '/themes',
                'methods' => [
                    [
                        'method' => 'GET',
                        'class' => \WPUmbrella\Controller\Theme\Data::class,
                        'options' => [
                            'prevent_active' => true,
                            'permission' => Controller::PERMISSION_AUTHORIZE,
                        ]
                    ],
                ]
            ],
            '/v1/snapshot' => [
                'route' => '/snapshot',
                'methods' => [
                    [
                        'method' => 'GET',
                        'class' => \WPUmbrella\Controller\Snapshot::class,
                        'options' => [
                            'prevent_active' => true,
                            'permission' => Controller::PERMISSION_AUTHORIZE,
                        ]
                    ],
                ]
            ],
            '/v1/update-theme' => [
                'route' => '/update-theme',
                'methods' => [
                    [
                        'method' => 'POST',
                        'class' => \WPUmbrella\Controller\Theme\Update::class,
                        'options' => [
                            'prevent_active' => true,
                            'permission' => Controller::PERMISSION_AUTHORIZE,
                        ]
                    ],
                ]
            ],
            '/v1/informations' => [
                'route' => '/informations',
                'methods' => [
                    [
                        'method' => 'GET',
                        'class' => \WPUmbrella\Controller\UmbrellaInformations::class,
                        'options' => [
                            'prevent_active' => true,
                            'permission' => Controller::PERMISSION_AUTHORIZE,
                        ]
                    ],
                ]
            ],
            '/v1/directories' => [
                'route' => '/directories',
                'methods' => [
                    [
                        'method' => 'GET',
                        'class' => \WPUmbrella\Controller\Directories::class,
                        'options' => [
                            'prevent_active' => true,
                            'permission' => Controller::PERMISSION_AUTHORIZE,
                        ]
                    ],
                ]
            ],
            '/v1/tables' => [
                'route' => '/tables',
                'methods' => [
                    [
                        'method' => 'GET',
                        'class' => \WPUmbrella\Controller\Tables::class,
                        'options' => [
                            'prevent_active' => true,
                            'permission' => Controller::PERMISSION_AUTHORIZE,
                        ]
                    ],
                ]
            ],
            '/v1/wordpress-data' => [
                'route' => '/wordpress-data',
                'methods' => [
                    [
                        'method' => 'GET',
                        'class' => \WPUmbrella\Controller\WordPressInfo::class,
                        'options' => [
                            'prevent_active' => true,
                            'permission' => Controller::PERMISSION_AUTHORIZE,
                        ]
                    ],
                ]
            ],
            '/v1/users' => [
                'route' => '/users',
                'methods' => [
                    [
                        'method' => 'GET',
                        'class' => \WPUmbrella\Controller\User\Data::class,
                        'options' => [
                            'prevent_active' => true,
                            'permission' => Controller::PERMISSION_AUTHORIZE,
                        ]
                    ],
                ]
            ],
            '/v1/backups' => [
                'route' => '/backups',
                'methods' => [
                    [
                        'method' => 'POST',
                        'class' => \WPUmbrella\Controller\Backup\Init::class,
                        'options' => [
                            'prevent_active' => true,
                            'permission' => Controller::PERMISSION_AUTHORIZE,
                        ]
                    ],
                    [
                        'method' => 'GET',
                        'class' => \WPUmbrella\Controller\Backup\CurrentProcess::class,
                        'options' => [
                            'prevent_active' => true,
                            'permission' => Controller::PERMISSION_AUTHORIZE
                        ]
                    ],
                    [
                        'method' => 'DELETE',
                        'class' => \WPUmbrella\Controller\Backup\DeleteProcess::class,
                        'options' => [
                            'prevent_active' => true,
                            'permission' => Controller::PERMISSION_AUTHORIZE
                        ]
                    ]
                ]
            ],
            '/v1/backups/scan' => [
                'route' => '/backups/scan',
                'methods' => [

                    [
                        'method' => 'GET',
                        'class' => \WPUmbrella\Controller\Backup\Scan::class,
                        'options' => [
                            'prevent_active' => true,
                            'permission' => Controller::PERMISSION_AUTHORIZE
                        ]
                    ],

                ]
            ],

            '/v1/clear-cache' => [
                'route' => '/clear-cache',
                'methods' => [
                    [
                        'method' => 'POST',
                        'class' => \WPUmbrella\Controller\ClearCache::class,
                        'options' => [
                            'prevent_active' => true,
                            'permission' => Controller::PERMISSION_AUTHORIZE,
                        ]
                    ],
                ]
            ],

            '/v1/cores' => [
                'route' => '/cores',
                'methods' => [
                    [
                        'method' => 'GET',
                        'class' => \WPUmbrella\Controller\Core\Data::class,
                        'options' => [
                            'prevent_active' => true,
                            'permission' => Controller::PERMISSION_AUTHORIZE,
                        ]
                    ],
                ]
            ],
            '/v1/update-core' => [
                'route' => '/update-core',
                'methods' => [
                    [
                        'method' => 'POST',
                        'class' => \WPUmbrella\Controller\Core\Update::class,
                        'options' => [
                            'prevent_active' => true,
                            'permission' => Controller::PERMISSION_AUTHORIZE,
                        ]
                    ],
                ]
            ],
            '/v1/restore/scan' => [
                'route' => '/restore/scan',
                'methods' => [
                    [
                        'method' => 'GET',
                        'class' => \WPUmbrella\Controller\Restore\Scan::class,
                        'options' => [
                            'prevent_active' => true,
                            'permission' => Controller::PERMISSION_AUTHORIZE
                        ]
                    ]
                ]
            ],
            '/v1/restore/download' => [
                'route' => '/restore/download',
                'methods' => [
                    [
                        'method' => 'POST',
                        'class' => \WPUmbrella\Controller\Restore\ZipDownload::class,
                        'options' => [
                            'prevent_active' => true,
                            'permission' => Controller::PERMISSION_AUTHORIZE,
                        ]
                    ]
                ]
            ],
            '/v1/restore/files' => [
                'route' => '/restore/files',
                'methods' => [
                    [
                        'method' => 'POST',
                        'class' => \WPUmbrella\Controller\Restore\RestoreFiles::class,
                        'options' => [
                            'prevent_active' => true,
                            'permission' => Controller::PERMISSION_AUTHORIZE,
                        ]
                    ]
                ]
            ],
            '/v1/restore/database' => [
                'route' => '/restore/database',
                'methods' => [
                    [
                        'method' => 'POST',
                        'class' => \WPUmbrella\Controller\Restore\RestoreDatabase::class,
                        'options' => [
                            'prevent_active' => true,
                            'permission' => Controller::PERMISSION_AUTHORIZE,
                        ]
                    ]
                ]
            ],
            '/v1/languages' => [
                'route' => ''
            ],
            '/v1/options' => [
                'route' => ''
            ],
            '/v1/login' => [
                'route' => ''
            ],
            '/v1/umbrella-nonce-login' => [
                'route' => ''
            ],
            '/v1/logs' => [
                'route' => ''
            ],
        ];
    }

    public static function canExecute()
    {
        $headers = \wp_umbrella_get_headers();

        $token = isset($headers['x-umbrella']) ? $headers['x-umbrella'] : null;
        $projectId = isset($headers['x-project']) ? $headers['x-project'] : null;

        $response = wp_umbrella_get_service('ApiWordPressPermission')->isAuthorizedToken($token);

        if (!isset($response['authorized']) || !$response['authorized']) {
            return false;
        }

        $response = wp_umbrella_get_service('ApiWordPressPermission')->isAuthorizedProjectId($projectId);

        if (!isset($response['authorized']) || !$response['authorized']) {
            return false;
        }

        return true;
    }

    public static function trySetupAdmin()
    {
        if (!defined('WP_ADMIN')) {
            define('WP_ADMIN', true);
        }

        if (!defined('WP_NETWORK_ADMIN')) {
            define('WP_NETWORK_ADMIN', false);
        }

        if (!defined('WP_USER_ADMIN')) {
            define('WP_USER_ADMIN', false);
        }

        if (!WP_NETWORK_ADMIN && !WP_USER_ADMIN) {
            define('WP_BLOG_ADMIN', true);
        }

        if (isset($_GET['import']) && !defined('WP_LOAD_IMPORTERS')) {
            define('WP_LOAD_IMPORTERS', true);
        }

        if (!function_exists('wp_set_current_user')) {
            include_once ABSPATH . '/wp-includes/pluggable.php';
            wp_cookie_constants();
        }

        if (!function_exists('get_current_screen')) {
            include_once ABSPATH . '/wp-admin/includes/screen.php';
        }

        $user = wp_umbrella_get_service('UsersProvider')->getUserAdminCanBy();
        if (!$user) {
            return false;
        }

        wp_set_current_user($user->ID, $user->user_login);
        wp_set_auth_cookie($user->ID);
    }

    public static function execute($data)
    {
        if (!class_exists('ActionScheduler')) {
            require_once WP_UMBRELLA_DIR . '/thirds/action-scheduler/action-scheduler.php';
        }

        self::setSettings($data);
        self::buildContainer();

        $headers = \wp_umbrella_get_headers();

        if (isset($headers['x-project']) && isset($headers['x-umbrella'])) {
            if (self::canExecute()) {
                self::trySetupAdmin();
            }
        }

        if (
            (isset($headers['x-action']) && isset($headers['x-umbrella']) && isset($headers['x-project'])) ||
            (isset($_GET['x-action']) && isset($_GET['x-umbrella']) && isset($_GET['x-project']))
        ) {
            $controllers = self::getControllers();
            $action = isset($headers['x-action']) ? $headers['x-action'] : $_GET['x-action'];

            if (array_key_exists($action, $controllers)) {
                require_once WP_UMBRELLA_DIR . '/request/umbrella-application.php';
                return;
            }
        }

        add_action('plugins_loaded', [__CLASS__, 'handleHooksPlugin'], 10);
        register_activation_hook($data['file'], [__CLASS__, 'handleHooksPlugin']);
        register_deactivation_hook($data['file'], [__CLASS__, 'handleHooksPlugin']);
    }
}
