<?php
require_once("libs/Config.php");
require_once("libs/BusStops.php");

$db = Config::getDb();

$db->routes->drop();
$db->departures->drop();
$db->stops->drop();
$db->progress->drop();

// Tracking progress
$db->progress->ensureIndex(array('name' => 1), array('unique' => true));
// Departures
$db->departures->ensureIndex(array('route'=>1, 'days'=>1, 'time'=>1),array('unique'=>true, 'dropDups'=>true));
// Routes
$db->routes->ensureIndex(array('num'=>1, 'dest' => 1, 'hash' => 1),array('unique'=>true, 'dropDups'=>true));
// Stops
$db->stops->ensureIndex(array('name'=>1),array('unique'=>true, 'dropDups'=>true));
$stopsCache = array();

// First import bus stops
$fil = "exports/stops.xml";
if (is_file($fil) && strpos($fil, ".xml") !== false && !strpos($fil, ".swp")) {
    $xml = simplexml_load_file($fil);

    foreach ($xml->stop as $s) {
        $a = $s->attributes();
        $hashname = sha1(toLower((string)$a->name));
        $stop = array(
            'name' => (string)$a->name,
            'aliases' => array(),
            'connectsFrom' => array(),
            'connectsTo' => array(),
            'active' => ($a->active == 1) ? true : false,
            'search' => array()
        );
        if (isset($a->location)) {
            list($lat,$long) = explode(",", (string)$a->location);
            $stop['location'] = array((float)$lat, (float)$long);
        }
        foreach ($s->aliases->item as $alias) {
            $stop['search'][] = toLower((string)$alias);
            $stop['aliases'][] = (string)$alias;
        }
        foreach ($s->connectsFrom->item as $cf) {
            $stop['connectsFrom'][] = toLower((string)$cf);
        }
        $db->stops->insert($stop);
        $stopsCache[$hashname] = $stop;
    }
}

// Now routes and departures
$fil = "exports/routes.xml";
if (is_file($fil) && strpos($fil, ".xml") !== false && !strpos($fil, ".swp")) {
    $xml = simplexml_load_file($fil);

    foreach ($xml->route as $s) {
        $a = $s->attributes();
        $fromHash = sha1(toLower((string)$a->from));
        $toHash = sha1(toLower((string)$a->to));
        $from = $stopsCache[$fromHash];
        $to = $stopsCache[$toHash];
        $route = array(
            'num' => (string)$a->num,
            'dest' => (string)$a->dest,
            'hash' => (string)$a->hash,
            'stops' => array(),
            'from' => (string)$a->from,
            'fromId' => $from['_id'],
            'to' => (string)$a->to,
            'toId' => $to['_id'],
            'search' => array()
        );
        foreach ($s->stops->stop as $st) {
            $sa = $st->attributes();
            $stophash = sha1(toLower((string)$sa->name));
            if (array_key_exists($stophash, $stopsCache))
                $stt = $stopsCache[$stophash];
            else
                exit("Failed to find stop: {$sa->name}");
            $route['stops'][] = array(
                'name' => (string)$sa->name,
                'stopId' => $stt['_id'],
                'timeDiff' => (int)$sa->timeDiff,
                'timeOffset' => (int)$sa->timeOffset
            );
            $route['search'][] = toLower($sa->name);
        }
        $db->routes->insert($route);
        // Handle departures
        foreach ($s->departures->dep as $deps) {
            $da = $deps->attributes();
            $days = array();
            if ((string)$da->days != "") {
                foreach (explode(";", (string)$da->days) as $day)
                    $days[] = (int)$day;
            }
            else
                $days = array(1,2,3,4,5,6,7);
            $db->departures->insert(array(
                'route' => $route['_id'],
                'days' => $days,
                'time' => (int)$da->time
            ));
        }
    }
}
