<?php
require_once("Config.php");
require_once("View.php");
require_once("libs/BusStops.php");

$db = Config::getDb();

// Clear db
$db->import->drop();

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
            $hashbase = "";
            $stops = array();
            $departures = array(
                1 => array(),
                6 => array(),
                7 => array()
            );
            $weekday = 7; // Force sunday for now
            foreach ($r->stop as $st) {
                $key++;
                $name = (string)$st;
                $hashbase .= $name;
                $sa = $st->attributes();
                //echo "Parsing stop $key $st";

                if (!$first) {
                    // This will be the total minutes after day break
                    $lastMin = ($sH * 60) + $sM; 
                }
                else {
                    $departures[$weekday][] = (string)$sa->departure;
                    $depart = str_replace(":", "", $sa->departure);
                }

                if (isset($sa->arrival))
                    list($sH, $sM) = explode(":", $sa->arrival);
                else // First stop
                    list($sH, $sM) = explode(":", $sa->departure);

                if (!$first) {
                    $busStops[$name] = BusStops::getStop($name);
                    $to = $name;
                    $toId = $busStops[$name]['_id'];
                    $timeDiff = date("i", mktime($sH, (int)($sM - $lastMin)));
                }
                else {
                    $busStops[$name] = BusStops::getStop($name);
                    $from = $name;
                    $fromId = $busStops[$name]['_id'];
                    $timeDiff = 0;
                }

                //echo " +$timeDiff\n";
                // Store last time diff
                $first = false;
                $stops[] = array(
                    'name' => $name,
                    'timeDiff' => $timeDiff
                );
            }
            $stopHash = sha1($hashbase);

            // Set route in db 
            $route = $db->import->findOne(array(
                'num' => $num, 
                'dest' => $dest,
                'hash' => $stopHash
            ));
            $departure = array(
                'from' => $from,
                'fromId' => $fromId,
                'to' => $to,
                'toId' => $toId,
                'days' => array(1,2,3,4,5,6,7),
                'time' => (int)$depart,
                'route' => $num
            );
            $db->departures->insert($departure);
            $deps++;
            if ($route === null) {
                $route = array(
                    'num' => $num, 
                    'dest' => $dest,
                    'hash' => $stopHash,
                    'stops' => $stops,
                    'departures' => $departures
                );
                if ($db->import->insert($route)) {
                    $cnt = count($departures[$weekday]);
                    echo "Inserted $num $dest now has $cnt departures on $weekday\n";
                }
            }
            else {
                $departures[$weekday] = array_unique(array_merge($route['departures'][$weekday], $departures[$weekday]));
                $res = $db->import->update(
                    array('_id' => $route['_id']),
                    array('$set' => array(
                        "departures" => $departures
                    ))
                );
                if ($res) {
                    $cnt = count($departures[$weekday]);
                    echo "Updated $num $dest now has $cnt departures on $weekday\n";
                }
            }
        }
    }
}
