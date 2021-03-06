<?php
require_once("gPoint.php");

/**
 * Import the bus dump from eiendomsprofilen
 * This will add a lot of stops
 * @author Raymond Julin
 */

class BusStops {

    public static function getStop($name) {
        $db = Config::getDb();
        // Try aliases
        $stop = $db->stops->find(array(
            'search' => array(
                '$in' => array(toLower($name))
            )
        ))->getNext();
        return $stop;
    }
    public static function byId($id) {
        $db = Config::getDb();
        $stop = $db->stops->findOne(array('_id' => new MongoId($id)));
        return $stop;
    }

    public static function getStopList($active=true,$hasPos=true) {
        $db = Config::getDb();
        $filters = array();
        // Try aliases
        if ($hasPos) {
            $filters['location'] = array(
                '$size' => 2
            );
        }
        if ($active)
            $filters['active'] = true;
        $stops = $db->stops->find($filters);
        return $stops;
    }

    public static function activateStop($stop) {
        $db = Config::getDb();
        $res = $db->stops->update(
            array("_id" => $stop['_id']), // Where clause
            array('$set' => array('active' => true)) // Update
        );
        return $res;
    }

    public static function updatePos($stop, $lat, $lng) {
        $db = Config::getDb();
        $res = $db->stops->update(
            array("_id" => $stop['_id']), // Where clause
            array('$set' => array('location' => array($lat,$lng))) // Update
        );
        return $res;
    }
    
    /**
     * Import bus stops from a csv file fmor eiendomsprofil data
     *
     * @return int
     * @param string $file
     */
    public function import($file) {
        $db = Config::getDb();
        // Lat/long limits, should be converted
        $top = 60.60108; //lat
        $bottom = 59.90108; //lat
        $left = 5.005268; //long
        $right = 5.725268; //long

        $gPoint = new gPoint();

        $gPoint->setLongLat($left, $top);
        $gPoint->convertLLtoTM();
        $utmTop = (int)$gPoint->N();
        $utmLeft = (int)$gPoint->E();

        $gPoint->setLongLat($right, $bottom);
        $gPoint->convertLLtoTM();
        $utmBottom = (int)$gPoint->N();
        $utmRight = (int)$gPoint->E();

        $utmTop = 6800000;
        $utmBottom = 6600000;
        $utmRight = -15000;
        $utmLeft = -46000;

        $handle = fopen($file, "r");
        $i = 0;
        $db->stops->ensureIndex(array('location' => '2d'));
        if ($handle) {
            echo "Top: $utmTop Bottom: $utmBottom Left: $utmLeft Right: $utmRight<br>\n";
            fgets($handle, 4096);
            while (!feof($handle)) {
                $line = fgets($handle);
                if (strlen($line) > 10) {
                    $fields = explode(",", $line);
                    $name = substr($fields[3], 1, -1);
                    $x = (int)substr($fields[8], 1, -1);
                    $y = (int)substr($fields[9], 1, -1);
                    if ($x < $utmRight && $x > $utmLeft && $y < $utmTop && $y > $utmBottom) {
                        $i++;
                        $gPoint->setUTM($x, $y, "33V");
                        $gPoint->convertTMtoLL();
                        $lat = $gPoint->Lat();
                        $long = $gPoint->Long();
                        $location = array((float)$lat, (float)$long);
                        $stop = $db->stops->findOne(array(
                            'location' => $location
                        ));
                        if ($stop === null) {
                            $db->stops->insert(array(
                                'name'=>$name,
                                'location' => $location,
                                'aliases' => array($name),
                                'search' => array(toLower($name)),
                                'connectsFrom' => array(),
                                'connectsTo' => array()
                            ));
                        }
                        else {
                            if (array_key_exists('aliases', $stop))
                                $aliases = $stop['aliases'];
                            else
                                $aliases = array();
                            $aliases[] = $name;
                            $res = $db->stops->update(
                                array("_id" => $stop['_id']), // Where clause
                                array('$set' => array('aliases' => $aliases)) // Update
                            );
                        }
                        unset($stop);
                        //echo "$name lies as $lat,$long\n";
                    }
                }
            }
            fclose($handle);
            return $i;
        }
        return false;
    }
}
