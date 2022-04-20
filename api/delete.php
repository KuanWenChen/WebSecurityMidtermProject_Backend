<?php
require_once('util.php');
require_once('database.php');

try {
    if(!isset($_POST["token"]) || $_POST["token"] == "") {
        $error = "Please login";
        echo res(401, $error);
        throw new Exception($error);
    }

    $user = database_getUser_byToken($_POST["token"]);
    if($user) {
        $delete = database_deleteComments($user["username"], $_POST["id"]);
        if($delete) {
            echo res(500, $delete);
        } else {
            database_kill_token($_POST["token"]);
            echo res(200, "Delete succeed");
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