<?php
namespace WPUmbrella\Core\Restore\ChainResponsibility;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Restore\ChainResponsibility\RestoreProcessHandler;
use WPUmbrella\Core\Restore\Memento\CaretakerHandler;
use WPUmbrella\Core\Restore\Memento\RestoreOriginator;

class WordPressDataSqlHandler extends RestoreProcessHandler implements CaretakerHandler
{
    public function handle($data)
    {
        $originator = $this->getOriginatorByData($data);

        $value = [
            'user' => null,
            'password' => null,
            'host' => null,
            'prefix' => null,
        ];
        try {
            $upload_dir = wp_upload_dir();
            global $wpdb;

            $value = [
                'dbname' => DB_NAME,
                'user' => DB_USER,
                'password' => DB_PASSWORD,
                'charset' => DB_CHARSET,
                'host' => DB_HOST,
                'collate' => DB_COLLATE,
                'prefix' => $wpdb->prefix,
            ];
        } catch (\Exception $e) {
            $this->setFailHandler($data, [
                'error_code' => 'wordpress_data_sql',
                'error_message' => $e->getMessage(),
            ]);

            return false;
        }

        $originator->setValueInSate('wordpress_data_sql', $value);

        $data['originator'] = $originator;

        return parent::handle($data);
    }
}
