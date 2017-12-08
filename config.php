<?php

include("/var/www/mysql-config2.php");

$mydatabase = $IS_DEVELOPMENT ? "gazillionairedev" : "gazillionaire";

$url_static_time = "http://alegrium5.alegrium.com/gazillionaire/cloudsave/";

$aws_s3_appname = "gazillionaire";

define('CACHE_USER_DEV', "gazdev_user_");
define('CACHE_USER', "gaz_user_");

$url_sandbox = "https://sandbox.itunes.apple.com/verifyReceipt";
$url_production = "https://buy.itunes.apple.com/verifyReceipt";

$url_receipt_validation = $IS_DEVELOPMENT ? $url_sandbox : $url_production;

// IN-GAME COPIES

define('STR_VERIFIED_INSTALL_CRYSTALS1', "FREE CRYSTALS!");
define('STR_VERIFIED_INSTALL_CRYSTALS2', "Your friend has installed Billionaire 2.");
define('STR_VERIFIED_INSTALL_CASH1', "CASH REWARD!");
define('STR_VERIFIED_INSTALL_CASH2', "Your friend has installed Billionaire 2.");

define('STR_ALERT_INBOX_TITLE1', "FREE CRYSTALS!");
define('STR_ALERT_INBOX_CAPTION1', "Boost your business now!");
define('STR_ALERT_INBOX_TITLE2', "SUBSCRIPTION ALMOST ENDS!");
define('STR_ALERT_INBOX_CAPTION2', "Extend time to enjoy the benefits even longer!");
define('STR_ALERT_INBOX_TITLE3', "SUBSCRIPTION HAD ENDED!");
define('STR_ALERT_INBOX_CAPTION3', "Let's get another!");
define('STR_ALERT_INBOX_TITLE4', "FREE CASH!");
define('STR_ALERT_INBOX_CAPTION4', "Let's build the other business!");
define('STR_ALERT_INBOX_TITLE_WEST', "FREE CASH!");
define('STR_ALERT_INBOX_CAPTION_WEST', "Let's build a business!");
define('STR_ALERT_INBOX_TITLE_EAST', "CASH BONUS!");
define('STR_ALERT_INBOX_CAPTION_EAST', "Continue your business journey now!");
