<?php
class AetherModuleStops extends AetherModuleHeader {
    public function run() {
        // List all stops
        $config = $this->sl->get('aetherConfig');
        $tpl = $this->sl->getTemplate();
        $routeSearch = new RouteSearch;
        if ($config->hasUrlVar("stopId") && $config->getUrlVar("stopId") != "")
            $stopId = $config->getUrlVar("stopId");
        elseif (isset($_GET['stopId']) && strlen($_GET['stopId']) > 0)
            $stopId = $_GET['stopId'];
        else
            $stopId = null;
        $this->sl->set("stopId", $stopId);
        $db = Config::getDb();
        $stops = $db->stops->find(
            array('active' => true)
        )->sort(array("name" => 1));
        $tplStops = array();
        while ($st = $stops->getNext()) {
            $id = (string)$st['_id'];
            $tplStops[] = array(
                'id' => $id,
                'name' => $st['name'],
                'selected' => ($id == $stopId) ? true : false
            );
        }
        $tpl->set('stops', $tplStops);
        return $tpl->fetch('stops.tpl');
    }

    public function service($name) {
        $start = AetherTimer::getMicroTime();
        $memcache = new Memcache;
        $memcache->connect('localhost', 11211);
        $db = Config::getDb();
        $routeSearch = new RouteSearch;

        $cacheKey = "stops_";
        $filters = array();
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

        $cacheKey .= r_implode(":", $filters);
        $result = $memcache->get($cacheKey);
        if ($result)
            $result = unserialize($result);
        else {
            // Find stops near me!!
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
        $time = AetherTimer::getMicroTime() - $start;
        $result['time'] = $time;
        if (isset($_GET['callback']))
            return new AetherJSONPResponse($result, $_GET['callback']);
        else
            return new AetherJSONResponse($result);
    }
}
