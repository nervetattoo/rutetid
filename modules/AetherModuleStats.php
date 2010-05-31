<?php
class AetherModuleStats extends AetherModuleHeader {
    public function run() {
        return "No run defined";
    }
    public function service($name) {
        if ($name == "searches") {
            // Find last 30 days
            $time = mktime(0, 0, 0) - (3600 * 24 * 30);
            $tplLogs = array();
            $db = Config::getDb();
            $logs = $db->log->find(array(
                    'time' => array('$gte' => $time)
                ))->sort(array(
                    'time' => -1
                ));
            $days = array(
                1 => 'Mandag',
                2 => 'Tirsdag',
                3 => 'Onsdag',
                4 => 'Torsdag',
                5 => 'Fredag',
                6 => 'Lørdag',
                7 => 'Søndag',
            );
            // One row pr distance
            $distribution = array();
            foreach ($logs as $l) {
                $weekday = date("w", $l['time']);
                if ($weekday == 0)
                    $weekday = 7;
                $day = $days[$weekday];
                $to = $l['to'];
                $from = $l['from'];
                $cur = array(
                    'day' => $day,
                    'from' => $from,
                    'to' => $to,
                    'timeUsed' => $l['timeused'],
                    'time' => (int)$l['time'],
                    'hits' => (int)$l['hits'],
                );
                $date = date("Y-m-d", $l['time']);
                if (!array_key_exists($date, $tplLogs)) {
                    $tplLogs[$date] = array(
                        'hits'=>0,
                        'noHits'=>0,
                        'day'=>$day
                    );
                }
                else {
                    if ($l['hits'] > 0)
                        $tplLogs[$date]['hits']++;
                    else
                        $tplLogs[$date]['noHits']++;
                }
            }
            if (isset($_GET['callback']))
                return new AetherJSONPResponse($tplLogs, $_GET['callback']);
            else
                return new AetherJSONResponse($tplLogs);
        }
    }
}
