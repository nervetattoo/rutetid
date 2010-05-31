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

            // Add jqplot style and js
            $this->sl->getVector('styles')
                ->append("/js/jqplot/jquery.jqplot.css");
            $this->sl->getVector('javascripts')
                ->append("/js/jqplot/jquery.jqplot.min.js");
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
