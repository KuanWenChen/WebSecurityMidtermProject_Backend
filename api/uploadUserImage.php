<?php
require_once('util.php');
require_once('database.php');
try {
    if(!isset($_REQUEST["token"]) || $_REQUEST["token"] == "") {
        $error = "Please login";
        echo res(401, $error);
        throw new Exception($error);
    }
    if( !strpos(",image/jpeg,image/gif,image/png", $_FILES["file"]["type"])) {
        $error = "Only png, jpge, gif";
        echo res(400, $error);
        throw new Exception($error);
    }
    if($_FILES["file"]["size"] > 5120) {
        $error = "Fill too big! Please small than 5MB";
        echo res(400, $error);
        throw new Exception($error);
    }
    $user = database_getUser_byToken($_REQUEST["token"]);
    if($user) {
        database_kill_token($_REQUEST["token"]);
        if(move_uploaded_file($_FILES["file"]["tmp_name"], "user_images/".stringEncode($user["username"]))) {
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

// $ans["request"] = $_REQUEST;
// $ans["file"] = $_FILES;
// echo res(200, $ans);
// echo res(200, exec('whoami'));
?>