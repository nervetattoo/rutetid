<?php
require_once("Config.php");
require_once("View.php");

if (!defined('PREFIX'))
    define('PREFIX', 'main');
$dbName = PREFIX;

// Set up mongo
$mongo = new Mongo;
$db = $mongo->$dbName;

$view = new View;
$view->display('index.tpl');
