<?php
namespace WPUmbrella\Core\Restore\ChainResponsibility;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Restore\ChainResponsibility\RestoreProcessHandler;
use WPUmbrella\Core\Restore\Memento\CaretakerHandler;
use WPUmbrella\Core\Restore\Memento\RestoreOriginator;

class DiskSpaceHandler extends RestoreProcessHandler implements CaretakerHandler
{
    public function handle($data)
    {
        $originator = $this->getOriginatorByData($data);

        $value = null;
        if (!function_exists('disk_free_space')) {
            $value = 'partial';
        } else {
            $value = @disk_free_space(ABSPATH);
            if (!$value) {
                $value = 'partial';
            }
        }

        $originator->setValueInSate('disk_free_space', $value);

        if ($value !== 'partial' && $value < $originator->getValueInState('zip_size')) {
            $this->setFailHandler($data, [
                'error_code' => 'disk_space',
                'error_message' => 'Insufficient disk space',
            ]);

            return false;
        }

        $data['originator'] = $originator;

        return parent::handle($data);
    }
}
