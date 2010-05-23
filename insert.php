<?php

require_once("Config.php");
require_once("View.php");

$db = Config::getDb();
$view = new View;
$view->display('insert.tpl');