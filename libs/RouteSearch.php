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
        $tmp = $db->routes->find();
        $buses = array();
        while ($bus = $tmp->getNext())
            $buses[] = $bus['num'];
        return array_unique(array_filter($buses));
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
        $start = toLower($from);
        $end = toLower($to);
        $buses = $db->routes->find(array(
            'search' => array(
                '$all' => array($start, $end)
            )
        ));

        // Find some necessary data
        if (!$time)
            $time = date("Hi");//"0700";
        if (!$weekday)
            $weekday = (int)date("w");
        if ($weekday == 0) // sunday, fix it
            $weekday = 7;
        $minutes = (int)substr($time, -2);
        $hours = (int)substr($time, 0, -2);

        $searchFilters = array();
        $hitCount = 0;
        $timeSort = array();
        while ($route = $buses->getNext()) {
            // Iterate over stops and find the 
            $startStop = null;
            $endStop = null;
            foreach ($route['stops'] as $stop) {
                if (!$startStop && toLower($stop['name']) == $start)
                    $startStop = $stop;
                elseif ($startStop && !$endStop && toLower($stop['name']) == $end)
                    $endStop = $stop;
            }
            if ($startStop && $endStop) {
                /**
                 * By now we have tested that we have both stops
                 * and in the correct order for this route
                 * Its time to generate the query to find departures
                 */
                $minuteMark = ($minutes - (int)$startStop['timeOffset']);
                $latestStartTime = date("Hi", mktime($hours, $minuteMark));
                $startOffset = (int)$startStop['timeOffset'];
                $endOffset = (int)$endStop['timeOffset'];
                $runningTime = $endOffset - $startOffset;

                $departuresForRoute = $db->departures->find(array(
                    'route' => $route['_id'],
                    'time' => array('$gte' => (int)$latestStartTime),
                    'days' => $weekday
                ))->sort(array('time'=>1))->limit(15);
                /**
                 * Loop over each departure and calculate some times
                 */
                while ($dep = $departuresForRoute->getNext()) {
                    $arrivalTime = (int)$dep['time'];
                    $startTime = Config::timeAdd($arrivalTime, $startOffset);
                    $startM = substr($startTime, -2);
                    $startH = substr($startTime, 0, -2);
                    $waitTime = ($startH - $hours) * 60 + 
                        ($startM - $minutes);
                    $arrivalTime = Config::timeAdd(str_replace(":","",$startTime), $runningTime, "H:i");

                    while ($waitTime < 0)
                        $waitTime += 3600;
                    $timeSort[] = $waitTime;
                    // Wait time should be arrival time minus specified search time
                    $hits[] = array(
                        'id' => $route['num'],
                        'name' => $route['dest'],
                        'runningTime' => $runningTime,
                        'startTime' => $startTime,
                        'wait' => $waitTime,
                        'arrivalTime' => $arrivalTime,
                        'arrivalSpan' => ($runningTime + $waitTime)
                    );
                }
            }
        }
        array_multisort($timeSort, SORT_ASC, $hits);
        return array_slice($hits, $offset, $limit);
    }
}
