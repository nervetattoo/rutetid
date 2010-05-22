<?php
function getMicroTime() { 
    list($usec, $sec) = explode(" ", microtime()); 
    return ((float)$usec + (float)$sec); 
}
require_once("Config.php");
require_once("View.php");

$db = Config::getDb();

$filters = array();
$lat = 60.3601528;
$long = 5.347809;
if (isset($_GET['lat']) && isset($_GET['long'])) {
    $lat = $_GET['lat'];
    $long = $_GET['long'];
    $regex = '/^[0-9]+\.[0-9]*$/';
    if (preg_match($regex, $lat) && preg_match($regex, $long)) {
        $filters['location'] = array(
            '$near' => array((float)$lat, (float)$long)
        );
    }
}

if (isset($_GET['term'])) {
    $query = $_GET['term'];
    $filters['name'] = new MongoRegex("/^$query/i");
}


// Find stops near me!!
$start = getMicroTime();
$stops = $db->stops->find($filters);

$result = array(
    //'filters' => $filters,
    'stops' => array()
);
while ($stop = $stops->getNext()) {
    $result['stops'][] = array(
        'name' => $stop['name']
    );
}
$time = getMicroTime() - $start;
$result['time'] = $time;
$result['length'] = count($result['stops']);
echo json_encode($result);
