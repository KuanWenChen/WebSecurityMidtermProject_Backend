<?php
define('DB_SERVER', 'db');
define('DB_USERNAME', 'SwordGunBlue');
define('DB_PASSWORD', 'MYSQL-SwordGunBlue');
define('DB_NAME', 'myDb');

require_once("util.php");
date_default_timezone_set('Asia/Taipei');
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($link == false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

function database_connect() {
    return $GLOBALS["link"];
}

function close_database_connect() {
    mysqli_close(database_connect());
}

function database_getUser($username, $select = ["id", "username", "user_level"]) {
    $username = stringEncode($username);
    $selected_name = string_array_concat($select, ", ");

    $sql = "SELECT $selected_name FROM `users` WHERE `username`='$username' LIMIT 1;";
    $query = mysqli_query(database_connect(), $sql);
    try {
        $data = mysqli_fetch_assoc($query);
        if($data) {
            $data["username"] = stringDecode($data["username"]);
            return $data;
        } else {
            return;
        }        
    } catch (Exception $e) {
        return;
    }
    return;
}

function database_register($username, $password) {
    $username = stringEncode($username);
    $password = hash("sha256", $password);

    $sql = "INSERT INTO `users` (username, password) VALUES ('$username', '$password');";
    if (mysqli_query(database_connect(), $sql)) {
        return;
    } else {
        return "Error: " . $sql . "<br>" . mysqli_error(database_connect());
    }
    return;
}

function database_setCookie($username) {
    $username = stringEncode($username);

    $token = token_generate($username, 1800);
    $str_token = $token["token"];
    $deadtime = time2text($token["deadtime"]);
    $sql = "UPDATE `users` SET cookie='$str_token', cookie_time='$deadtime' WHERE username='$username';";
    if (mysqli_query(database_connect(), $sql)) {
        return;
    } else {
        return "Error: " . $sql . "<br>" . mysqli_error(database_connect());
    }
}

function database_kill_cookie($cookie) {
    $cookie = string_filter($cookie, STRING_FILTER_HEX);

    $newToken = token_generate("kill", -1800);
    $str_token = $newToken["token"];
    $deadtime = time2text(time()-36000);
    $sql = "UPDATE `users` SET cookie='$str_token', cookie_time='$deadtime' WHERE cookie='$cookie';";
    if (mysqli_query(database_connect(), $sql)) {
        return;
    } else {
        return "Error: " . $sql . "<br>" . mysqli_error(database_connect());
    }
}

function database_setToken($username) {
    $username = stringEncode($username);

    $token = token_generate($username, 30);
    $str_token = $token["token"];
    $deadtime = time2text($token["deadtime"]);
    $sql = "UPDATE `users` SET token='$str_token', token_time='$deadtime' WHERE username='$username';";
    if (mysqli_query(database_connect(), $sql)) {
        return;
    } else {
        return "Error: " . $sql . "<br>" . mysqli_error(database_connect());
    }
}

function database_kill_token($token) {
    $token = string_filter($token, STRING_FILTER_HEX);

    $newToken = token_generate("kill", -1800);
    $str_token = $newToken["token"];
    $deadtime = time2text(time()-36000);
    $sql = "UPDATE `users` SET token='$str_token', token_time='$deadtime' WHERE token='$token';";
    if (mysqli_query(database_connect(), $sql)) {
        return;
    } else {
        return "Error: " . $sql . "<br>" . mysqli_error(database_connect());
    }
}

function database_login($username, $password) {
    $username = stringEncode($username);
    $password = hash("sha256", $password);

    $sql = "SELECT id, username, user_level, cookie, cookie_time FROM `users` WHERE username='$username' AND password='$password' LIMIT 1;";
    $query = mysqli_query(database_connect(), $sql);
    try {
        $data = mysqli_fetch_assoc($query);
        if($data) {
            //Set new cookie
            $setCookie = database_setCookie(stringDecode($username));
            if($setCookie) {
                return $setCookie;
            };
            //refetch
            $query = mysqli_query(database_connect(), $sql);
            $data = mysqli_fetch_assoc($query);

            //Decode
            $data["username"] = stringDecode($data["username"]);
            return $data;
        } else {
            return;
        }
    } catch (Exception $e) {
        return;
    }
    return;
}

function database_getUser_byCookie($user_cookie, $select = ["id", "username", "user_level", "cookie_time"]) {
    $user_cookie = string_filter($user_cookie, STRING_FILTER_HEX);
    $selected_name = string_array_concat($select, ", ");

    $sql = "SELECT $selected_name FROM `users` WHERE cookie='$user_cookie' LIMIT 1;";
    $query = mysqli_query(database_connect(), $sql);
    try {
        $data = mysqli_fetch_assoc($query);
        if(time() >= strtotime($data["cookie_time"])) {
            //cookie is dead;
            return;
        } else {
            //decode username
            $data["username"] = stringDecode($data["username"]);
            return $data;
        }
    } catch (Exception $e) {
        return;
    }
    return;
}

function database_getUser_byToken($user_token) {
    $user_token = string_filter($user_token, STRING_FILTER_HEX);

    $sql = "SELECT id, username, user_level, token_time FROM `users` WHERE token='$user_token' LIMIT 1;";
    $query = mysqli_query(database_connect(), $sql);
    try {
        $data = mysqli_fetch_assoc($query);
        if(time() >= strtotime($data["token_time"])) {
            //token is dead;
            return;
        } else {
            //decode username
            $data["username"] = stringDecode($data["username"]);
            return $data;
        }
    } catch (Exception $e) {
        return;
    }
    return;
}

function database_getComments($id) {
    $id = string_filter((string)$id, STRING_FILTER_DEC);

    try {
        if($id == "") {
            // fetch all
            $sql = "SELECT * FROM `comments` ORDER BY id ASC;";
            $query = mysqli_query(database_connect(), $sql);
            $data = mysqli_fetch_all($query, MYSQLI_ASSOC);
            //decode
            for ($i = 0; $i < count($data); $i++) {
                $data[$i]["publisher"] = stringDecode($data[$i]["publisher"]);
                $data[$i]["content"] = stringDecode($data[$i]["content"]);
            }
            return $data;
       } else {
            $sql = "SELECT * FROM `comments` WHERE id = $id LIMIT 1;";
            $query = mysqli_query(database_connect(), $sql);
            $data = mysqli_fetch_assoc($query);
            
            //decode
            if($data) {
                $data["publisher"] = stringDecode($data["publisher"]);
                $data["content"] = stringDecode($data["content"]);
                return $data;
            } else {
                return;
            }
            
        }
    } catch (Exception $e) {
        return $e;
    }
    return;
}

function database_publishComments($publisher, $content) {
    $publisher = stringEncode($publisher);
    $content = stringEncode($content);
    $publish_time = time2text(time());

    $sql = "INSERT INTO `comments` (publisher, content, publish_time) VALUES ('$publisher', '$content', '$publish_time');";
    if (mysqli_query(database_connect(), $sql)) {
        return;
    } else {
        return "Error: " . $sql . "<br>" . mysqli_error(database_connect());
    }
    return;
}

function database_deleteComments($publisher, $id) {
    $publisher = stringEncode($publisher);
    $id = string_filter($id, STRING_FILTER_DEC);

    $sql = "SELECT * FROM `comments` WHERE id=$id;";
    $query = mysqli_query(database_connect(), $sql);
    $comment = mysqli_fetch_assoc($query);

    $sql = "DELETE FROM `comments` WHERE id=$id AND publisher='$publisher'";
    if (mysqli_query(database_connect(), $sql)) {
        unlink("comment_files/".$comment["file_name"]);
        return;
    } else {
        return "Error: " . $sql . "<br>" . mysqli_error(database_connect());
    }
    return;
}

function database_updateComment($publisher, $id, $content) {
    $publisher = stringEncode($publisher);
    $id = string_filter($id, STRING_FILTER_DEC);
    $content = stringEncode($content);
    $publish_time = time2text(time());

    $sql = "UPDATE `comments` SET content='$content', publish_time='$publish_time' WHERE id=$id AND publisher='$publisher'";
    if (mysqli_query(database_connect(), $sql)) {
        return;
    } else {
        return "Error: " . $sql . "<br>" . mysqli_error(database_connect());
    }
    return;
}

function database_updateCommentFileName($id) {
    $id = string_filter($id, STRING_FILTER_DEC);
    $filename = stringEncode($id);

    $sql = "UPDATE `comments` SET file_name='$filename' WHERE id=$id;";
    if (mysqli_query(database_connect(), $sql)) {
        return;
    } else {
        return "Error: " . $sql . "<br>" . mysqli_error(database_connect());
    }
    return;
}

function database_getTitle() {
    $sql = "SELECT title FROM `title` LIMIT 1;";
    $query = mysqli_query(database_connect(), $sql);
    try {
        $data = mysqli_fetch_assoc($query);
        if($data) {
            $data["title"] = stringDecode($data["title"]);
            return $data;
        } else {
            return;
        }
    } catch (Exception $e) {
        return;
    }
}

function database_updateTitle($username, $user_level, $title) {
    $username = stringEncode($username);
    $user_level = (int) string_filter($id, STRING_FILTER_DEC);
    $title = stringEncode($title);

    if($user_level == 0) {
        $sql = "UPDATE `title` SET title='$title';";
        if (mysqli_query(database_connect(), $sql)) {
            return;
        } else {
            return "Error: " . $sql . "<br>" . mysqli_error(database_connect());
        }
    } else {
        return "user is not admin";
    }
}

?>