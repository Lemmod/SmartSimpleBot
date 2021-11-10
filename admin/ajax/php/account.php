<?php
error_reporting(E_ERROR);
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php?response=notloggedin');
	die;
}

include ('../../../app/Config.php');
include ('../../../app/Core.php');
include ('../../../app/3CommasConnector.php');
include ('../../../app/DataMapper.php');
include ('../../../app/DataReader.php');
include ('../../../app/Table.php');
include ('../../../app/functions.php');

$dataMapper = new DataMapper();
$dataReader = new DataReader();

$action = $_REQUEST['action'];

/**
 * Load all accounts for current user
 */
if($action == 'load_all_accounts') {

    $accounts = $dataReader->get_user_accounts($_SESSION['user_id']);

    // Terminate if the user is nog logged in
    check_credentials($_SESSION['user_id']);

    $account_response = [];

    $i = 0;
    
    foreach ($accounts as $account) {

        $settings = $dataReader->get_account_settings($account['internal_account_id']);

        $account_info = $dataReader->get_account_info_internal($account['internal_account_id']);
        $xcommas = new MC3Commas\threeCommas(BASE_URL , $account_info['api_key'] , $account_info['api_secret']); 

        try {
            $stats = $xcommas->get_bot_stats(['account_id' => $account['bot_account_id']]);
            $deals = $xcommas->get_deals(['account_id' => $account['bot_account_id'] , 'scope' => 'active']);

            $stats_usdt = '$ '.number_format($stats['today_stats']['USD'],2); ;
            $deals_running = count($deals);
        } catch (Exception $e) {

            $stats_usdt = '-error-';
            $deals_running = '-error-';
        } 
        
        $account_response[$i]['internal_id'] = $account['internal_account_id'];
        $account_response[$i]['3c_id'] = $account['bot_account_id'];
        $account_response[$i]['internal_name'] = $account['account_name'];
        $account_response[$i]['mad'] = $settings['max_active_deals'];
        $account_response[$i]['bo_size'] = $settings['bo_size'];
        $account_response[$i]['active'] = $settings['active'];
        $account_response[$i]['strategy'] = $settings['strategy_id'];
        $account_response[$i]['use_ss'] = $settings['use_smart_strategy'];
        $account_response[$i]['daily_usdt_profit'] = $stats_usdt;
        $account_response[$i]['open_deals'] = $deals_running;

        // Only show one account in Demo mode
        if (DEMO_MODE) {
            break;
        }
        
        $i++;
    }

    $table = new STable();
    $table->class = 'table table-hover table-striped table-bordered';
    $table->width = '100%';

    $table->thead()
        ->th('Account ID')
        ->th('Name')
        ->th('Max deals')
        //->th('BO/SO Size')
        ->th('Manage bots')
        ->th('Bot Status')
        ->th('Strategy')
        ->th('Smart Strategy')
        ->th('Strategy Status')
        ->th('TV Alerts')
        //->th('Telegram')
        ->th('Logs')
        ->th('Delete')
        ->th('Open deals')
        ->th('Daily profit');
        
   
    foreach ($account_response as $response) {

        if($response['active'] == 1) {
            $switch = '<a class="disable_account_link" id="account_'.$response['internal_id'].'"><i class="fas fa-stop-circle"></i> Disable</a>';
        }
        if($response['active'] == 0) {
            $switch = '<a class="enable_account_link" id="account_'.$response['internal_id'].'"><i class="fas fa-play-circle"></i> Enable</a>';
        }

        $strategies = $dataReader->get_strategies($response['internal_id']);

        $strat_list = array();

        $strat_list[0] = 'no strategy';
        foreach ($strategies as $strat) {
            $strat_list[$strat['strategy_id']] = $strat['strategy_name'];
        }

        $time_frames = $dataReader->get_time_frames($response['internal_id']);

        $status_overview = '';
        foreach($time_frames as $tf) {
            $tf_status = $dataReader->get_time_frame_status($tf['time_frame_id']);

            if ($tf_status == 'long') {
                $status_overview.= '<span class="dot long"></span>';
            } elseif ($tf_status == 'short') {
                $status_overview.= '<span class="dot short"></span>';
            } else {
                $status_overview.= '<span class="dot not_set"></span>';
            }
            
        }

        // Demo mode
        $response['3c_id'] = DEMO_MODE ? 1111111 : $response['3c_id'];
        $response['internal_name'] = DEMO_MODE ? 'DEMO Account' : $response['internal_name'];

        $table->tr()
        ->td($response['3c_id'])
        ->td($response['internal_name'])
        ->td(create_dropdown_number_with_id(0 , 100 , 'mad_dropdown' , 'mad_dropdown' , 'account_'.$response['internal_id'] , $response['mad']))
        //->td(create_dropdown_number_with_id(5 , 200 , 'bo_size' , 'bo_size' , 'account_'.$response['internal_id'] , $response['bo_size']))
        ->td('<a class="manage_bots_link" id="mbots_'.$response['internal_id'].'"><i class="fas fa-robot"></i> Manage bots </a>')
        ->td($switch)
        ->td(create_dropdown_options($strat_list, '' , 'strategy' , 'account_'.$response['internal_id'] , $response['strategy'] , true))
        ->td('Enabled : '.create_dropdown_options(['0' => 'no' , '1' => 'yes'], '' , 'use_ss' , 'account_'.$response['internal_id'] , $response['use_ss'] , true).'
             <a class="manage_strats_link" id="strats_'.$response['internal_id'].'">Edit strategies</a>')
        ->td($status_overview)
        ->td('<a class="tv_alerts_link" id="account_'.$response['internal_id'].'_strat_0"><i class="fas fa-chart-bar"></i>  Trading View alerts</a>')
        //->td('<a class="telegram_settings_link" id="account_'.$response['internal_id'].'"><i class="fas fa-comment-dots"></i>  Telegram settings</a> | <a class="test_message_link" id="sentmsg_'.$response['internal_id'].'"><i class="fas fa-paper-plane"></i> Test msg.</a>')
        ->td('<a class="logbook_link" id="account_'.$response['internal_id'].'"><i class="fas fa-book"></i>  Logbook</a>')
        ->td('<a class="delete_account_link" id="account_'.$response['internal_id'].'"><i class="fas fa-trash"></i>  Delete</a>')
        ->td($response['open_deals'])
        ->td($response['daily_usdt_profit']);
    }

    echo $table->getTable();

    $strat_list = array();

}


/**
 * Add an account
 */
if($action == 'add_account') {

    $internal_account_id = $dataMapper->insert_account($_SESSION['user_id'] , $_REQUEST['bot_account_id'] , $_REQUEST['account_name'] , $_REQUEST['api_key'] , $_REQUEST['api_secret']);

    $dataMapper->insert_account_settings($internal_account_id);

    $dataMapper->insert_default_strategies($internal_account_id);

    $dataMapper->insert_default_timeframes($internal_account_id);

    $dataMapper->insert_default_strategy_settings($internal_account_id);

    echo 'Account added.';
}

/**
 * Edit an account
 */
if($action == 'edit_account') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $dataMapper->edit_account($_SESSION['user_id'] , $internal_account_id);

    echo 'Account deleted.';
}

