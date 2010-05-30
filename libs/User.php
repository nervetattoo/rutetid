<?php
$db = Config::getDb();
$db->users->ensureIndex(array("username"=>1), array("unique" => true));

class User {
    public static function byUsername($username) {
        $db = Config::getDb();
        $user = $db->users->findOne(array(
            'username' => $username
        ));
        return $user;
    }

    public static function byId($id) {
        $db = Config::getDb();
        $user = $db->users->findOne(array(
            '_id' => new MongoId($id)
        ));
        return $user;
    }

    public static function auth($user, $authAgainst) {
        return ($user['password'] === md5($authAgainst));
    }

    public static function create($username, $password, $email, $isAdmin) {
        $db = Config::getDb();
        // Check not exists
        $user = array(
            'username' => $username,
            'password' => md5($password),
            'email' => $email,
            'isAdmin' => $isAdmin
        );
        if ($db->users->insert($user))
            return $user;
        return false;
    }
}
