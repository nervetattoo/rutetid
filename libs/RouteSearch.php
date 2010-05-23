<?php

/**
 * Do route searches
 * @author Raymond Julin <raymond.julin@gmail.com>
 */

class RouteSearch {
    /**
     * Search for routes going from from to to
     * @return array
     * @param string $from
     * @param string $to
     * @param mixed $time
     */
    public function search($from, $to, $time=false) {
        /**
         * First find all buses having both stops in their path, kinda nice
         */
        $db = Config::getDb();
        $buses = $db->buses->find(array(
            'search.stops' => array(
                '$all' => array($from, $to)
            )
        ));
        $hits = array();
        while ($bus = $buses->getNext()) {
            // Find what route to use
            foreach ($bus['routes'] as $route) {
                $name = $route['name'];
                // Verify that stop is after start in this route
                $status = 0;
                $time = 0;
                $wait = 0;
                $stops = 0;
                foreach ($route['stops'] as $key => $stop) {
                    if ($status == 0) {
                        $wait += $stop['time'];
                    }
                    if ($stop['name'] == $from && $status == 0) {
                        // Start accumulating time
                        $time += $stop['time'];
                        $status = 1;
                    }
                    elseif ($stop['name'] == $to && $status == 1) {
                        $stops++;
                        $time += $stop['time'];
                        $status = 2;
                        break;
                    }
                }
                if ($status == 2) {
                    /**
                     * Find a departure to match
                     */
                    $now = "0700";
                    $startTime = $now + $wait;
                    $departures = $db->departures->find(array(
                        'route' => $bus['id'],
                        'time' => array(
                            '$gt' => (int)$startTime
                        )
                    ))->sort(array('time' => 1))->limit(5);
                    while ($dep = $departures->getNext()) {
                        $hits[] = array(
                            'id' => $bus['id'],
                            'name' => $route['name'],
                            'time' => $time,
                            'start' => $dep['time'],
                            'wait' => ((int)$dep['time'] - $now) + $wait,
                            'stops' => $stops
                        );
                        $timeSort[] = $wait;
                    }
                }
            }
        }
        array_multisort($timeSort, SORT_ASC, $hits);
        return $hits;
    }
}
