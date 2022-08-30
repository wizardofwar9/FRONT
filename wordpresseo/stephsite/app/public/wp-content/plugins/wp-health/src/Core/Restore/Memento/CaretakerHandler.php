<?php
namespace WPUmbrella\Core\Restore\Memento;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Restore\Memento\RestoreCaretaker;

interface CaretakerHandler
{
    public function getData();

    public function setData($data);
}
