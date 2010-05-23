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

if (isset($_GET['route']))
{
    $view->assign('route', $db->routes->find(array('id' => $_GET['route'])));
}

$view->display('insert.tpl');