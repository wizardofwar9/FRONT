<?php
namespace WPUmbrella\Core\Restore;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Restore\ChainResponsibility\RestoreProcessHandler;
use WPUmbrella\Core\Restore\Memento\RestoreCaretaker;

class RestoreKernel
{
    /**
     * @var RestoreProcess
     */
    protected $restoreProcess;

    public function setRestoreProcess(RestoreProcessHandler $restoreProcess)
    {
        $this->restoreProcess = $restoreProcess;
        return $this;
    }

    public function setCaretaker(RestoreCaretaker $caretaker)
    {
        $this->caretaker = $caretaker;
        return $this;
    }

    public function execute($originator)
    {
        $this->caretaker->setOriginator($originator)->backup();

        $this->restoreProcess->handle([
            'originator' => $originator,
            'caretaker' => $this->caretaker,
        ]);
    }

    public function hasError()
    {
        $state = $this->caretaker->getOriginator()->getState();
        if (!isset($state['error_code'])) {
            return false;
        }
        return true;
    }

    public function getError()
    {
        $state = $this->caretaker->getOriginator()->getState();
        if (!$this->hasError()) {
            return [];
        }

        return [
            'code' => $state['error_code'],
            'message' => isset($state['error_message']) ? $state['error_message'] : '',
        ];
    }

    public function showHistory()
    {
        $this->caretaker->showHistory();
    }

    public function undo()
    {
        $this->caretaker->undo();
        $originator = $this->caretaker->getOriginator();

        var_dump($originator->getState());
    }
}
