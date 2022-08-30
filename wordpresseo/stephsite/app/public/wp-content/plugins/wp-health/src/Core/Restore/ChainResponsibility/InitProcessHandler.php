<?php
namespace WPUmbrella\Core\Restore\ChainResponsibility;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Restore\ChainResponsibility\RestoreProcessHandler;
use WPUmbrella\Core\Restore\Memento\CaretakerHandler;
use WPUmbrella\Core\Restore\Memento\RestoreOriginator;

class InitProcessHandler extends RestoreProcessHandler implements CaretakerHandler
{
    public function handle($data)
    {
        $originator = $this->getOriginatorByData($data);

        try {
            if (!file_exists(WP_UMBRELLA_DIR_TEMP_RESTORE) && !@mkdir(WP_UMBRELLA_DIR_TEMP_RESTORE)) {
                $this->setFailHandler($data, [
                    'error_code' => 'init_process_mkdir',
                    'error_message' => 'Failed mkdir process',
                ]);

                return false;
            }

            $file = '.htaccess';
            $current = 'deny from all';
            file_put_contents(sprintf('%s/%s', WP_UMBRELLA_DIR_TEMP_RESTORE, $file), $current);
            $state = $originator->getState();

            $file = 'index.php';
            $current = '<?php // Nothing';
            file_put_contents(sprintf('%s/%s', WP_UMBRELLA_DIR_TEMP_RESTORE, $file), $current);
            $state = $originator->getState();

            $file = 'index.html';
            $current = '';
            file_put_contents(sprintf('%s/%s', WP_UMBRELLA_DIR_TEMP_RESTORE, $file), $current);
            $state = $originator->getState();

            @mkdir(sprintf('%s/%s', WP_UMBRELLA_DIR_TEMP_RESTORE, 'logs'));
            $state = $originator->getState();
        } catch (\Exception $e) {
            $this->setFailHandler($data, [
                'error_code' => 'init_process',
                'error_message' => $e->getMessage(),
            ]);

            return false;
        }

        $data['originator'] = $originator;

        return parent::handle($data);
    }
}
