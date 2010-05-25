<?php
require_once("../Config.php");

$db = Config::getDb();
$view = Config::getView();
$search = new RouteSearch;

if (isset($_GET['from']) && isset($_GET['to'])) {
    $from = $_GET['from'];
    $to = $_GET['to'];
    if (isset($_GET['time']) && !empty($_GET['time']))
        $time = $_GET['time'];
    else
        $time = date("H:i");


    if (isset($_GET['format']) && $_GET['format'] == "json") {
        $offset = (int)$_GET['offset'];
        $limit = 10;
    }
    else {
        $offset = 0;
        $limit = 5;
    }
    $sTime = getMicroTime();
    $hits = $search->search($from, $to, $time, false, $limit, $offset);
    $timeUsed = getMicroTime() - $sTime;
    if (isset($_GET['format']) && $_GET['format'] == "json") {
        echo json_encode($hits);
        exit;
    }

    if (count($hits) == 0)
        $view->assign('error', "noHits");

    $view->assign('timeUsed', $timeUsed);
    $view->assign('from', $from);
    $view->assign('to', $to);
    if($time != date('H:i'))
        $view->assign('time', $time);

    $view->assign('routes', $hits);
}

$activeRoutes = $search->getActiveBusNumbers();
sort($activeRoutes);
$view->assign('activeRoutes', $activeRoutes);
$view->assign('departures', $search->getDepartureCount());

if (isset($_GET['format']) && $_GET['format'] == "json") {
}
else
    $view->display('search.tpl');
