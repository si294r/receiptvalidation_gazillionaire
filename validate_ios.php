<?php

if ($IS_DEVELOPMENT == false) {
    $filter_time = "NOW() <= COALESCE(expired_date, NOW())"; 
} else {
    $iservice = "gettime-dev";
    $result_gettime = file_get_contents('http://alegrium5.alegrium.com/gazillionaire/cloudsave/?'.$iservice, null, stream_context_create(
            array(
                'http' => array(
                    'method' => 'POST',
                    'header' => 'Content-Type: application/json'. "\r\n"
                    . 'x-api-key: ' . X_API_KEY_TOKEN . "\r\n"
                    . 'Content-Length: ' . strlen('{}') . "\r\n",
                    'content' => '{}'
                )
            )
        )
    );
    $result_gettime = json_decode($result_gettime, true);
    $timestamp = $result_gettime['timestamp'];

    $filter_time = "$timestamp <= COALESCE(UNIX_TIMESTAMP(expired_date), $timestamp)"; 
}

$connection = new PDO(
    "mysql:dbname=$mydatabase;host=$myhost;port=$myport",
    $myuser, $mypass
);

$sql = "SELECT transaction_id FROM transactions "
        . "WHERE device_id = :device_id AND product_id = :product_id "
        . "AND $filter_time";
$statement1 = $connection->prepare($sql);
$statement1->bindParam(":device_id", $device_id);
$statement1->bindParam(":product_id", $product_id);
$statement1->execute();
$row = $statement1->fetch(PDO::FETCH_ASSOC);

//        var_dump($row);
if ($row) {
    $response = array("error" => 1, "message" => $product_id . " already purchase");
} else {
    $response = array("error" => 0, "message" => "");
}

