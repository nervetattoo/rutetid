<?php
require_once("Config.php");
require_once("View.php");

$db = Config::getDb();

$file = file('data/23.txt');
$routeNumber = array_shift($file);
list($name, $number) = explode(";", $routeNumber);
$route = array(
    'id' => $number,
    'name' => $name,
    'stops' => array()
);

foreach ($file as $line) {
    $arr = explode(";", $line);
    $stopName = array_shift($arr);
    $route['stops'][] = array(
        'name' => $stopName,
        'times' => $arr
    );
}

$db->routes->insert($route);
