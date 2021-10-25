<?php
error_reporting(E_ERROR);
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

$all_accounts = $dataReader->get_all_accounts();
$unprocessed_alerts = $dataReader->get_unprocessed_alerts(MAX_TIME_TO_CHECK_ALERT);




$total_alerts = count($unprocessed_alerts);
$errors_3c = 0;
$calls_3c = 0;

/** Set the alerts in process. This to prevent lagging API on 3C side to sent the alert multiple times */
foreach ($unprocessed_alerts as $alert) {
    $dataMapper->update_alert_in_process($alert['input_id']);
}

//pr($all_accounts);

foreach($all_accounts as $account_wrapper) {


    $account_info = $dataReader->get_account_info($account_wrapper['bot_account_id']);
    $account_settings = $dataReader->get_account_settings($account_info['internal_account_id']);



    /**
     * 
     * Check if account exist , if not we can skip this iteration
     * 
     */
    if(!$account_info) {
        echo 'Account not found...';
        continue; 
    } 

    $processed_alerts = 1;

    foreach ($unprocessed_alerts as $alert) {

        /**
         * Get the data , decode the JSON
         */
        $data = json_clean_decode($alert['input'] , TRUE);

        // Check if the current data belongs to the current account
        if($data['account_id'] == $account_wrapper['bot_account_id']) {

            // Only on first alert we need to setup 3Commas connection
            if ($processed_alerts == 1) {
                $xcommas_main = new MC3Commas\threeCommas(BASE_URL , $account_info['api_key'] , $account_info['api_secret']);
                $calls_3c++;
            
                try {
                    $deals = $xcommas_main->get_deals(['account_id' => $account_info['bot_account_id'] , 'scope' => 'active']);
                    $calls_3c++;
                    $count_active_deals_on_3c = count($deals);

                    $active_deal_bot_ids = array();
		    $active_deal_pairs = array();
                    foreach($deals as $deal) {
                        $active_deal_bot_ids[] = $deal['bot_id'];
                        $active_deal_pairs[] = ['bot_id' => $deal['bot_id'] , 'pair' => $deal['pair']];
                    }
                } catch (Exception $e) {

                    continue;
                } 
            }

            /**
             * Set the alert as processed
             */
            $dataMapper->update_alert($alert['input_id'] , date('Y-m-d H:i:s',time()));
            $processed_alerts++;


            /**
             * Get source information from JSON and check account info
             */
            $bot_account_id = $data['account_id'];
            $message = $data['message'];

            /**
             * 
             * Enable bots / account on Smart Simple Bot (doesn't affect 3C bots / account)
             * 
             */
            if($message == 'enable_bots') {
                $dataMapper->enable_disable_account($account_info['internal_account_id'] , 1);
                $dataMapper->insert_log($data['account_id'] , 0 , '' , 'Bot enabled by TV message');
                continue; 
            }

            /**
             * 
             * Disable bots / account on Smart Simple Bot (doesn't affect 3C bots / account)
             * 
             */
            if($message == 'disable_bots') {
                $dataMapper->enable_disable_account($account_info['internal_account_id'] , 0);
                $dataMapper->insert_log($data['account_id'] , 0 , '' , 'Bot disabled by TV message');
                continue; 
            }


            /**
             * 
             * Disable bots / account on Smart Simple Bot (doesn't affect 3C bots / account)
             * 
             */
            if($message == 'set_mode') {
                $dataMapper->update_mode($account_info['internal_account_id'] , $data['value']);
                $dataMapper->insert_log($data['account_id'] , 0 , '' , 'Bot updated via TV alert to mode: '.$data['value']);
                continue; 
            }

            /**
             * 
             * Open deals checker , sent to Telegram if hit
             * 
             */
            if($message == 'check_open_deals') {

                // When there are more deals open then user settings send an alert via Telegarm
                if (($count_active_deals_on_3c > $account_settings['max_active_deals'])) {

                    if($account_settings['notify_telegram']) {

                        $telegram_bot_hash = $account_settings['telegram_bot_hash'];
                        $telegram_chat_id = $account_settings['telegram_chat_id'];
                        $msg = 'Account : '.$account_info['account_name'].' .There are more open deals on 3Commas then set for user. ( Active : '.($count_active_deals_on_3c).' , Max : '.$account_settings['max_active_deals'].' )';

                        telegram($telegram_bot_hash , $telegram_chat_id , $msg);

                    }
                }
                continue;
            }

            // Update the status of an set time_frame for use with smart strategy
            if($message == 'set_tf') {
                $dataMapper->insert_timeframe_status($account_info['internal_account_id'] , $data['tf_id'] , $data['type']);
                $dataMapper->insert_log($data['account_id'] , 0 , '' , 'Added status for '.$data['label'].' with type '.$data['type']);
                continue;
            }

            /**
             * 
             * Check if account is active , if not we can skip this record
             * 
             */
            if($account_settings['active'] == 0) {
                $dataMapper->insert_log($data['account_id'] , $data['bot_id'] , $data['pair'] , 'Account disabled...');
                continue;
            }

            /** 
             * 
             * Check if the alert is using the same mode as set
             * 
             */

            // Check the alert mode , if not set we assume it's for all modes
            $alert_mode = !isset($data['strategy_id']) ? 'all' : $data['strategy_id'];
            // Check the account mode setting , if not set we assume it's set to all
            $set_mode = $account_settings['strategy_id'] == '-1' ? 'all' : $account_settings['strategy_id'];

            $alert_passed_mode = false;
            if($alert_mode == 'all' || $set_mode == 'all') {
                $alert_passed_mode = true;
            } elseif ($set_mode == $alert_mode) {
                $alert_passed_mode = true;
            }

            // If the alert doesn't get through the mode we stop this alert from further processing
            if (!$alert_passed_mode) {
                $set_mode_name = $dataReader->get_strategy_info($set_mode);
                $alert_mode_name = $dataReader->get_strategy_info($alert_mode);

                $dataMapper->insert_log($data['account_id'] , $data['bot_id'] , $data['pair'] , 'Alert not proccesed due different mode settings. Set : '.$set_mode_name['strategy_name']. ', Alert : '.$alert_mode_name['strategy_name']);
                continue;
            }
            
            /**
             * 
             * 
             * Get deals on the current bot , first check if there isn't allready running an order , in that case we can skipe the rest
             * 
             */
            // Check if the deal is allready running
            $deal_running = false;

            foreach ($active_deal_pairs as $adp) {
                // If there is an active pair we assume the deal is allready running. Also check for "old" pair naming on 3c to prevent duplication
                if ($adp['pair'] ==  $data['pair'] || $adp['pair'] == substr($data['pair'] , 0 , -4)) {
                    $deal_running = true;
                }
            }

            // If there is allready a deal (pair) running we can skip further logic. We won't open a 2nd deal
            if($deal_running) {

                $dataMapper->insert_log($data['account_id'] , $data['bot_id'] , $data['pair'] , 'Deal allready running');

            } else {
                
                $bot_info = $xcommas_main->get_bot_info($data['bot_id']);
                $calls_3c++;

                if ( ($count_active_deals_on_3c < $account_settings['max_active_deals'])  && !is_null($deals) && $bot_info['is_enabled']) {

                    /**
                     * Create the deal on 3Commas
                    */ 
                    try {
                        $xcommas_main->start_deal_on_bot($data['bot_id'] , ['pair' => $data['pair']]);
                        $dataMapper->insert_log($data['account_id'] , $data['bot_id'] , $data['pair'] , 'Deal added ( Active : '.($count_active_deals_on_3c + 1).' , Max : '.$account_settings['max_active_deals'].' )');
                        $calls_3c++;

                        $count_active_deals_on_3c++;

                    } catch (Exception $e) {
                        echo ' > Pair : '.$data['pair'].PHP_EOL;
                        echo ' > Caught exception: '.$e->getMessage().'.'.PHP_EOL;
                    }
                
                } else {
                    /**
                     * Log reason for not able to add deal
                     */
                    if (is_null($deals)) {
                        $errors_3c++;
                        $dataMapper->insert_log($data['account_id'] , $data['bot_id'] , $data['pair'] , 'Deal not added , ERROR - 3Commas deal count is null');
                    } elseif (!$bot_info['is_enabled']) {
                        $dataMapper->insert_log($data['account_id'] , $data['bot_id'] , $data['pair'] , 'Deal not added , BOT Not enabled');
                    } else {
                        $dataMapper->insert_log($data['account_id'] , $data['bot_id'] , $data['pair'] , 'Deal not added , max active deals hit ( Active : '.$count_active_deals_on_3c.' , Max : '.$account_settings['max_active_deals'].' )');
                    }
                }
            }
        }
    }

    $xcommas_main = null;
}

/**
 * 
 * Used for debug
 * 
 */
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finishtime = $time;
$total_time = round(($finishtime - $start), 4);

echo 'SSB - 3 Commas '.date('Y-m-d H:i:s').' script ran for '.$total_time.PHP_EOL;

$dataMapper->insert_debug_log(basename(__FILE__) , $total_alerts , $errors_3c , $calls_3c , $total_time) ;

/**
 * Close connection
 */
$dataMapper->close_connection(); 
