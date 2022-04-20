<?php
require_once('util.php');
require_once('database.php');

try {
    $cookie = isset($_COOKIE['login']) ? $_COOKIE['login'] : $_GET['cookie'];
    if (!isset($cookie) || $cookie == "" || $cookie == null) {
        $error = "Please login";
        echo res(401, $error);
        throw new Exception($error);
    }
    
    $user_cookie = $cookie;
    $user = database_getUser_byCookie($user_cookie);
    if($user) {
        echo res(200, $user);
    } else {
        $error = "cookie timeout";
        echo res(401, $error);
        throw new Exception($error);
    }
} catch(Exception $e) {
}
close_database_connect();

?>