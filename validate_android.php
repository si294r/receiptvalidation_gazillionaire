<?php

$filter_time = get_filter_time();

$connection = new PDO(
    "mysql:dbname=$mydatabase;host=$myhost;port=$myport",
    $myuser, $mypass
);

// change device_id to user_id
$device_id = get_user_id($device_id);

$sql = "SELECT transaction_id FROM transactions_android "
        . "WHERE device_id = :device_id AND product_id = :product_id "
        . "AND $filter_time";
$statement1 = $connection->prepare($sql);
$statement1->bindParam(":device_id", $device_id);
$statement1->bindParam(":product_id", $product_id);
$statement1->execute();
$row = $statement1->fetch(PDO::FETCH_ASSOC);

//var_dump($row);
if ($row) {
    $response = array("error" => 1, "message" => $product_id . " already purchase");
} else {
    $response = array("error" => 0, "message" => "");
}

