<?php
class AetherModuleAdmin extends AetherModuleHeader {
    public function run() {
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $location = "/admin";
            if (($errCode = $this->auth($_POST)) != 1) {
                // Redirect
                $location .= "?err=" . $errCode;
            }
            header("Location: $location");
        }
        $tpl = $this->sl->getTemplate();
        $status = $this->userStat();
        if ($status == 1) {
            $time = mktime(0, 0, 0) - (3600 * 24 * 7);
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
                $to = $l['to'];
                $from = $l['from'];
                $cur = array(
                    'from' => $from,
                    'to' => $to,
                    'timeUsed' => $l['timeused'],
                    'time' => (int)$l['time'],
                    'hits' => (int)$l['hits'],
                );
                $dist = md5($from . " - " . $to);
                if (!array_key_exists($dist, $distribution)) {
                    $distribution[$dist] = array(
                        'from' => $from,
                        'to' => $to,
                        'searches' => 0, 
                        'totalHits' => 0,
                        'withHits' => 0,
                        'noHits' => 0,
                        'timeSpent' => 0
                    );
                }
                if ($l['hits'] > 0)
                    $distribution[$dist]['withHits']++;
                else
                    $distribution[$dist]['noHits']++;
                $distribution[$dist]['totalHits'] += $l['hits'];
                $distribution[$dist]['timeSpent'] += $l['timeused'];
                $distribution[$dist]['searches']++;
                $tplLogs[$weekday][] = $cur;
            }
            $tpl->set("days", $days);
            $tpl->set("distribution", $distribution);
            $tpl->set("searches", $tplLogs);
        }

        $tpl->set("status", $status);
        return $tpl->fetch('admin.tpl');
    }
    
    /**
     * Attempt to authenticate the user and store session info
     *
     * @return bool
     * @param array $data
     */
    private function auth($data) {
        $username = $data['username'];
        $password = $data['password'];
        $user = User::byUsername($username);
        if ($user) {
            if (User::auth($user, $password)) {
                $_SESSION['userId'] = $user['_id'];
                return 1;
            }
            else {
                unset($_SESSION['userId']);
                return 0;
            }
        }
        else {
            return -1;
        }
    }

    private function userStat() {
        if (isset($_SESSION['userId'])) {
            $userId = $_SESSION['userId'];
            $user = User::byId($userId);
            $this->user = $user;
            if ($user && $user['isAdmin'] == true)
                return 1;
            elseif ($user)
                return 0;
            else
                return -1;
        }
        else
            return -1; // Not authed
    }
}
