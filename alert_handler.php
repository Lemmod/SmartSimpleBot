<?php
/*****

(c) 2021 - Lemmod

Handles all the alerts directly from TradingView

*/

ini_set('display_errors', 1);
error_reporting(E_ALL);

include (__DIR__.'/app/Config.php');
include (__DIR__.'/app/Core.php');
include (__DIR__.'/app/DataMapper.php');
include (__DIR__.'/app/functions.php');

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
