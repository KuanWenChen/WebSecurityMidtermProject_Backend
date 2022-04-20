<?php
require_once('util.php');
require_once('database.php');
try {
    if(!isset($_POST["token"]) || $_POST["token"] == "") {
        $error = "Please login";
        echo res(401, $error);
        throw new Exception($error);
    }
    if(!isset($_POST["url"]) || $_POST["url"] == "") {
        $error = "Please input url";
        echo res(400, $error);
        throw new Exception($error);
    }

    $file_info = get_remote_file_info($_POST["url"]);
    if(!$file_info["exist"]) {
        $error = "get file failed.";
        echo res(400, $error);
        throw new Exception($error);
    }
    if(strpos(",image/jpeg,image/gif,image/png", $file_info["type"]) == false) {
        $error = "Only png, jpge, gif";
        echo res(400, $error);
        throw new Exception($error);
    }
    if($file_info["size"] > 5120) {
        $error = "Fill too big! Please small than 5MB";
        echo res(400, $error);
        throw new Exception($error);
    }

    $user = database_getUser_byToken($_POST["token"]);
    if($user) {
        database_kill_token($_POST["token"]);
        $path = "user_images/".stringEncode($user["username"]);
        if( file_put_contents($path, fopen($_POST["url"], 'r')) ) {
            echo res(200, "Upload image success!");
        } else {
            echo res(500, "something wrong");
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