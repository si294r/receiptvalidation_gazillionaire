<?php

use Aws\S3\S3Client;

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
    
    // 1. Save file to S3
    $s3ClientS3 = new S3Client(array(
        'credentials' => array(
            'key' => $aws_access_key_id,
            'secret' => $aws_secret_access_key
        ),
        "region" => "us-east-1",
        "version" => "2006-03-01"
    ));

    $receipt_type = $array_json["receipt"]["receipt_type"];
    
    foreach ($array_json["receipt"]["in_app"] as $k=>$v) {
        $s3ClientS3->putObject(array(
            'Bucket' => "alegrium-iap",
            'Key'    => "$aws_s3_appname/iOS/$receipt_type/{$v["transaction_id"]}",
            'Body'   => json_encode($v)
        ));
    }
    
    // 2. Save payment transaction to database  
    if (in_array($transaction_id, array_column($array_json["receipt"]["in_app"], "transaction_id"))) {
        
        $key = array_search($transaction_id, array_column($array_json["receipt"]["in_app"], "transaction_id"));
        $receipt_transaction = json_encode($array_json["receipt"]["in_app"][$key]); 
        
        if ($product_type == "Subscription") {
            $product_value = $product_value.",CRYSTAL,IAP_SUBSCRIPTION";
        }
        
        $connection = new PDO(
            "mysql:dbname=$mydatabase;host=$myhost;port=$myport",
            $myuser, $mypass
        );

        // change device_id to user_id
        $device_id = get_user_id($device_id);

        $sql = "INSERT IGNORE INTO transactions (transaction_id, product_id, product_type, product_value, facebook_id, device_id, receipt_data) 
                VALUES (:transaction_id, :product_id, :product_type, :product_value, :facebook_id, :device_id, :receipt_data)";
        
        $statement1 = $connection->prepare($sql);
        $statement1->bindParam(":transaction_id", $transaction_id);
        $statement1->bindParam(":product_id", $product_id);
        $statement1->bindParam(":product_type", $product_type);
        $statement1->bindParam(":product_value", $product_value);
        $statement1->bindParam(":facebook_id", $facebook_id);
        $statement1->bindParam(":device_id", $device_id);
        $statement1->bindParam(":receipt_data", $receipt_transaction);
        $statement1->execute();

        if ($product_type == "Subscription") {
            
            $sql = "UPDATE transactions SET expired_date = date_add(purchase_date, interval $interval_value $interval_unit) 
                    WHERE transaction_id = :transaction_id";
            $statement1 = $connection->prepare($sql);
            $statement1->bindParam(":transaction_id", $transaction_id);
            $statement1->execute();

//            TODO - integrate to inbox
            if (strpos($product_value, "CASH") !== FALSE) {
                $title = STR_ALERT_INBOX_TITLE4;
                $caption = STR_ALERT_INBOX_CAPTION4;
            } else {
                $title = STR_ALERT_INBOX_TITLE1;
                $caption = STR_ALERT_INBOX_CAPTION1;
            }
            
            $union = [];
            for ($i = 0; $i< $interval_value; $i++) {
                $union[] = "SELECT '$interval_unit' as unit, $i as col, $interval_value as unit_total";
            }
            $sql_union = implode(" UNION ", $union);
            $sql = "INSERT INTO master_inbox (type, header, message, data, target_device, target_fb, os, status, valid_from)
                    SELECT 'reward', :title, :caption, CONCAT(:data, ',', t2.unit, ',', t2.col+1, ',', t2.unit_total), :target_device, :target_fb, 'All', 1,
                        date_add(purchase_date, interval t2.col $interval_unit)
                    FROM transactions,
                    ($sql_union) t2
                    WHERE transaction_id = :transaction_id ";
            $statement1 = $connection->prepare($sql);
            $statement1->bindParam(":title", $title);
            $statement1->bindParam(":caption", $caption);
            $statement1->bindParam(":data", $product_value);
            $statement1->bindParam(":target_device", $device_id);
            $statement1->bindParam(":target_fb", $facebook_id);
            $statement1->bindParam(":transaction_id", $transaction_id);
            $statement1->execute();
        }
        
        $response = array("error" => 0, "message" => "");
    } else {
        $response = array("error" => 1, "message" => "transaction_id: $transaction_id with product_id: $product_id is not registered in receipt data");
    }

} else {
    $response = array("error" => 1, "message" => "Receipt Validation Failed. Status : " . $array_json["status"]);
}

