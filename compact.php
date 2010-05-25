<?php
require_once("Config.php");
require_once("View.php");
require_once("libs/BusStops.php");

$db = Config::getDb();

// Clear db
$db->routes->drop();
$db->departures->drop();

$db->departures->ensureIndex(array('route'=>1, 'days'=>1, 'time'=>1),array('unique'=>true, 'dropDups'=>true));
$db->routes->ensureIndex(array('num'=>1, 'dest' => 1, 'hash' => 1),array('unique'=>true, 'dropDups'=>true));

$busStops = array();
$files = scandir("data/ruter/");
foreach ($files as $fil) {
    $fil = "data/ruter/" . $fil;
    if (is_file($fil) && strpos($fil, ".xml") !== false && !strpos($fil, ".swp")) {
        $xml = simplexml_load_file($fil);
        echo "Parsing file $fil\n";
        foreach ($xml->route as $r) {
            $a = $r->attributes();
            $num = (string)$a->no;
            $trafficDays = (string)$a->trafficDays;
            $days = Config::parseTrafficDaysText($trafficDays);
            if ($num == "BUSSNatt")
                $num = "Nattbuss";
            else
                $num = (int)$num;
            $dest = (string)$a->destination;
            /**
             * Loop over stops and build a neat array over them
             * Also create some funky hash to assure uniqueness of stop routes
             */
            $first = true;
            $key = 0;
            $hashbase = "$num:" . $dest;
            $stops = array();
            $stopsSearch = array();
            $timeOffset = 0;
            foreach ($r->stop as $st) {
                $key++;
                $name = (string)$st;
                // 
                if (!array_key_exists($name, $busStops)) {
                    $stop = BusStops::getStop($name);
                    if ($stop === null) {
                        $stop = $db->stops->insert(array(
                            'name' => $name,
                            'aliases' => array($name),
                            'active' => true,
                            'search' => array(toLower($name))
                        ));
                    }
                    else {
                        BusStops::activateStop($stop);
                    }
                    $busStops[$name] = $stop;
                }
                else
                    $stop = $busStops[$name];
                $hashbase .= $name;
                $sa = $st->attributes();
                //echo "Parsing stop $key $st";

                if (!$first) {
                    // This will be the total minutes after day break
                    $lastMin = ($sH * 60) + $sM; 
                }
                else {
                    $firstTimes = explode(":", "", $sa->departure);
                    $depart = str_replace(":", "", $sa->departure);
                }

                if (isset($sa->arrival))
                    list($sH, $sM) = explode(":", $sa->arrival);
                else // First stop
                    list($sH, $sM) = explode(":", $sa->departure);

                if (!$first) {
                    $to = $name;
                    $toId = $busStops[$name]['_id'];
                    $timeDiff = (int)date("i", mktime((int)$sH, (int)($sM - $lastMin)));
                    $timeOffset += $timeDiff;
                }
                else {
                    $from = $name;
                    $fromId = $busStops[$name]['_id'];
                    $timeDiff = 0;
                    $timeOffset = 0;
                }

                //echo " +$timeDiff\n";
                // Store last time diff
                $first = false;
                $stopsSearch[] = toLower($name);
                $stops[] = array(
                    'name' => $name,
                    'stopId' => $stop['_id'],
                    'timeDiff' => $timeDiff,
                    'timeOffset' => $timeOffset
                );
            }
            $stopHash = sha1($hashbase);

            // Set route in db 
            $route = $db->routes->findOne(array(
                'num' => $num, 
                'dest' => $dest,
                'hash' => $stopHash
            ));
            if ($route === null) {
                $route = array(
                    'num' => $num, 
                    'dest' => $dest,
                    'hash' => $stopHash,
                    'stops' => $stops,
                    'from' => $from,
                    'fromId' => $fromId,
                    'to' => $to,
                    'toId' => $toId,
                    'search' => $stopsSearch
                );
                if ($route = $db->routes->insert($route)) {
                    echo "Inserted $num $dest\n";
                }
            }
            $departure = array(
                'route' => $route['_id'],
                'days' => $days,
                'time' => (int)$depart
            );
            if ($db->departures->insert($departure)) {
                $deps++;
            }
        }
    }
}
