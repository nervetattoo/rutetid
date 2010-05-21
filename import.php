<?php
require_once("Config.php");
require_once("View.php");

$db = Config::getDb();

$file = file('data/23.txt');
$routeNumber = array_shift($file);
list($name, $number) = explode(";", $routeNumber);

// Flush routes
$db->routes->drop();
$db->routes->ensureIndex(array('id'=>1), array('unique'=>true));
$db->routes->ensureIndex(array('name'=>1));

$db->stops->drop();
$db->stops->ensureIndex(array('name'=>1),array('unique'=>true, 'dropDups'=>true));


$route = array(
    'id' => $number,
    'name' => $name,
    'stops' => array()
);

$started = false;
foreach ($file as $line) {
    $arr = explode(";", $line);
    $stopName = array_shift($arr);
    $times = array();
    foreach ($arr as $s) {
        $s = str_replace("\n", "", $s);
        if (preg_match("/[0-9]{1,2}\.[0-9]{1,2}/", $s)) {
            $started = true;
            $times[] = $s;
        }
    }
    $db->stops->insert(array('name'=>$stopName));
    if (count($times) > 0 || $started) {
        $stop = array(
            'name' => $stopName,
            'times' => $times
        );
        $route['stops'][] = $stop;
    }
}
// Flip array and remove all entries of stops that have no times
$flipped = array_reverse($route['stops']);

foreach ($flipped as $key => $tmp) {
    if (count($tmp['times']) == 0)
        unset($flipped[$key]);
    else
        break;
}
$route['stops'] = array_reverse($flipped);

$db->routes->insert($route);
