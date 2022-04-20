<?php
require_once('util.php');
require_once('database.php');

try {
    $data = database_getTitle();
    if($data) {
        echo res(200, $data);
    } else {
        echo res(404, "Something wrong");
    }
} catch(Exception $e) {
}
close_database_connect();
?>