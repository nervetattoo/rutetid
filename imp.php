<?php
require_once("Config.php");
require_once("View.php");
require_once("libs/BusStops.php");

$db = Config::getDb();

$xml = simplexml_load_file("data/routes.xml");
$busStopImporter = new BusStops;

// Clear db
$db->buses->drop();
$db->departures->drop();

// Caching
$busStops = array();

$deps = 0;
foreach ($xml->bus as $node) {
    $attr = $xml->bus->attributes();
    $bus = array(
        'id' => (string)$attr['id'],
        'routes' => array(),
    );
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
            $r['stops'][] = array(
                'name' => $stopName,
                'time' => $time,
                'stopId' => $stop['_id']
            );
        }
        $bus['routes'][] = $r;
    }
    echo "Insert bus: \n";
    print_r($bus);
    $db->buses->insert($bus);
    echo "Start importing departures\n";

    // Import departures
    foreach ($node->run as $xmlRun) {
        $runAttr = $xmlRun->attributes();
        $start = (string)$runAttr['start'];
        $stop = (string)$runAttr['stop'];
        $route = (string)$runAttr['route'];
        $days = explode(",", (string)$runAttr['days']);
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
                'time' => $time,
                'route' => $bus['id'],
                'routeId' => $bus['_id']
            );
            $db->departures->insert($departure);
            $deps++;
        }
    }
} 
echo "Inserted $deps departures\n";
