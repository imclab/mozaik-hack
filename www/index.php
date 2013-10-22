<?php
session_start();

require '../src/config.inc.php';

$f3 = require APPROOT.'/vendor/talis/f3/base.php';

function allowCrossDomain()
{
    header('Access-Control-Allow-Origin: *',true);
    header("Access-Control-Allow-Credentials: true",true);
    header('Access-Control-Allow-Methods: OPTIONS, GET, POST, HEAD, PATCH, PUT',true);
    header('Access-Control-Allow-Headers: Accept, Authorization, Origin, X-Requested-With, Content-Type, User-Agent',true);
}

/*
 * FatFreeFramework config...
 */
F3::set('DEBUG','3'); // 0=off, 1-3=increasing verbosity
F3::set('UI',APPROOT.'/views/');
F3::set('AUTOLOAD',APPROOT."/src/");

/*
 * Routing
 */
// marketing site
$f3->route('GET /isbn/@isbn','controllers\images->get');
$f3->route('GET /isbn/@isbn.json','controllers\images->metadata');
$f3->route('GET /isbn/@isbn/colors.json','controllers\images->colors');
$f3->route('GET /isbn/@isbn/mozaik','controllers\images->mozaik');

$f3->route('GET /colors/@color/images.json','controllers\colors->images');
$f3->route('GET /colors/@color/image','controllers\colors->image');
$f3->route('GET /colors/@color/image/mozaik','controllers\colors->mozaik');

F3::run();
