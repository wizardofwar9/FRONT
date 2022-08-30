<?php
namespace WPUmbrella\Models\Backup;

defined('ABSPATH') or die('Cheatin&#8217; uh?');

use WPUmbrella\Models\Backup\BackupProcessedData;

interface BackupDestination
{
    public function send($extension, BackupProcessedData $model);
}
