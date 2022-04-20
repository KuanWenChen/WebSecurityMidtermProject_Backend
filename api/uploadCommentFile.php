<?php
require_once('util.php');
require_once('database.php');
try {
    if(!isset($_REQUEST["token"]) || $_REQUEST["token"] == "") {
        $error = "Please login";
        echo res(401, $error);
        throw new Exception($error);
    }
    if(!isset($_REQUEST["id"]) || $_REQUEST["id"] == "") {
        $error = "Comment no id";
        echo res(404, $error);
        throw new Exception($error);
    }
    if($_FILES["file"]["size"] > 5120) {
        $error = "Fill too big! Please small than 5MB";
        echo res(400, $error);
        throw new Exception($error);
    }
    $comment = database_getComments($_REQUEST["id"]);
    if(!$comment) {
        $error = "Comment not found";
        echo res(404, $error);
        throw new Exception($error);
    }

    $user = database_getUser_byToken($_REQUEST["token"]);
    if($user) {
        if($user["username"] != $comment["publisher"]) {
            $error = "You are not owner.";
            echo res(400, $error);
            throw new Exception($error);
        }

        database_kill_token($_REQUEST["token"]);
        if(move_uploaded_file($_FILES["file"]["tmp_name"], "comment_files/".stringEncode($comment["id"]))) {
            if(database_updateCommentFileName($comment["id"])) {
                echo res(500, "Something wrong");
            } else {
                echo res(200, "Upload image success!");
            }  
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