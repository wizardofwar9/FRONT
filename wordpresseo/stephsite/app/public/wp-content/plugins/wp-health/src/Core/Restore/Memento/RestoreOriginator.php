<?php
namespace WPUmbrella\Core\Restore\Memento;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Restore\Memento\RestoreMemento;
use WPUmbrella\Core\Models\Memento\Memento;

/**
 * The Originator holds some important state that may change over time. It also
 * defines a method for saving the state inside a memento and another method for
 * restoring the state from it.
 */
class RestoreOriginator
{
    protected $state = [
        'handler' => null,
        'disk_free_space' => 0,
    ];

    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    public function setValueInSate($key, $value)
    {
        $this->state[$key] = $value;
        return $this;
    }

    public function getValueInState($key)
    {
        if (isset($this->state[$key])) {
            return $this->state[$key];
        }
        return null;
    }

    public function getState()
    {
        return $this->state;
    }

    public function save()
    {
        return new RestoreMemento($this->state);
    }

    public function restore(Memento $memento)
    {
        $this->setState($memento->getState());
    }
}
