<?php
require_once("../Config.php");

$db = Config::getDb();
$view = Config::getView();

if (isset($_GET['from']) && isset($_GET['to'])) {
    $from = $_GET['from'];
    $to = $_GET['to'];
    if (isset($_GET['time']) && !empty($_GET['time']))
        $time = $_GET['time'];
    else
        $time = date("H:i");

    $search = new RouteSearch;

    if (isset($_GET['format']) && $_GET['format'] == "json") {
        $offset = (int)$_GET['offset'];
        $limit = 10;
    }
    else {
        $offset = 0;
        $limit = 5;
    }
    $hits = $search->search($from, $to, $time, false, $limit, $offset);
    if (isset($_GET['format']) && $_GET['format'] == "json") {
        echo json_encode($hits);
        exit;
    }

    if (count($hits) == 0)
        $view->assign('error', "noHits");

    $view->assign('from', $from);
    $view->assign('to', $to);

    $view->assign('routes', $hits);
}
if (isset($_GET['format']) && $_GET['format'] == "json") {
}
else
    $view->display('search.tpl');
