<?php
require_once('util.php');
require_once('database.php');

try {
    if(!isset($_POST["token"]) || $_POST["token"] == "") {
        $error = "Please login";
        echo res(401, $error);
        throw new Exception($error);
    }
    if( strlen($_POST["title"]) > 30 || strlen($_POST["title"]) < 1) {
        $error = "title length has 1~30";
        echo res(400, $error);
        throw new Exception($error);
    }

    $user = database_getUser_byToken($_POST["token"]);
    if($user) {
        $update = database_updateTitle($user["username"], $user["user_level"], $_POST["title"]);
        if($update) {
            echo res(500, $update);
        } else {
            database_kill_token($_POST["token"]);
            echo res(200, "Set new title");
        }
    } else {
        $error = "Token is dead";
        echo res(401, $error);
        throw new Exception($error); 
    }
} catch(Exception $e) {
}
close_database_connect();
?>