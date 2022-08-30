<?php
namespace WPUmbrella\Services\Backup;

if (!defined('ABSPATH')) {
    exit;
}

class BackupScan
{
    public function getData($options = [])
    {
        return [
            'curl_exist' => function_exists('curl_init'),
			'pdo_mysql' => extension_loaded('pdo_mysql'),
            'class_exists_zip_archive' => class_exists('ZipArchive'),
            'memory_limit' => @ini_get('memory_limit')
        ];
    }
}
