<?php

date_default_timezone_set('Europe/London');

$appRoot = dirname(__DIR__);
if (!defined('APPROOT'))
{
    define('APPROOT', $appRoot);
}

// Composer autoloader
require APPROOT.'/vendor/autoload.php';

require_once APPROOT . '/src/classes/ImageUtils.class.php';
require_once APPROOT . '/src/classes/jobs/ProcessImage.class.php';
