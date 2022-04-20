<?php
require_once('util.php');

try {
    $filename = "";
    if(!isset($_GET["username"]) || $_GET["username"] == "") {
        $filename = "default_icon";
    } else {
        $filename = stringEncode($_GET["username"]);
    }

    if(strlen($filename) > 30) {
        echo res(202, "user_images/default_icon");
        throw new Exception("filename too long");
    }
    $path = "user_images/$filename";
    if(file_exists($path)) {
        echo res(200, $path);    
    } else {
        echo res(202, "user_images/default_icon");
    }
} catch(Exception $e) {
}
?>