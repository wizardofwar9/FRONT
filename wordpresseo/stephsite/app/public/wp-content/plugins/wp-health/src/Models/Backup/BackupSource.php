<?php

namespace WPUmbrella\Models\Backup;

defined('ABSPATH') or die('Cheatin&#8217; uh?');

interface BackupSource
{
	public function fetch($scratchDir);
}
