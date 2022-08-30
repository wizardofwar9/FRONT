<?php
namespace WPUmbrella\Helpers;

if (!defined('ABSPATH')) {
    exit;
}

abstract class Controller
{
    const API = 'api';
    const PHP = 'php';

    const PERMISSION_AUTHORIZE = 'authorize';
    const PERMISSION_AUTHORIZE_APPLICATION = 'authorize_application';
}
