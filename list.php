<?php
require_once("Config.php");
require_once("View.php");

$db = Config::getDb();

$routes = $db->routes->find();
while ($route = $routes->getNext()) {
    print_r($route);
}
echo "foo";
