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

$all_accounts = $dataReader->get_all_accounts();

foreach($all_accounts as $account_wrapper) {
    
    echo 'Processing account : '.$account_wrapper['bot_account_id'].'<br />';

    $account_settings = $dataReader->get_account_settings($account_wrapper['internal_account_id']);
    $internal_account_id = $account_settings['internal_account_id'];

    // If smart strategy is disabled we can skip all
    if($account_settings['use_smart_strategy'] == 0) {
        $dataMapper->insert_log($account_wrapper['bot_account_id'] , 0 , '' , 'Smart strategy not enabled , skip processing...');
        continue;
    } else {

        // Get current selected strategy
        $current_strategy = $account_settings['strategy_id'];

        $time_frames = $dataReader->get_time_frames($internal_account_id);

        $tf_data = [];
        foreach($time_frames as $tf) {
            $tf_status = $dataReader->get_time_frame_status($tf['time_frame_id']);
            $tf_data[$tf['time_frame_id']] = $tf_status;
        }

        $info = strat_query_builder($tf_data);

        $corresponding_strategy = $dataReader->get_strategy_setting_info($info['join'] , $info['filter']);

        // If the current strategy is the same as calculated with current time_frames we can continue
        if ($current_strategy == $corresponding_strategy['strategy_id']) {
            //$dataMapper->insert_log($account_wrapper['bot_account_id'] , 0 , '' , 'Strategy is the same as currently running , no need to update...');
            continue;
        } else {

            // Get info on strategy names for clearer logging
            $strat_info_old = $dataReader->get_strategy_info($current_strategy);
            $strat_info_new = $dataReader->get_strategy_info($corresponding_strategy['strategy_id']);

            if ($current_strategy == -1 || $current_strategy == 0) {
                $strat_name_old = 'no strategy';
            } else {
                $strat_name_old = $strat_info_old['strategy_name'];
            }
            $strat_name_new = $strat_info_new['strategy_name'];

            $dataMapper->insert_log($account_wrapper['bot_account_id'] , 0 , '' , sprintf( 'AUTO : Updating strategy from %s to %s' , $strat_name_old , $strat_name_new));
            

            // Updating strategy and Max active deals
            $dataMapper->update_max_active_deals($internal_account_id , $strat_info_new['max_active_deals']);
            $dataMapper->update_strategy($internal_account_id , $strat_info_new['strategy_id']);

            // Also update the bots
            $set_bots = $dataReader->get_set_bots($strat_info_new['strategy_id']);
            if ($set_bots == 0) {
                $dataMapper->insert_log($account_wrapper['bot_account_id'] , 0 , '' , 'No bot settings found for this strategy , not updating any bots (set bots on 3commas will be used)');
                continue;                
            } else {
                // We want to actually update our bots
                $account_info = $dataReader->get_account_info_internal($internal_account_id);
                $xcommas = new MC3Commas\threeCommas(BASE_URL , $account_info['api_key'] , $account_info['api_secret']); 
                $xc_bots = $xcommas->get_all_bots(['account_id' => $account_info['bot_account_id'] , 'limit' => 100]);

                foreach ($xc_bots as $bot) {

                    $strat_bot_info = $dataReader->get_bot_settings($bot['id'] , $strat_info_new['strategy_id']);

                    $bot_setting = array();
                    foreach ($strat_bot_info as $bi) {
                        $bot_setting[$bi['label']] = $bi['value'];
                    }
           
                    $data = [
                        // General
                        'name' => $bot['name'],
                        'pairs' => json_encode($bot['pairs']),
                        'start_order_type' => $bot_setting['start_order_type'],
                        'strategy_list' => json_encode(['strategy' => $bot['strategy_list'][0]['strategy']]),
                        'bot_id' => $bot['id'],
                        'is_enabled' => $bot_setting['is_enabled'],
                        
                         // Volumes
        
                        'base_order_volume' => $bot_setting['base_order_volume'],
                        'safety_order_volume' => $bot_setting['safety_order_volume'],                        
                        'base_order_volume_type' => $bot_setting['base_order_volume_type'],
                        'safety_order_volume_type' => $bot_setting['safety_order_volume_type'],
        
                        // SO paramaters
                        'safety_order_step_percentage' => $bot_setting['safety_order_step_percentage'],
                        'max_safety_orders' => $bot_setting['max_safety_orders'],
                        'active_safety_orders_count' => $bot_setting['active_safety_orders_count'],
                        'martingale_volume_coefficient' => $bot_setting['martingale_volume_coefficient'],
                        'martingale_step_coefficient' => $bot_setting['martingale_step_coefficient'],
                        
                        // Profit types
                        'take_profit_type' => $bot['take_profit_type'],
                        'take_profit' => $bot_setting['take_profit'],
                        'trailing_enabled' => $bot_setting['trailing_enabled'],
                        'trailing_deviation' => $bot_setting['trailing_deviation'],
        
                        'cooldown' => $bot_setting['cooldown'],     
        
                        // Leverage parameters
                        'leverage_type' =>  $bot_setting['leverage_type'], //$bot['leverage_type'] ,
                        'leverage_custom_value' => $bot_setting['leverage_custom_value'], //$bot['leverage_custom_value'],
                    ];
                    
                    if ($bot_setting['is_enabled'] == "1") {
                        $xcommas->enable_bot($bot['id']);
                    } 
                    if ($bot_setting['is_enabled'] == "0") {
                        $xcommas->disable_bot($bot['id']);
                    }  
                           
                    try {
                        $update = $xcommas->update_bot($bot['id'] , $data);
                    } catch (Exception $e) {
                        $dataMapper->insert_log($account_wrapper['bot_account_id'] , 0 , '' , 'Not able to update bot setting for '.$bot['name'].' with message : '.$e->getMessage());
                        echo $e->getMessage();
                    }    
                }
            } 
        }
    }
}
?>