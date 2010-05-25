<?php
require_once("View.php");
require_once("libs/RouteSearch.php");
if (file_exists('my.config.php'))
    require_once('my.config.php');
if (!defined('PREFIX'))
    define('PREFIX', 'main');


function r_implode($glue, $pieces) {
    $retVal = array();
    foreach($pieces as $r_pieces) {
        if(is_array($r_pieces))
            $retVal[] = r_implode($glue, $r_pieces);
        else
            $retVal[] = $r_pieces;
    }
    return implode($glue, $retVal);
} 
function getMicroTime() { 
    list($usec, $sec) = explode(" ", microtime()); 
    return ((float)$usec + (float)$sec); 
}
function toLower($text) {
    return mb_strtolower($text, "UTF-8");
}

class Config {
    private static $path = null;
    private static $db = null;
    public static function getPath() {
        if (self::$path == null) {
            $path = split("/",pathinfo(__FILE__, PATHINFO_DIRNAME));
            self::$path = join("/", $path) . "/";
        }
        return self::$path;
    }

    public static function getDb() {
        if (self::$db == null) {
            $dbName = PREFIX;
            $mongo = new Mongo;
            self::$db = $mongo->$dbName;
        }
        return self::$db;
    }

    public static function getView() {
        return new View;
    }

    public static function timeAdd($t1, $t2, $format="H:i") {
        $t1 = str_pad($t1, 4, "0", STR_PAD_LEFT);
        $t2 = str_pad($t2, 4, "0", STR_PAD_LEFT);

        $fi = substr($t1, -2);
        $fH = substr($t1, 0, -2);
        $si = substr($t2, -2);
        $sH = substr($t2, 0, -2);

        $newTime = date($format, mktime($fH, ($fi + $si) + ($sH * 60)));
        return $newTime;
    }

    public static function timeSub($t1, $t2,$format="Hi") {
        $t1 = str_pad($t1, 4, "0", STR_PAD_LEFT);
        $t2 = str_pad($t2, 4, "0", STR_PAD_LEFT);
        $fi = substr($t1, -2);
        $fH = substr($t1, 0, -2);
        $si = substr($t2, -2);
        $sH = substr($t2, 0, -2);

        $newTime = date($format, mktime($fH - $sH, $fi - $si));
        return $newTime;
    }

    /**
     * Parse a traffic days text and return an array over running days
     *
     * @return array Array of days this text implies, and in the future what exceptions it implies
     * @param string $text
     */
    public static function parseTrafficDaysText($text) {
        $text = str_replace("kjører ", "", $text);
        $parts = explode(" ", $text);
        $days = array(
            'ma' => 1,
            'ti' => 2,
            'on' => 3,
            'to' => 4,
            'fr' => 5,
            'lø' => 6,
            'sø' => 7
        );
        $start = false;
        $end = false;
        $exceptions = false;
        foreach ($parts as $p) {
            $p = str_replace(array(".",","),array("",""), $p);
            $p = toLower($p);
            if (is_numeric($p)) {
                $exceptions[] = $p;
            }
            else {
                if (is_string($p) && array_key_exists($p, $days)) {
                    // This is a valid day
                    if (!$start)
                        $start = $days[$p];
                    elseif ($start && !$end && $p != "ikke")
                        $end = $days[$p];
                    elseif ($p == "ikke" && !$exceptions)
                        break;
                }
            }
        }
        $return = array();
        if ($start) {
            if (!$end)
                $end = $start;
            for ($i = $start; $i <= $end; $i++)
                $return[] = $i;
        }
        return $return;
    }
}
