<?php
require_once('util.php');
require_once('database.php');

try {
    if (!isset($_COOKIE["login"]) || $_COOKIE["login"] == "") {
        $error = "Please login";
        echo res(401, $error);
        throw new Exception($error);
    }
    
    $user_cookie = $_COOKIE["login"];
    $user = database_getUser_byCookie($user_cookie);
    if($user) {
        $setToken = database_setToken($user["username"]);
        if($setToken) {
            // $error["msg"] = "something wrong.";
            // $error["setToken"] = $setToken;
            $error = "something wrong.";
            echo res(500, $error);
            throw new Exception($error);
        };

        //Set token succeed
        $user = database_getUser($user["username"], ["id", "username", "user_level", "token", "token_time"]);
        if($user) {
            echo res(201, $user);
        } else {
            echo res(500, "something wrong.");
        }
    } else {
        $error = "cookie timeout";
        echo res(401, $error);
        throw new Exception($error);
    }
} catch(Exception $e) {
}
close_database_connect();

?>