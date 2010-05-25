<?php
require_once("View.php");
require_once("libs/RouteSearch.php");
if (file_exists('my.config.php'))
    require_once('my.config.php');
if (!defined('PREFIX'))
    define('PREFIX', 'main');


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
