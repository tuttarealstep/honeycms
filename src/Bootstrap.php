<?php
/**
 * User: tuttarealstep
 * Date: 04/11/17
 * Time: 10.23
 */
use HoneyCMS\Application;

require realpath(__DIR__) . '/../vendor/autoload.php';

define('ROOT_PATH', __DIR__);
define('THEMES_PATH', ROOT_PATH . '/Storage/Themes');

$App = new Application();
$App->initialize();
$App->run();
