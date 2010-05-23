<?php

require_once("Config.php");
require_once("View.php");

$db = Config::getDb();
$view = new View;

$buses = $db->buses->find();
$busList = array();
while ($bus = $buses->getNext())
{
    $busId = $bus['id'];
    if (isset($_GET['route']))
        $routeId = $_GET['route'];
    else
        $routeId = false;
    foreach ($bus['routes'] as $i => $route) {
        $busList[] = array(
            'id'=>$busId, 
            'name'=>$route['name'], 
            'i'=>$i,
            'selected' => ($routeId && $routeId == $busId."_".$i)
        );
    }
}
$view->assign('routes', $busList);

if (isset($_GET['route']))
{
    list($busId, $routeKey) = explode('_', $_GET['route']);
    $view->assign('stops', $route['stops']);

    $route = null;
    $bus = null;
    foreach ($buses as $bus)
        if ($bus['id'] == $busId) 
            $route = $bus['routes'][$routeKey];
    if ($route && $bus) {
        $stops = $route['stops'];
        $view->assign('stops', $route['stops']);
    }
}


if (isset($_POST['route']) && isset($_POST['stopIndex']) && isset($_POST['stopName']))
{
    $routeInfo   = explode('_', $_POST['route']);
    $routeNumber =  $routeInfo[0];
    $routeName   =  $routeInfo[1];
    $stopIndex   = $_POST['stopIndex'];
    $stopName    = $_POST['stopName'];
    $stopTime    = $_POST['stopTime'];
    
    $stop = $db->stops->findOne(array('name' => $stopName));
    if (!isset($stop['_id']))
    {
        exit('OBS! Fant ikke stoppet');
    }
    $stopId = (string) $stop['_id'];

    $route = null;
    $routes = $db->buses->find(array('id' => $routeNumber));
    foreach ($routes as $_route)
    {
        if ($routeName == $_route['name'])
        {
            $route = $_route;
            break;
        }
    }
    if ($route)
    {
        if (isset($route['stops'][$stopIndex]))
        {
            $stopsBefore = array_slice($route['stops'], 0, $stopIndex + 1);
            $stopsAfter  = array_slice($route['stops'], $stopIndex + 1);

            $newStop = array(
                'name'   => $stopName,
                'time'   => $stopTime,
                'stopId' => $stopId
            );

            $route['stops'] = array_merge($stopsBefore, $newStop, $stopsAfter);
            $route->save();
        }
    }
    else
    {
        exit('OBS! Fant ikke ruten');
    }
}

$view->display('insert.tpl');
