<?php
require_once('util.php');
require_once('database.php');
try {
    if( !isset($_POST['username']) || !isset($_POST['password']) || $_POST['username'] == "" || $_POST['password'] == "" ) {
        $error["msg"] = "Account or Password can't be empty";
        $error["username"] = $_POST['username'];
        $error['password'] = $_POST['password'];
        echo res(400, $error);
        throw new Exception($error["msg"]);
    }

    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if(   !(strlen($username) >= 1 && 
            strlen($username) <= 12 && 
            strlen($password) >= 6 && 
            strlen($password) <= 99)
        ) {
        $error = "Account or Password length wrong";
        echo res(400, $error);
        throw new Exception($error);
    }
    $getuser = database_getUser($username);
    if($getuser) {
        $error = "Account existed.";
        // $error["msg"] = "Account existed.";
        // $error["user"] = database_getUser($username);
        echo res(403, $error);
    } else {
        $reg = database_register($username, $password);
        if( !$reg ) {
            echo res(201, "Register succeed.");
        } else {
            echo res(403, "Unknow error.");
        }
    }
} catch(Exception $e) {
}
close_database_connect();


?>