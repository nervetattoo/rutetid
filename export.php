<?php
require_once("libs/Config.php");
require_once("libs/BusStops.php");

/*
 * { "_id" : ObjectId("4c0186558ead0e4116c00000"), 
    "active" : true, 
    "aliases" : [ "Takvam E16", "Takvam" ], 
    "connectsFrom" : [ ], 
    "connectsTo" : [ ], 
    "location" : [ 60.422539197480226, 5.5144880649336425 ], 
    "name" : "Takvam E16", 
    "search" : [ "takvam e16" ] }
 */

function addAttr($dom, $node, $key, $value) {
    // create attribute node
    $attr = $dom->createAttribute($key);
    $attr->appendChild($dom->createTextNode($value));

    $node->appendChild($attr);

    return $node;
}

function exportRoutes($db) {
    $stops = $db->routes->find();

    $exportTo = "exports/routes_" . date("m_d_H_i") . ".xml";
    $exportTo = "exports/routes.xml";
    $dom = new DOMDocument("1.0");
    $dom->formatOutput = true;

    // create root element
    $root = $dom->createElement("routes");
    $dom->appendChild($root);

    // save and display tree
    foreach ($stops as $stop) {
        $item = $dom->createElement("route");
        foreach ($stop as $key => $val) {
            if (in_array($key, array("search","_id","fromId","toId")))
                continue;
            elseif ($key == "stops") {
                $domStops = $dom->createElement($key);
                foreach ($val as $k => $v) {
                    $st = $dom->createElement("stop");
                    foreach ($v as $_k => $_v)
                        if ($_k != "stopId")
                            addAttr($dom, $st, $_k, $_v);
                    $domStops->appendChild($st);
                }
                $item->appendChild($domStops);
            }
            elseif (is_array($val)) {
                // Generic array values, dont include sea
                $col = $dom->createElement($key);
                foreach ($val as $k => $v) {
                    if (is_array($v)) {
                        $v = implode(";", $v);
                    }
                    $tt = $dom->createElement("item");
                    $tt->appendChild($dom->createTextNode($v));
                    $col->appendChild($tt);
                }
                $item->appendChild($col);
            }
            else
                addAttr($dom, $item, $key, $val);
        }
        // Append departures
        $deps = $db->departures->find(array("route" => $stop['_id']));
        $dd = $dom->createElement("departures");
        foreach ($deps as $d) {
            $_d = $dom->createElement("dep");
            addAttr($dom, $_d, "time", str_pad($d['time'], 4, "0", STR_PAD_LEFT));
            addAttr($dom, $_d, "days", implode(";", $d['days']));
            $dd->appendChild($_d);
        }
        $item->appendChild($dd);
        addAttr($dom, $root, "count", $stops->count());
        $root->appendChild($item);
    }
    $dom->save($exportTo);
}
function exportStops($db) {
    $stops = $db->stops->find()->sort(array("name" => 1));

    $exportTo = "exports/stops_" . date("m_d_H_i") . ".xml";
    $exportTo = "exports/stops.xml";
    $dom = new DOMDocument("1.0");
    $dom->formatOutput = true;

    // create root element
    $root = $dom->createElement("stops");
    $dom->appendChild($root);



    // save and display tree
    foreach ($stops as $stop) {
        $item = $dom->createElement("stop");
        foreach ($stop as $key => $val) {
            if ($key == "search")
                continue;
            elseif ($key == "location" && is_array($val))
                addAttr($dom, $item, $key, implode(",", $val));
            elseif (is_array($val)) {
                // Generic array values, dont include sea
                $col = $dom->createElement($key);
                foreach ($val as $v) {
                    $tt = $dom->createElement("item");
                    $tt->appendChild($dom->createTextNode($v));
                    $col->appendChild($tt);
                }
                $item->appendChild($col);
            }
            else
                addAttr($dom, $item, $key, $val);
        }
        addAttr($dom, $root, "count", $stops->count());
        $root->appendChild($item);
    }
    $dom->save($exportTo);
}

// Export bus stops
$db = Config::getDb();
if (count($argv) > 1) {
    if ($argv[1] == "stops")
        exportStops($db);
    elseif ($argv[1] == "routes")
        exportRoutes($db);
}
else {
    echo "Must state what to export: stops, routes\n";
}
