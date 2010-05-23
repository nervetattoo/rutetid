<?php

require_once("Config.php");
require_once("View.php");

$db = Config::getDb();
$view = new View;

if (isset($_POST['route']) && isset($_POST['stop']) && isset($_POST['time']))
{
    $db->times->insert(array(
        'route' => $_POST['route'],
        'stop'  => $_POST['stop'],
        'time'  => $_POST['time']
    ));
}

$view->display('insert.tpl');