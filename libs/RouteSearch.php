<?php

/**
 * Do route searches
 * @author Raymond Julin <raymond.julin@gmail.com>
 */

class RouteSearch {

    /**
     * Return number of active nus routes (numbers)
     *
     * @return int
     */
    public function getActiveBusNumbers() {
        $db = Config::getDb();
        $tmp = $db->buses->find();
        $buses = array();
        while ($bus = $tmp->getNext())
            $buses[] = $bus['id'];
        return array_filter($buses);
    }

    
    /**
     * Return number of departures in system
     *
     * @return int
     */
    public function getDepartureCount() {
        $db = Config::getDb();
        return $db->departures->find()->count();
    }

    /**
     * Search for routes going from from to to
     * @return array
     * @param string $from
     * @param string $to
     * @param mixed $time
     */
    public function search($from, $to, $time=false, $weekday=false, $limit=5, $offset=0) {
        $hits = array();
        /**
         * First find all buses having both stops in their path, kinda nice
         */
        $db = Config::getDb();
        $buses = $db->buses->find(array(
            'search.stops' => array(
                '$all' => array($from, $to)
            )
        ));

        // Iterate over each bus
        if (!$time)
            $time = date("Hi");//"0700";
        if (!$weekday)
            $weekday = (int)date("w");
        if ($weekday == 0) // sunday, fix it
            $weekday = 7;
        //list($timeHour, $timeMinte) = explode(":", $time);
        $time = str_replace(":", "", $time);
        $timeSort = array();
        while ($bus = $buses->getNext()) {
            // Find what route to use
            foreach ($bus['routes'] as $route) {
                // Verify that stop is after start in this route
                $status = 0;
                $runningTime = 0; // Running time of bus
                $wait = 0;
                $stops = 0;
                foreach ($route['stops'] as $key => $stop) {
                    if ($status == 0) {
                        $wait += $stop['time'];
                    }
                    if ($stop['name'] == $from && $status == 0) {
                        // Start accumulating time
                        $runningTime += $stop['time'];
                        $status = 1;
                    }
                    elseif ($stop['name'] == $to && $status == 1) {
                        $stops++;
                        $runningTime += $stop['time'];
                        $status = 2;
                        break;
                    }
                }
                if ($status == 2) {
                    /**
                     * Find a departure to match
                     */
                    $now = date("Hi");//"0700";
                    $startTime = $time - $wait;
                    $departures = $db->departures->find(array(
                        'route' => $bus['id'],
                        'days' => array(
                            '$all' => array((int)$weekday)
                        ),
                        'time' => array(
                            '$gt' => (int)$time - $wait
                        )
                    ))->sort(array('time' => 1))->skip(0);

                    while ($dep = $departures->getNext()) {
                        $departMinute = (int)substr((string)$dep['time'], -2);
                        $departHour = (int)substr((string)$dep['time'], 0, -2);
                        $nowHour = (int)date("H");
                        $startTime = date("H:i", mktime($departHour, $departMinute + $wait));
                        $arrivalTime = date("H:i", mktime($departHour, $departMinute + $wait + $runningTime));
                        $waitTime = (
                            (((int)$departHour - (int)$nowHour) * 60) +
                            ($departMinute + $wait) - date("i")
                        );
                        $hits[] = array(
                            'id' => $bus['id'],
                            'name' => $route['name'],
                            'runningTime' => $runningTime,
                            'startTime' => $startTime,
                            'arrivalTime' => $arrivalTime,
                            'arrivalSpan' => $runningTime + $waitTime,
                            'wait' => $waitTime,
                            'stops' => $stops
                        );
                        $timeSort[] = $waitTime;
                    }
                }
            }
        }
        array_multisort($timeSort, SORT_ASC, $hits);
        return array_slice($hits, $offset, $limit);
    }
}
