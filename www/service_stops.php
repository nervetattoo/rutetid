<?php
function getMicroTime() { 
    list($usec, $sec) = explode(" ", microtime()); 
    return ((float)$usec + (float)$sec); 
}
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
require_once("../Config.php");

$start = getMicroTime();
$memcache = new Memcache;
$memcache->connect('localhost', 11211);

$filters = array();
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
    $filters['search'] =  new MongoRegex("/^$query/i");
}

$cacheKey = md5(r_implode(":", $filters));
$result = $memcache->get($cacheKey);
if ($result)
    $result = unserialize($result);
else {
    // Find stops near me!!
    $db = Config::getDb();
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
