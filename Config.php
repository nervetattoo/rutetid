<?php

class Config {
    private static $path = null;
    public static function getPath() {
        if (self::$path == null) {
            $path = split("/",pathinfo(__FILE__, PATHINFO_DIRNAME));
            self::$path = join("/", $path) . "/";
        }
        return self::$path;
    }
}
