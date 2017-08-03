<?php

include("config.php");

$json = json_decode($input, true);
$product_id = $json["product_id"];
$value = $json["value"];
$facebook_id = $json["facebook_id"];
$device_id = $json["device_id"];
$receipt_data = $json["receipt_data"];

$data_string = json_encode(array(
    "password" => SHARED_SECRET,
    "receipt-data" => $receipt_data
));

$result = file_get_contents($url_receipt_validation, null, stream_context_create(
        array(
            'http' => array(
                'method' => 'POST',
                    'header' => 'Content-Type: application/json' . "\r\n"
                    . 'Content-Length: ' . strlen($data_string) . "\r\n",
                    'content' => $data_string
            )
        )
    )
);
$array_json = json_decode($result, TRUE);

// 1. Save file local
$data_dir = "data";
if ($array_json["status"] == 0) {
    $type_dir = $data_dir . "/" . $array_json["receipt"]["receipt_type"];
    if (!is_dir($type_dir)) {
        mkdir($type_dir);
    }
    $bundle_dir = $type_dir . "/" . $array_json["receipt"]["bundle_id"];
    if (!is_dir($bundle_dir)) {
        mkdir($bundle_dir);
    }
    foreach ($array_json["receipt"]["in_app"] as $k=>$v) {
        $date_dir = $dir . "/" . substr($v["purchase_date"], 0, 19);
        $date_dir = str_replace(array("-", " ", ":"), "", $date_dir);
        if (!is_dir($date_dir)) {
            mkdir($date_dir);
        }
        
        $file_iap = $date_dir . "/" . $v["transaction_id"]; 
        file_put_contents($file_iap, json_encode($v));
    }
}

// 2. Save to database inbox - TODO

return $array_json;