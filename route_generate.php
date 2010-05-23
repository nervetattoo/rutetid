<?php
require_once("Config.php");
require_once("View.php");
require_once("libs/BusStops.php");

$db = Config::getDb();
$view = new View;

if (isset($_GET['num'])) {
    $num = $_GET['num'];
    $stops = explode("\n", $_GET['stops']);
    $xml = "<routes>\n
        <bus id=\"$num\">\n";
    $stopXML = array();
    foreach ($stops as $key => $stop) {
        $stop = str_replace("\n", "", $stop);
        $stop = str_replace("\r", "", $stop);
        $stops[$key] = $stop;
        if (empty($stop))
            continue;
        $stopXML[] = "<stop name=\"$stop\" time=\"0\" />";
    }
    $xml .= "<route name=\"tour\">\n" .
        implode("\n\t", $stopXML) . "\n" .
        "</route>\n<route name=\"retour\">\n" .
        implode("\n\t", array_reverse($stopXML)) . "\n</route>\n</bus>\n</routes>";
}

$view->assign("xml", $xml);
$view->display("route_generate.tpl");
