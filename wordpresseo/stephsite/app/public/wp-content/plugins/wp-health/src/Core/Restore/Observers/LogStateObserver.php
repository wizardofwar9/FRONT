<?php
namespace WPUmbrella\Core\Restore\Observers;

if (!defined('ABSPATH')) {
    exit;
}

class LogStateObserver implements \SplObserver
{
    public function update(\SplSubject $subject)
    {
        $data = $subject->getData();
        if (empty($data['originator'])) {
            return;
        }

        $originator = $data['originator'];

        $state = $originator->getState();
        $logfile = null;
        if (!isset($state['logfile'])) {
            $logfile = sprintf('%s/%s/%s', WP_UMBRELLA_DIR_TEMP_RESTORE, 'logs', 'umbrella-restore-' . \substr(md5(time()), 0, 6) . '.log');
        } else {
            $logfile = $state['logfile'];
        }

        if (!file_exists($logfile)) {
            file_put_contents($logfile, '');
        }

        $current = file_get_contents($logfile);
        $current .= sprintf("\n[%s] : %s ", date('Y-m-d H:i:s'), $originator->getValueInState('handler'));

        if ($originator->getValueInState('error_code') !== null) {
            $current .= sprintf("\n[%s] Error Code : %s", date('Y-m-d H:i:s'), $originator->getValueInState('error_code'));
        }
        if ($originator->getValueInState('error_message') !== null) {
            $current .= sprintf("\n[%s] Error Message : %s", date('Y-m-d H:i:s'), $originator->getValueInState('error_message'));
        }

        file_put_contents($logfile, $current);
    }
}
