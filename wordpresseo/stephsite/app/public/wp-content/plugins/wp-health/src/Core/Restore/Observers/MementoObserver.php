<?php
namespace WPUmbrella\Core\Restore\Observers;

if (!defined('ABSPATH')) {
    exit;
}
use WPUmbrella\Core\Restore\Memento\RestoreCaretaker;
use WPUmbrella\Core\Restore\Memento\CaretakerHandler;

class MementoObserver implements \SplObserver
{
    public function update(\SplSubject $subject)
    {
        if (($subject instanceof CaretakerHandler) === false) {
            return;
        }

        $data = $subject->getData();

        if (empty($data['originator'])) {
            return;
        }

        $data['caretaker']->setOriginator($data['originator']);
        $data['caretaker']->backup();
    }
}
