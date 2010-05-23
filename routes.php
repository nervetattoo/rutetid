<?php
require_once("Config.php");
require_once("View.php");
require_once("libs/BusStops.php");

$db = Config::getDb();
$view = new View;


if (isset($_GET['name']) && isset($_GET['to'])) {
}
elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $route = $db->routes->findOne(array('id'=>$id));
    $markers = array();
    $markerUrl = "";
    $mapPath = array(
        'color:0xff0000ff',
        'weight:4'
    ); // Fill the rest with coord path
    $i = 0;
    foreach ($route['stops'] as $s) {
        $i++;
        $stop = BusStops::getStop($s['name']);
        if ($stop !== null) {
            $m = $stop['location'][0] . "," . $stop['location'][1];
            $markers[] = $m;
            $markerUrl .= "&markers=size:mid|color:red|label:$i|$m";
        }
    }
    $mapPath = array_merge($mapPath, $markers);
    $mapUrl = "http://maps.google.com/maps/api/staticmap?path=" . implode('|', $mapPath);
    $mapUrl .= "&size=512x512&sensor=false" . $markerUrl;

    $view->assign('mapUrl', $mapUrl);
    $view->assign('route', $route);
    $view->display("route.tpl");
}
else {
    $routes = $db->routes->find()->sort(array('id'=>1));
    $data = array();
    while ($route = $routes->getNext()) {
        $data[] = array(
            'id' => $route['id'],
            'name' => $route['name'],
            'stops' => $route['stops'],
        );
    }
    $view->assign('routes', $data);
    $view->display("routes.tpl");
}
