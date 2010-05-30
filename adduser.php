<?php
require_once("libs/Config.php");
require_once("libs/User.php");

$db = Config::getDb();
$username = $argv[1];
$password = $argv[2];
$isAdmin = true;

if (User::byUsername($username) === null) {
    $user = User::create($username, $password, false, $isAdmin);
    if ($user) {
        echo "Created {$user['username']}\n";
        exit;
    }
}
echo "User exists or stuff fucked upz\n";
