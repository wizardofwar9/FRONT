<?php

namespace WPUmbrella\Models;

if (! defined('ABSPATH')) {
    exit;
}

class RollbackSkin extends \Plugin_Upgrader_Skin{
	public function header(){
		return;
	}

	public function footer(){
		return;
	}


	public function before() {
		return;
	}

	public function after() {
		return;
	}

	public function feedback($string, ...$args) {
		return;
	}

}
