<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
/*****

Remember , script is under construction and not documented but the basics.

Use this script at your own risk!

It won't contain all possibilitys from the 3c API , mainly used for updating multiple bots at once

(c) 2021 - MileCrypto (Lemmod)

*/

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;

include (__DIR__.'/app/Config.php');
include (__DIR__.'/app/Core.php');
include (__DIR__.'/app/3CommasConnector.php');
include (__DIR__.'/app/DataMapper.php');
include (__DIR__.'/app/DataReader.php');
include (__DIR__.'/app/functions.php');

$dataMapper = new DataMapper();
$dataReader = new DataReader();

$upgrade_sql = "
CREATE TABLE IF NOT EXISTS `bot_settings` (
    `bot_setting_id` int NOT NULL AUTO_INCREMENT,
    `bot_id` int NOT NULL,
    `strategy_id` int NOT NULL,
    `label` text NOT NULL,
    `value` text NOT NULL,
    PRIMARY KEY (`bot_setting_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `strategies` (
    `strategy_id` int NOT NULL AUTO_INCREMENT,
    `internal_account_id` int NOT NULL,
    `strategy_name` text NOT NULL,
    `max_active_deals` int NOT NULL DEFAULT '0',
    `locked` int NOT NULL DEFAULT '0',
    PRIMARY KEY (`strategy_id`),
    KEY `strategies_delete_account` (`internal_account_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `strategy_settings` (
    `strategy_setting_id` int NOT NULL AUTO_INCREMENT,
    `combination_id` int NOT NULL,
    `strategy_id` int NOT NULL,
    `time_frame_id` int NOT NULL,
    `type` enum('short','long') NOT NULL DEFAULT 'short',
    PRIMARY KEY (`strategy_setting_id`),
    KEY `time_frame_strat_settings` (`time_frame_id`),
    KEY `strategy_strat_settings` (`strategy_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `time_frames` (
    `time_frame_id` int NOT NULL AUTO_INCREMENT,
    `internal_account_id` int NOT NULL,
    `label` text NOT NULL,
    `description` text NOT NULL,
    PRIMARY KEY (`time_frame_id`),
    KEY `time_frames_account_delete` (`internal_account_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `time_frame_status` (
    `status_id` int NOT NULL AUTO_INCREMENT,
    `account_id` int NOT NULL,
    `time_frame_id` int NOT NULL,
    `type` varchar(10) NOT NULL,
    `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`status_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `account_settings` ADD COLUMN IF NOT EXISTS `strategy_id` int NOT NULL DEFAULT '-1';
ALTER TABLE `account_settings` ADD COLUMN IF NOT EXISTS `use_smart_strategy` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `account_settings` ADD KEY IF NOT EXISTS `account_settings_account_delete` (`internal_account_id`);


ALTER TABLE `account_settings`
  ADD CONSTRAINT  `account_settings_account_delete` FOREIGN KEY IF NOT EXISTS (`internal_account_id`) REFERENCES `accounts` (`internal_account_id`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `strategies`
  ADD CONSTRAINT `strategies_delete_account` FOREIGN KEY IF NOT EXISTS (`internal_account_id`) REFERENCES `accounts` (`internal_account_id`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `strategy_settings`
  ADD CONSTRAINT  `strategy_strat_settings` FOREIGN KEY IF NOT EXISTS (`strategy_id`) REFERENCES `strategies` (`strategy_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT  `time_frame_strat_settings` FOREIGN KEY IF NOT EXISTS (`time_frame_id`) REFERENCES `time_frames` (`time_frame_id`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `time_frames`
  ADD CONSTRAINT  `time_frames_account_delete` FOREIGN KEY IF NOT EXISTS (`internal_account_id`) REFERENCES `accounts` (`internal_account_id`) ON DELETE CASCADE ON UPDATE CASCADE;
";

$stmt = $dataMapper->dbh->prepare($upgrade_sql);
$stmt->execute();
$stmt = null;

$all_accounts = $dataReader->get_all_accounts();

foreach($all_accounts as $account_wrapper) {

    echo '<h1>Account : '.$account_wrapper['bot_account_id'].'</h1>';
    
    $strategies = $dataReader->get_strategies($account_wrapper['internal_account_id']);

    if (count($strategies) > 0) {
        echo 'Strategies allready found , not updating...<br />';
    } else {
        // Add default strategies
        $dataMapper->insert_default_strategies($account_wrapper['internal_account_id']);

        $dataMapper->insert_default_timeframes($account_wrapper['internal_account_id']);

        $dataMapper->insert_default_strategy_settings($account_wrapper['internal_account_id']);
    }
}

echo 'Looks like everything is updated!';

?>