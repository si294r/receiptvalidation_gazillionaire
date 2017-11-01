<?php

include("config.php");
include_once('function.php');

$json = json_decode($input, true);
$product_id = $json["product_id"];
//$facebook_id = $json["facebook_id"];
$device_id = $json["device_id"];
$receipt_data = $json["receipt_data"];
$os = isset($json["os"]) ? $json["os"] : "iOS";


if ($os == "iOS") {
    if (isset($receipt_data) && $receipt_data != "") {
        include 'validate_ios_remote.php';
    } else {
        include 'validate_ios.php';
    }
} else if ($os == "Android") {
    include 'validate_android.php';
} else {
    $response = array("error" => 1, "message" => "$.os is invalid!");
}


return $response;