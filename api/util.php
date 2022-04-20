<?php
define("STRING_FILTER_HEX", "0123456789abcdefABCDEF");
define("STRING_FILTER_DEC", "0123456789");

function stringEncode($str) {
    return bin2hex($str);
}
function stringDecode($strCode) {
    return hex2bin($strCode);
}

function setHeader() {
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }
}

function res($code, $data) {
    // setHeader();
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    // $ans["status"] = $code;
    // $ans["data"] = $data;
    $ans = $data;
    return json_encode($ans);
}

function token_generate($id, $alivetime) {
    $randomnum = rand(1, 10000);
    $timestamp = time();
    $token = hash("sha256", $randomnum.$id.$timestamp);
    $ans['token'] = $token;
    $ans['deadtime'] = time() + $alivetime;
    return $ans;
}

function string_filter($str, $filter) {
    if(!$str || !$filter) {
        return "";
    }
    $newStr = "";
    for($i=0; $i <= strlen($str); $i++) {
        for($j=0; $j <= strlen($filter); $j++) {
            if($str[$i] == $filter[$j]) {
                $newStr = $newStr.$str[$i];
                break;
            }
        }
    }
    return $newStr;
}

function string_array_concat($strarr, $fill) {
    $str = $strarr[0];
    for($i=1; $i < count($strarr); $i++) {
        $str = $str . $fill . $strarr[$i];
    }
    return $str;
}

function time2text($time) {
    return date('Y-m-d H:i:s', $time);
    // return date('d-m-Y H:i:s', $time);
}

function get_remote_file_info($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, TRUE);
    curl_setopt($ch, CURLOPT_NOBODY, TRUE);
    $data = curl_exec($ch);
    $fileType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    $fileSize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    $httpResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    curl_close($ch);
    // return $info;
    return [
        'exist' => (int) $httpResponseCode == 200,
        'type' => (string) $fileType,
        'size' => (int) $fileSize
    ];
}


?>