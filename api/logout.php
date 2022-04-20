<?php
require_once('util.php');
require_once('database.php');

try {
    $cookie = $_COOKIE['login'];
    if (!isset($cookie) || $cookie == "") {
        $error["msg"] = "no cookie";
        $error["cookie"] = $cookie;
        echo res(400, $error);
        throw new Exception($error["msg"]);
    }
    
    $kill_cookie = database_kill_cookie($cookie);
    if($kill_cookie) {
        echo res(500, "something wrong");
    } else {
        setcookie("login", null, -1);
        echo res(200, "logout");
    }
} catch(Exception $e) {
}
close_database_connect();

?>