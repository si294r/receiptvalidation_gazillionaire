<?php

include("config.php");

$json = json_decode($input, true);
$product_id = $json["product_id"];
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

if ($array_json["status"] == 0) {
    if (in_array($product_id, array_column($array_json["receipt"]["in_app"], "product_id"))) {
        
        // TODO check database 
        $already_purchase = true;
        $connection = new PDO(
            "mysql:dbname=$mydatabase;host=$myhost;port=$myport",
            $myuser, $mypass
        );
        
        foreach ($array_json["receipt"]["in_app"] as $k=>$v) {
            if ($v['product_id'] == $product_id) {
                
                $sql = "SELECT * FROM transactions WHERE transaction_id = :transaction_id "
                        . "AND NOW() <= COALESCE(expired_date, NOW())";
        
                $statement1 = $connection->prepare($sql);
                $statement1->bindParam(":transaction_id", $v['transaction_id']);
                $row = $statement1->fetch(PDO::FETCH_ASSOC);
                var_dump($row);
                
            }
        }
        if ($already_purchase) { 
            $response = array("error" => 1, "message" => $product_id . " already purchase");
        } else {
            $response = array("error" => 0, "message" => "");
        }
    } else {
        $response = array("error" => 0, "message" => "");
    }
} else {
    $response = array("error" => 1, "message" => "Receipt Validation Failed. Status : " . $array_json["status"]);    
}

return $response;