<?php
require_once("Config.php");
require_once("View.php");

$db = Config::getDb();

$routes = $db->routes->find(array(
  'stops.name' => array('$all' => array('Olav Kyrres gate', 'Lagunen'))
));
while ($route = $routes->getNext()) {
    echo '<pre>';
    print_r($route);
    echo '</pre>';
}
echo "foo";
