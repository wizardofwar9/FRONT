<?php
namespace WPUmbrella\Core\Restore\ChainResponsibility;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Restore\Memento\RestoreCaretaker;
use WPUmbrella\Core\Restore\Memento\CaretakerHandler;
use WPUmbrella\Core\Models\Observers\Subject;
use WPUmbrella\Core\Restore\Memento\RestoreOriginator;
use WPUmbrella\Core\Restore\Memento\RestoreMemento;

abstract class RestoreProcessHandler extends Subject
{
    protected $data;

    /**
     * @var RestoreProcessHandler
     */
    protected $next;

    public function linkWith(RestoreProcessHandler $next)
    {
        $this->next = $next;

        return $next;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function getOriginatorByData($data)
    {
        if (!isset($data['originator'])) {
            throw new \Exception('No Originator');
        }

        return $data['originator'];
    }

    public function getCaretakerByData($data)
    {
        if (!isset($data['caretaker'])) {
            throw new \Exception('No caretaker');
        }

        return $data['caretaker'];
    }

    public function setFailHandler($data, $error)
    {
        $originator = $this->getOriginatorByData($data);

        if (isset($error['error_code'])) {
            $originator->setValueInSate('error_code', $error['error_code']);
        }
        if (isset($error['error_message'])) {
            $originator->setValueInSate('error_message', $error['error_message']);
        }

        $data['originator'] = $originator;

        // Need for access on notify subject
        $this->setData($data);

        $this->notify();
    }

    public function setCurrentHandler($data)
    {
        $originator = $this->getOriginatorByData($data);
        $originator->setValueInSate('handler', get_called_class());
        $data['originator'] = $originator;

        // Need for access on notify subject
        $this->setData($data);
    }

    public function handle($data)
    {
        $this->setCurrentHandler($data);

        $this->notify();

        if (!$this->next) {
            return true;
        }

        return $this->next->handle($data);
    }
}
