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

    if(database_getUser($username)) {
        $user = database_login($username, $password);
        if($user) {
            setcookie("login", $user["cookie"]);
            echo res(200, $user);
        } else {
            echo res(403, "Account or Password is wrong.");
        }
    } else {
        echo res(403, "Account or Password is wrong.");
    }
} catch(Exception $e) {
}
close_database_connect();

?>