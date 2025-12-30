<?php

var_dump("Hi, test for git.");
die;

require_once __DIR__ . "/vendor/autoload.php";

ini_set('display_errors', true);
ini_set('display_startup_errors', true);
error_reporting(E_ALL);

initSessionIfNotStarted();
$dotenv = Dotenv\Dotenv::createMutable(__DIR__);
$dotenv->load();
src\core\Bootstrap::start();
