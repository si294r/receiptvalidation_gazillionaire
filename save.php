<?php

include("config.php");
include_once('function.php');

$json = json_decode($input, true);
$transaction_id = $json["transaction_id"];
$product_id = $json["product_id"];
$product_type = $json["product_type"];
$product_value = $json["product_value"];
$interval_value = is_numeric($json["interval_value"]) ? $json["interval_value"] : 1;
$interval_unit = in_array($json["interval_unit"], array("day", "hour", "minute")) ? $json["interval_unit"] : "day";
//$facebook_id = $json["facebook_id"];
$facebook_id = "";
$device_id = $json["device_id"];
$receipt_data = $json["receipt_data"];
$platform = isset($json["platform"]) ? $json["platform"] : "iOS";

if ($platform == "iOS") {
    include 'save_ios.php';
} else if ($platform == "Android") {
    include 'save_android.php';
} else {
    $response = array("error" => 1, "message" => "platform is invalid!");
}

return $response;