<?php

namespace WPUmbrella\Actions\Admin;

use WPUmbrella\Core\Hooks\ExecuteHooksBackend;

class Logger implements ExecuteHooksBackend
{
    public function hooks()
    {
       \wp_umbrella_get_service('Logger')->deleteLogger();
    }

}
