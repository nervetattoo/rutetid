<?php

require_once("Config.php");
require_once("View.php");

$db = Config::getDb();
$view = new View;

if (isset($_POST['bus']) && isset($_POST['stopIndex']) && isset($_POST['stopName']))
{
    $busInfo   = explode(':', $_POST['bus']);
    $busNumber = $busInfo[0];
    $busName   = $busInfo[1];
    $stopIndex = $_POST['stopIndex'];
    $stopName  = $_POST['stopName'];
    $stopTime  = $_POST['stopTime'];
    
    $stop = $db->stops->findOne(array('name' => $stopName));
    if (!isset($stop['_id']))
    {
        exit('OBS! Fant ikke stoppet');
    }
    $stopId = $stop['_id'];

    $route = null;
    $routes = $db->buses->find(array('id' => $busNumber));
    foreach ($routes as $_route)
    {
        if ($busName == $_route['name'])
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

if (isset($_GET['bus']))
{
    $busInfo   = explode(':', $_POST['bus']);
    $busNumber = $busInfo[0];
    $busName   = $busInfo[1];

    $route = null;
    $routes = $db->buses->find(array('id' => $busNumber));
    foreach ($routes as $_route)
    {
        if ($busName == $_route['name'])
        {
            $route = $_route;
            break;
        }
    }
    if ($route)
    {
        $view->assign('stops', $route['stops']);
    }
    else
    {
        exit('OBS! Fant ikke ruten');
    }
}

$buses = $db->buses->find();
$busList = array();
while ($bus = $buses->getNext())
{
    $busId = $bus['id'];
    foreach ($bus['routes'] as $route)
        $busList[] = array('id'=>$busId, 'name'=>$route['name']);
}
$view->assign('routes', $busList);

$view->display('insert.tpl');
