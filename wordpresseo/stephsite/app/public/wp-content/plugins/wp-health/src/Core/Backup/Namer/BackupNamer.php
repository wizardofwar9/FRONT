<?php

namespace WPUmbrella\Core\Backup\Namer;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Models\Backup\BackupNamer as BackupNamerModel;

class BackupNamer implements BackupNamerModel
{
    protected $name;



	public function setName($name){
		$this->name = $name;
		return $this;
	}

    public function getName()
    {
        return apply_filters('wp_umbrella_backup_namer',$this->name);
    }
}