/**
 * Delete an account
 */
if($action == 'delete_account') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $dataMapper->delete_account($_SESSION['user_id'] , $internal_account_id);

    echo 'Account deleted.';
}

/**
 * Disable an account
 */
if($action == 'disable_account') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $dataMapper->enable_disable_account($internal_account_id , 0);

    echo 'Account disabled.';
}

/**
 * Enable an account
 */
if($action == 'enable_account') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $dataMapper->enable_disable_account($internal_account_id , 1);

    echo 'Account enabled.';
}


/**
 * Update max acticve deals
 */
if($action == 'update_max_active_deals') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $dataMapper->update_max_active_deals($internal_account_id , $_REQUEST['deals']);

    echo 'Max deals set to '.$_REQUEST['deals'];
}

/**
 * Update mode
 */
if($action == 'update_strategy') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $dataMapper->update_strategy($internal_account_id , $_REQUEST['strategy']);

    // Get strategy information
    $strat_info_new = $dataReader->get_strategy_info($_REQUEST['strategy']);
    $strat_name_new = $strat_info_new['strategy_name'];

    $dataMapper->insert_log($account_info['bot_account_id'] , 0 , '' , sprintf( 'MANUAL : Updating strategy to %s' , $strat_name_new));

    $dataMapper->update_max_active_deals($internal_account_id , $strat_info_new['max_active_deals']);


     // Also update the bots
     $set_bots = $dataReader->get_set_bots($strat_info_new['strategy_id']);
     if ($set_bots == 0) {
         $dataMapper->insert_log($account_info['bot_account_id'] , 0 , '' , 'No bot settings found for this strategy , not updating any bots.');
     } else {
        // We want to actually update our bots
        $xcommas = new MC3Commas\threeCommas(BASE_URL , $account_info['api_key'] , $account_info['api_secret']); 
        $xc_bots = $xcommas->get_all_bots(['account_id' => $account_info['bot_account_id'] , 'limit' => 100]);

        $errors = 0;
        // Updating the bots
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
                $dataMapper->insert_log($account_info['bot_account_id'] , 0 , '' , 'Not able to update bot setting for '.$bot['name'].' with message : '.$e->getMessage());
                //echo $e->getMessage();
                $errors++;
            }    
        }

        if ($errors > 0) {
            echo '<span style="color : red">Not able to update all bots, please refer to the logbook for more details. </span><br />';
        }
    }
    echo 'Strategy updated';
}

/**
 * Update mode
 */
if($action == 'update_use_ss') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $dataMapper->update_use_ss($internal_account_id , $_REQUEST['value']);

    echo 'Status updated';
}