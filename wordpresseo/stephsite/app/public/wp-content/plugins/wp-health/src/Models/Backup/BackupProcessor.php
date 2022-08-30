<?php

namespace WPUmbrella\Models\Backup;

defined('ABSPATH') or die('Cheatin&#8217; uh?');

interface BackupProcessor
{
	public function process($scratchDir, $dirDestinationZip = '');
	public function canExecute();
}
