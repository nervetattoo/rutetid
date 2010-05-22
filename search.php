<?php
require_once("Config.php");
require_once("View.php");

$db = Config::getDb();
$view = new View;

if (isset($_GET['from']) && isset($_GET['to'])) {
    $from = $_GET['from'];
    $to = $_GET['to'];
    $time = str_replace(":", "", $_GET['time']);
    $routes = $db->routes->find(array(
        'stops.name' => array(
            '$all' => array(
                $from,
                $to
            )
        )
    ));
    $hits = array();
    while ($route = $routes->getNext()) {
        foreach ($route['stops'] as $stop) {
            $routeId = $route['id'];
            if ($stop['name'] == $from) {
                $hasTime = false;
                foreach ($stop['times'] as $t) {
                    echo "Test time: $t >= $time\n";
                    if ($t >= $time) {
                        $hasTime = true;
                        break;
                    }
                }
                if ($hasTime) {
                    $sortTime[$routeId] = $t;
                    $hits[$routeId] = array(
                        'id' => $routeId,
                        'name' => $route['name'],
                        'time' => $t
                    );
                }
            }
        }
    }
    array_multisort($sortTime, SORT_ASC, $hits);
    $view->assign('routes', $hits);
    $view->assign('from', $from);
}

$view->display('search.tpl');
