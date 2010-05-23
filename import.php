<?php
require_once("Config.php");
require_once("View.php");
require_once("libs/BusStops.php");

$db = Config::getDb();


// Flush routes
/*
$db->routes->drop();
$db->routes->ensureIndex(array('id'=>1), array('unique'=>true));
$db->routes->ensureIndex(array('name'=>1));

$db->stops->drop();
$db->stops->ensureIndex(array('name'=>1),array('unique'=>true, 'dropDups'=>true));

// Import bus stops first
$busStopImporter = new BusStops;
echo "Start importing bus stops\n";
$imported = $busStopImporter->import("crap/buss-dump.csv");
echo "Imported $imported bus stops from crap/buss-dump.csv\n";

$dir = scandir('data/');

foreach ($dir as $file) {
    $tmpFile = $file;
    $file = 'data/' . $file;
    if (is_file($file) && substr($tmpFile,0,1) && strpos($file, "txt") !== false) {
        $fileLines = file($file);
        $routeNumber = array_shift($fileLines);
        list($number, $name) = explode(";", $routeNumber);

        $route = array(
            'id' => $number,
            'name' => $name,
            'stops' => array()
        );

        foreach ($fileLines as $line) {
            $arr = explode(";", $line);
            $stopName = array_shift($arr);
            $times = array();
            foreach ($arr as $s) {
                $s = str_replace("\n", "", $s);
                if (preg_match("/[0-9]{1,2}:[0-9]{1,2}/", $s))
                    $times[] = str_replace(":", "", $s);
            }
            $stopObject = BusStops::getStop($stopName);
            if ($stopObject === null) {
                echo "'$stopName' does not exist among stops<br>\n";
                //$db->stops->insert(array('name'=>$stopName));
                //$stopObject = BusStops::getStop($stopName);
            }
            $stop = array(
                'stopId' => $stopObject['_id'],
                'name' => $stopName,
                'times' => $times
            );
            $route['stops'][] = $stop;
        }
        $db->routes->insert($route);
    }
}

echo "Imported routes\n";
*/
