<?php
require_once("../Config.php");
require_once("../libs/BusStops.php");

$db = Config::getDb();
$view = new View;

$routes = $db->routes->find();
$routeList = array();
$routeSort = array();
while ($route = $routes->getNext())
{
    if (isset($_GET['route']))
        $getId = $_GET['route'];
    else
        $getId = false;
    $id = $route['_id'];
    $routeSort[] = $route['num'];
    $routeList[] = array(
        'id' => $id,
        'num' => $route['num'],
        'dest' => $route['dest'],
        'selected' => ($getId && $getId == $id)
    );
}
array_multisort($routeSort, SORT_ASC, $routeList);
$view->assign('routes', $routeList);

if (isset($_GET['route_json']))
{
    list($busId, $routeKey) = explode('_', $_GET['route_json']);
    $stops = $route['stops'];

    $route = null;
    $bus = null;
    foreach ($buses as $bus)
        if ($bus['id'] == $busId)
            $route = $bus['routes'][$routeKey];
    if ($route && $bus) {
        $stops = $route['stops'];
    }

    exit(json_encode(array('stops' => $stops)));
}

if (isset($_GET['route']))
{
    $routeId = $_GET['route'];
    $view->assign('stops', $route['stops']);

    $route = null;
    $routes->reset();
    while ($r = $routes->getNext()) {
        if ($r['_id'] == $routeId)
            $route = $r;
    }
    if ($route) {
        $stops = $route['stops'];
        $markers = array();
        $markerUrl = "";
        $mapPath = array(
            'color:0xff0000ff',
            'weight:4'
        ); // Fill the rest with coord path
        $i = 0;
        foreach ($stops as $st) {
            $stop = BusStops::getStop($st['name']);
            $i++;
            if ($stop !== null && isset($stop['location']) && $i % 4) {
                $m = $stop['location'][0] . "," . $stop['location'][1];
                $markers[] = $m;
                $markerUrl .= "&markers=size:mid|color:red|label:$i|$m";
            }
        }
        $mapPath = array_merge($mapPath, $markers);
        $mapUrl = "http://maps.google.com/maps/api/staticmap?path=" . implode('|', $mapPath);
        $mapUrl .= "&size=512x512&sensor=false" . $markerUrl;

        $view->assign('mapUrl', $mapUrl);
        $view->assign('stops', $route['stops']);
    }
}


if (isset($_POST['route']) && isset($_POST['stopIndex']) && isset($_POST['stopName']))
{
    $routeInfo   = explode('_', $_POST['route']);
    $routeNumber = $routeInfo[0];
    $routeName   = $routeInfo[1];
    $stopIndex   = $_POST['stopIndex'];
    $stopName    = $_POST['stopName'];
    $stopTime    = $_POST['stopTime'];
    
    $stop = $db->stops->findOne(array('name' => $stopName));
    if (!isset($stop['_id']))
    {
        exit('OBS! Fant ikke stoppet');
    }
    $stopId = $stop['_id'];

    $route = null;
    $bus = $db->buses->findOne(array('id' => $routeNumber));
    foreach ($bus['routes'] as &$_route)
    {
        if ($stopName == $_route['name'])
        {
            $route = &$_route;
            break;
        }
    }
    if ($route)
    {
        if (isset($route['stops'][$stopIndex]))
        {
            $stopsBefore = array_slice($route['stops'], 0, $stopIndex + 1);
            $stopsAfter  = array_slice($route['stops'], $stopIndex + 1);

            $newStop = array(array(
                'name'   => $stopName,
                'time'   => $stopTime,
                'stopId' => $stopId
            ));

            $route['stops'] = array_merge($stopsBefore, $newStop, $stopsAfter);
            $db->buses->save($bus);
        }
    }
    else
    {
        exit('OBS! Fant ikke ruten');
    }
}

$view->display('insert.tpl');
