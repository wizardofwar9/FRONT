<?php
namespace WPUmbrella\Core\Restore\ChainResponsibility;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Restore\ChainResponsibility\RestoreProcessHandler;
use WPUmbrella\Core\Restore\Memento\CaretakerHandler;
use WPUmbrella\Core\Restore\Memento\RestoreOriginator;

class MemoryLimitHandler extends RestoreProcessHandler implements CaretakerHandler
{
    public function handle($data)
    {
        $originator = $this->getOriginatorByData($data);

        $value = null;

        try {
            $value = @ini_get('memory_limit');
            $value = trim($value);
            $last = strtolower($value[strlen($value) - 1]);
            $value = (int) substr($value, 0, -1);
        } catch (\Exception $e) {
            $value = 256;
        }

        if (!$value || $value === 0) {
            $value = 256;
        }

        if ($last == 'g') {
            $value *= 1024;
        }
        if ($last == 'm') {
            $value *= 1024 * 1024;
        }
        if ($last == 'k') {
            $value *= 1024 * 1024 * 1024;
        }

        $originator->setValueInSate('memory_limit_bytes', $value);

        $data['originator'] = $originator;

        return parent::handle($data);
    }
}
