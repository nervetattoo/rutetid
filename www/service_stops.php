<?php
require_once("../Config.php");
require_once("../libs/RouteSearch.php");

$start = getMicroTime();
$memcache = new Memcache;
$memcache->connect('localhost', 11211);
$routeSearch = new RouteSearch;

$cacheKey = "";
if (isset($_GET['term'])) {
    $query = toLower($_GET['term']);
    $filters['search'] =  new MongoRegex("/^$query/i");
    $cacheKey .= $query;
}
elseif (isset($_GET['lat']) && isset($_GET['long'])) {
    $lat = $_GET['lat'];
    $long = $_GET['long'];
    $regex = '/^[0-9]+\.[0-9]*$/';
    $cacheKey .= $lat . "," . $long;
    if (preg_match($regex, $lat) && preg_match($regex, $long)) {
        $filters['location'] = array(
            '$near' => array((float)$lat, (float)$long)
        );
    }
}
if (isset($_GET['from']) && !empty($_GET['from'])) {
    $from = toLower($_GET['from']);
    $filters['connectsFrom'] = $from;
}

$cacheKey = r_implode(":", $filters);
$result = $memcache->get($cacheKey);
if ($result)
    $result = unserialize($result);
else {
    // Find stops near me!!
    $db = Config::getDb();
    $db->routes->ensureIndex(array('search'=>1));
    $filters['active'] = true;
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
    $result['generated'] = time();
    $result['length'] = count($result['stops']);
    $memcache->set($cacheKey, serialize($result), false, 7200);
}
$time = getMicroTime() - $start;
$result['time'] = $time;
if (isset($_GET['callback'])) {
    $jsonp = $_GET['callback'];
    $out =  $jsonp . "(" . json_encode($result) . ")";
}
else
    $out = json_encode($result);

echo $out;
