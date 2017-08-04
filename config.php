<?php

include("/var/www/mysql-config2.php");

$url_sandbox = "https://sandbox.itunes.apple.com/verifyReceipt";
$url_production = "https://buy.itunes.apple.com/verifyReceipt";

$url_receipt_validation = $IS_DEVELOPMENT ? $url_sandbox : $url_production;

