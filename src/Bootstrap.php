<?php
/**
 * User: tuttarealstep
 * Date: 04/11/17
 * Time: 10.23
 */
use HoneyCMS\Application;

require realpath(__DIR__) . '/../vendor/autoload.php';

$App = new Application();
$App->initialize();
$App->run();
