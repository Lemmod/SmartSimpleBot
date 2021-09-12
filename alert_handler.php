<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
/*****

Remember , script is under construction and not documented but the basics.

Use this script at your own risk!

It won't contain all possibilitys from the 3c API , mainly used for updating multiple bots at once

(c) 2021 - MileCrypto (Lemmod)

*/
include ('app/Config.php');
include ('app/Core.php');
include ('app/DataMapper.php');
include ('app/functions.php');

$dataMapper = new DataMapper();

// Grab the input from trading view
$tv_input = file_get_contents('php://input');

$clean_alerts = json_cleaner($tv_input);

$all_alerts  = explode("+" , $clean_alerts);

foreach ($all_alerts as $alert) {
    $dataMapper->insert_raw_tv_input(trim($alert) , 'alert_handler.php');
}

$dataMapper->close_connection(); 
?>
