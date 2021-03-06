<?php
class AetherModuleRuteSearch extends AetherModuleHeader {
    public function run() {
        $tpl = $this->sl->getTemplate();
        if ($this->sl->hasObject('timer')) {
            $timer = $this->sl->get('timer');
            $timer->start('search');
        }
        else
            $timer = false;
        $config = $this->sl->get('aetherConfig');
        $search = new RouteSearch;
        if ($timer)
            $timer->tick('search', 'Create search object');
        $db = Config::getDb();
        if ($config->hasUrlVar("from") && $config->hasUrlVar("to")) {
            $from = urldecode($config->getUrlVar('from'));
            $to = urldecode($config->getUrlVar('to'));
            if (strlen($from) > 0 && strlen($to) > 0) {
                if ($from == $to)
                    $tpl->set('easteregg', "samestop");
                else {
                    $hits = $this->search($search, $from, $to, $timer);
                    if ($timer)
                        $timer->tick('search', 'Search done');
                    $tpl->set('routes', $hits);
                    $tpl->set('from', $from);
                    $tpl->set('to', $to);
                }
            }
        }
        $activeRoutes = $search->getActiveBusNumbers();
        sort($activeRoutes);
        $tpl->set('activeRoutes', $activeRoutes);
        $tpl->set('departures', $search->getDepartureCount());
        $tpl->set('import', $db->progress->findOne(
            array('name' => 'import')
        ));
        return $tpl->fetch('search.tpl');
    }
    public function service($name) {
        if ($name == "search") {
            $config = $this->sl->get('aetherConfig');
            $search = new RouteSearch;
            if ($config->hasUrlVar("from") && $config->hasUrlVar("to")) {
                $from = urldecode($config->getUrlVariable('from'));
                $to = urldecode($config->getUrlVariable('to'));
                if (strlen($from) > 0 && strlen($to) > 0) {
                    if ($from !== $to)
                        $hits = $this->search($search, $from, $to);
                }
            }
            $data = $hits;
        }
        if (isset($_GET['callback']))
            return new AetherJSONPResponse($data, $_GET['callback']);
        else
            return new AetherJSONResponse($data);
    }

    private function search($search, $from,$to, $timer=false) {
        if (isset($_GET['time']) && strlen($_GET['time']) > 0)
            $time = $_GET['time'];
        else
            $time = date("H:i");
        if (!isset($_GET['weekday']))
            $weekday = (int)date("w");
        else
            $weekday = (int)$_GET['weekday'];
        if ($weekday == 0) // sunday, fix it
            $weekday = 7;
        $hits = $this->performSearch($search, array(
            'from' => $from, 
            'to' => $to, 
            'time' => $time, 
            'offset' => 0, 
            'limit' => 5,
            'weekday' => $weekday
        ), $timer);
        return $hits;
    }
    private function performSearch($searcher, $data, $timer=false) {
        $from = $data['from'];
        $to = $data['to'];
        $time = $data['time'];
        $offset = $data['offset'];
        $limit = $data['limit'];
        $weekday = $data['weekday'];

        $sTime = AetherTimer::getMicroTime();
        $hits = $searcher->search($from, $to, $time, $weekday, $limit, $offset, $timer);
        $timeUsed = AetherTimer::getMicroTime() - $sTime;
        $db = Config::GetDb();
        $db->log->insert(array(
            'hits' => $searcher->count,
            'from' => $from,
            'to' => $to,
            'time' => time(),
            'date' => date("Y-m-d H:i:s"),
            'timeused' => $timeUsed,
            'ua' => $_SERVER['HTTP_USER_AGENT'],
            'ip' => $_SERVER['REMOTE_ADDR'],
        ));
        return $hits;
    }
}
