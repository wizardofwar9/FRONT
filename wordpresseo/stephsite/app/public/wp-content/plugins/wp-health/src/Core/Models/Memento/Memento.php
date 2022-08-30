<?php
namespace WPUmbrella\Core\Models\Memento;

if (!defined('ABSPATH')) {
    exit;
}

interface Memento
{
    public function getName();

    public function getDate();
}
