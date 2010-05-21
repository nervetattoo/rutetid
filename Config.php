<?php
if (file_exists('my.config.php'))
    require_once('my.config.php');
if (!defined('PREFIX'))
    define('PREFIX', 'main');

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
        $dbName = PREFIX;
        $mongo = new Mongo;
        return $mongo->$dbName;
    }
}
