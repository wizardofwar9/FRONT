<?php
namespace WPUmbrella\Core\Restore\Memento;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Restore\Memento\RestoreMemento;
use WPUmbrella\Core\Restore\Memento\RestoreOriginator;
use WPUmbrella\Core\Models\Memento\Memento;

class RestoreCaretaker
{
    /**
     * @var Memento[]
     */
    protected $mementos = [];

    /**
     * @var Originator
     */
    protected $originator;

    public function setOriginator(RestoreOriginator $originator)
    {
        $this->originator = $originator;
        return $this;
    }

    public function getOriginator()
    {
        return $this->originator;
    }

    public function backup()
    {
        $this->mementos[] = $this->originator->save();
    }

    public function undo()
    {
        if (!count($this->mementos)) {
            return;
        }
        $memento = array_pop($this->mementos);
        try {
            $this->originator->restore($memento);
        } catch (\Exception $e) {
            $this->undo();
        }
    }

    public function showHistory()
    {
        foreach ($this->mementos as $memento) {
            echo $memento->getName() . "\n";
            print_r($memento->getState());
        }
    }
}
