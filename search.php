<?php
require_once("Config.php");
require_once("View.php");
require_once("libs/RouteSearch.php");

$db = Config::getDb();
$view = new View;

if (isset($_GET['from']) && isset($_GET['to'])) {
    $from = $_GET['from'];
    $to = $_GET['to'];
    $time = $_GET['time'];

    $search = new RouteSearch;
    $hits = $search->search($from, $to, false, false, 15);

    $view->assign('routes', $hits);
    $view->assign('from', $from);
    $view->assign('to', $to);
}

$view->display('search.tpl');
