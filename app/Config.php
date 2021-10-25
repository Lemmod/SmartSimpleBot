<?php
/**
 * Set your own credentials for mysql/db
 */
define ('DB_HOST' , 'localhost');
define ('DB_DBNAME' , 'your_database_name');
define ('DB_CHARSET' , 'utf8mb4');
define ('DB_USERNAME' , 'db_user');
define ('DB_PASSWORD' , 'db_password');

define ('MAX_TIME_TO_CHECK_ALERT' , 35); // Set the max history in seconds to process an alert with the processor. Make sure this timescale is larger then frequency you call the alert_processor.

define ('DEMO_MODE' , 0); // Only used for hiding sensitve bot information. Generally used for creating documentation
