<?php
require_once("gPoint.php");

/**
 * Import the bus dump from eiendomsprofilen
 * This will add a lot of stops
 * @author Raymond Julin
 */

class BusStops {

    public function import($file) {
        $db = Config::getDb();
        // Lat/long limits, should be converted
        $top = 60.47108; //lat
        $bottom = 60.30108; //lat
        $left = 5.125268; //long
        $right = 5.525268; //long

        $gPoint = new gPoint();

        $gPoint->setLongLat($left, $top);
        $gPoint->convertLLtoTM();
        $utmTop = (int)$gPoint->N();
        $utmLeft = (int)$gPoint->E();

        $gPoint->setLongLat($right, $bottom);
        $gPoint->convertLLtoTM();
        $utmBottom = (int)$gPoint->N();
        $utmRight = (int)$gPoint->E();

        $handle = fopen($file, "r");
        $i = 0;
        $db->stops->ensureIndex(array('location' => '2d'));
        if ($handle) {
            //echo "Top: $utmTop Bottom: $utmBottom Left: $utmLeft Right: $utmRight\n";
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
                        $db->stops->insert(array(
                            'name'=>$name,
                            'location' => array(
                                'lat'=>$lat,
                                'long'=>$long
                            )
                        ));
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
