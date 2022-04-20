<?php
require_once('util.php');
require_once('database.php');

try {
    if(!isset($_POST["token"]) || $_POST["token"] == "") {
        $error = "Please login";
        echo res(401, $error);
        throw new Exception($error);
    }
    if( strlen($_POST["content"]) > 2000) {
        $error = "content too long";
        echo res(400, $error);
        throw new Exception($error);
    }
    $user = database_getUser_byToken($_POST["token"]);
    if($user) {
        $pub = database_publishComments($user["username"], $_POST["content"]);
        if($pub) {
            echo res(500, $pub);
        } else {
            database_kill_token($_POST["token"]);
            echo res(201, "Publish succeed");
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