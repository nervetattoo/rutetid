<?php
require_once("Config.php");
require_once("View.php");

if (!isset('PREFIX'))
    define('PREFIX', 'main');
$dbName = PREFIX;

// Set up mongo
$mongo = new Mongo;
$db = $mongo->$dbName;


