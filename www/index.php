<?php
require_once("../Config.php");

$db = Config::getDb();
$view = Config::getView();
$search = new RouteSearch;

if (isset($_GET['from']) && isset($_GET['to'])) {
    if (empty($_GET['from']) && empty($_GET['to'])) {
        // Empty search, not cool
    }
    else {
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
    if ($from != $to) {
        $hits = $search->search($from, $to, $time, false, $limit, $offset);
        $timeUsed = getMicroTime() - $sTime;
        $db->log->insert(array(
            'hits' => $search->count,
            'from' => $from,
            'to' => $to,
            'time' => time(),
            'date' => date("Y-m-d H:i:s"),
            'timeused' => $timeUsed,
            'ua' => $_SERVER['HTTP_USER_AGENT'],
            'ip' => $_SERVER['REMOTE_ADDR'],
        ));
        if (isset($_GET['format']) && $_GET['format'] == "json") {
            echo json_encode($hits);
            exit;
        }

        if (count($hits) == 0)
            $view->assign('error', "noHits");
        $view->assign('routes', $hits);
        $view->assign('timeUsed', $timeUsed);
    }
    else
        $view->assign('easteregg', true);
        

    $view->assign('from', $from);
    $view->assign('to', $to);
    if($time != date('H:i'))
        $view->assign('time', $time);
    }

}

$activeRoutes = $search->getActiveBusNumbers();
sort($activeRoutes);
$view->assign('activeRoutes', $activeRoutes);
$view->assign('departures', $search->getDepartureCount());
$view->assign('import', $db->progress->findOne(array('name' => 'import')));

if (isset($_GET['format']) && $_GET['format'] == "json") {
}
else
    $view->display('search.tpl');
