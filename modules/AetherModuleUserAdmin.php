<?php
require_once("AetherModuleAuthed.php");
class AetherModuleUserAdmin extends AetherModuleAuthed {
    public function runAdmin() {
        $tpl = $this->sl->getTemplate();
        $tpl->set("users", User::getAll());
        return $tpl->fetch('userAdmin.tpl');
    }

    public function service($name) {
        if ($name == "edit") {
            $id = $_GET['id'];
            $user = User::byId($id);
            $fields = array("username", "email", "isAdmin");
            foreach ($fields as $key) {
                if (isset($_GET[$key]))
                    $user[$key] = $_GET[$key];
            }
            $ok = User::update($user);
            return new AetherJSONResponse(array("ok"=>$ok,'id'=>$id));
        }
        elseif ($name == "create") {
            // Silly way to generate a password
            if (isset($_GET['username']) && strlen($_GET['username']) > 0) {
                if (isset($_GET['email']) && strlen($_GET['email']) > 0) {
                    $username = $_GET['username'];
                    $email = $_GET['email'];
                    $isAdmin = (isset($_GET['isAdmin']) && $_GET['isAdmin'] == 1) 
                        ? true : false;
                    $password = substr(md5(time().rand(0,10)), 0, 6);
                    $user = User::create($username, $password, $email, $isAdmin);
                    return new AetherJSONResponse(array(
                        "ok" => true,
                        'id' => $user['_id'],
                        'username' => $user['username'],
                        'pass' => $password
                    ));
                }
                else {
                    $ok = false;
                    $msg = "Bad email";
                }
            }
            else {
                $ok = false;
                $msg = "Bad username";
            }
            return new AetherJSONResponse(array(
                "ok"=>$ok,
                'msg'=>$msg
            ));
        }
    }
}
