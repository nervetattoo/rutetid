<?php
require_once("Config.php");
require_once("View.php");
require_once("libs/BusStops.php");

$db = Config::getDb();

// Clear db
//$db->buses->drop();
//$db->departures->drop();

// Flush routes
/*
$db->stops->drop();
$db->stops->ensureIndex(array('name'=>1),array('unique'=>true, 'dropDups'=>true));

$xml = simplexml_load_file("data/routes.xml");
$busStopImporter = new BusStops;
echo "Start importing bus stops\n";
$imported = $busStopImporter->import("data/busstops.csv");
echo "Imported $imported bus stops\n";

// Caching
$busStops = array();
// Searchable list of routes
array(
    "
$stopsSearch = array();

$deps = 0;
$activeStops = 0;
foreach ($xml->bus as $node) {
    $attr = $node->attributes();
    $bus = array(
        'id' => (string)$attr['id'],
        'routes' => array(),
    );
    $compactStops = array();
    foreach ($node->route as $route) {
        $attr = $route->attributes();
        $r = array(
            'name' => (string)$attr['name'],
            'stops' => array()
        );
        foreach ($route->stop as $st) {
            $stopAttrs = $st->attributes();
            $stopName = (string)$stopAttrs['name'];
            $time = (string)$stopAttrs['time'];
            $stop = BusStops::getStop($stopName);
            if (BusStops::activateStop($stop))
                $activeStops++;
            $r['stops'][] = array(
                'name' => $stopName,
                'time' => $time,
                'stopId' => $stop['_id']
            );
            $compactStops[] = $stopName;
        }
        $bus['routes'][] = $r;
    }
    $compactStops = array_unique($compactStops);
    foreach ($compactStops as $cp)
        $bus['search']['stops'][] = $cp;
    echo "Insert bus: \n";
    $db->buses->insert($bus);
    echo "Start importing departures\n";

    // Import departures
    foreach ($node->run as $xmlRun) {
        $runAttr = $xmlRun->attributes();
        $start = (string)$runAttr['start'];
        $stop = (string)$runAttr['stop'];
        $route = (string)$runAttr['route'];
        $days = explode(",", (string)$runAttr['days']);
        foreach ($days as $dk => $dv)
            $days[$dk] = (int)$dv;
        $times = explode(";", str_replace("\n", "", (string)$xmlRun->times));
        foreach ($times as $key => $time) {
            $time = str_replace(" ", "", $time);

            if (!isset($busStops[$start]))
                $busStops[$start] = BusStops::getStop($start);
            if (!isset($busStops[$stop]))
                $busStops[$stop] = BusStops::getStop($stop);

            if (!is_array($days))
                $days = array($days);
            // One departure for each time
            $departure = array(
                'from' => $start,
                'fromId' => $busStops[$start]['_id'],
                'to' => $stop,
                'toId' => $busStops[$stop]['_id'],
                'days' => $days,
                'time' => (int)str_replace(":", "", $time),
                'route' => $bus['id'],
                'routeId' => $bus['_id']
            );
            $db->departures->insert($departure);
            $deps++;
        }
    }
} 
echo "Inserted $deps departures\n";
*/
echo "Activated $activeStops stops\n";
