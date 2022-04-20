<?php
require_once('util.php');
require_once('database.php');

try {
    $id = $_GET["id"];
    $data = database_getComments($id);

    if($data) {
        echo res(200, $data);
    } else {
        echo res(404, "comments not exist");
    }
} catch(Exception $e) {
}
close_database_connect();
?>