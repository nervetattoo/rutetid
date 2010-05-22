<?php
require_once("Config.php");
require_once("View.php");

$db = Config::getDb();

$routes = $db->routes->find();
while ($route = $routes->getNext()) {
    echo '<pre>';
    print_r($route);
    echo '</pre>';
}
echo "foo";
