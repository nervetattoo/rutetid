<?php
/**
 * A basic module to extend for hiding authed information
 *
 * @author Raymond Julin 
 */

class AetherModuleAuthed extends AetherModule {
    private $user = null;

    public function run() {
        $selfIsRunner = (get_class($this) === "AetherModuleAuthed");
        if (!$selfIsRunner && $this->auth() === true) {
            $user = $this->getUser();
            // Allow one type of rendering for admins vs regular users.
            // Might be handy yes-oh
            if ($user['isAdmin'] === true)
                return $this->runAdmin();
            else
                return $this->runAuthed();
        }
        elseif ($selfIsRunner && $this->auth() == false) {
            /**
             * This code gets run only if user is not logged in
             * is triggered from some other module inheriting
             * the authed state module, and its not that module
             * it self that is calling the run()
             * This is to ensure not all X modules inheriting
             * AetherModuleAuthed renders a login form, which
             * would look dead silly
             */
            if (isset($_POST['username']) && isset($_POST['password'])) {
                $location = "/admin";
                if (($errCode = $this->logIn($_POST)) != 1) {
                    // Redirect
                    $location .= "?err=" . $errCode;
                }
                header("Location: $location");
            }
            $tpl = $this->sl->getTemplate();
            return $tpl->fetch('login.tpl');
        }
        return "";
    }

    public function runAdmin() {
        return "";
    }

    public function runAuthed() {
        return "";
    }

    protected function auth() {
        if (isset($_SESSION['userId'])) {
            $userId = $_SESSION['userId'];
            $user = User::byId($userId);
            $this->user = $user;
            if ($user)
                return true;
        }
        return false;
    }

    protected function getUser() {
        if ($this->user !== null)
            return $this->user;
        else
            return false;
    }
    
    /**
     * Attempt to authenticate the user and store session info
     *
     * @return bool
     * @param array $data
     */
    protected function logIn($data) {
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
}
