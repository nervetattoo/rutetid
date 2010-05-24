<?php
require_once("../Config.php");
require_once("../libs/BusStops.php");

$db = Config::getDb();
$view = Config::getView();

$stops = BusStops::getStopList(true, true);
$stopList = array();
while ($s = $stops->getNext()) {
    $id = (string)$s['_id'];
    $stopList[$id] = array(
        'id' => $id,
        'name' => $s['name']
    );
}
$view->assign("stops", $stopList);

if (isset($_GET['stop']) && !empty($_GET['stop'])) {
    $stopId = $_GET['stop'];
    $stop = BusStops::byId($stopId);

    if ($stop) {
        if (isset($_GET['update']) && isset($_GET['lat']) && isset($_GET['lng'])) {
            $lat = (float)$_GET['lat'];
            $lng = (float)$_GET['lng'];
            if (BusStops::updatePos($stop, $lat, $lng))
                exit(json_encode(array("ok"=>true)));
            else
                exit(json_encode(array("ok"=>false)));
        }
        list($lat,$long) = $stop['location'];
        $view->assign("lat", $lat);
        $view->assign("long", $long);
        $view->assign("stop", $stop);
    }
}
$view->display('map.tpl');
