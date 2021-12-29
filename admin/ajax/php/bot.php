<?php
/*****

(c) 2021 - Lemmod

Handles bot settings such as updating or changing bots
*/
error_reporting(E_ERROR);
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
 * Update BO/SO size
 */
if($action == 'update_bo_size') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];

    $size = $_REQUEST['size'];

    // Get settings to create 3C connection
    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    // Update 3C , takes a while
    $xcommas = new MC3Commas\threeCommas(BASE_URL , $account_info['api_key'] , $account_info['api_secret']); 
    $bots = $xcommas->get_all_bots(['account_id' => $account_info['bot_account_id'] , 'limit' => 100]);

    foreach ($bots as $bot) {

        $data = [
            'name' => $bot['name'],
            'pairs' => json_encode($bot['pairs']),
            'base_order_volume' => $size,
            'take_profit' => $bot['take_profit'],
            'safety_order_volume' => $size,
            'martingale_volume_coefficient' => $bot['martingale_volume_coefficient'],
            'martingale_step_coefficient' => $bot['martingale_step_coefficient'],
            'max_safety_orders' => $bot['max_safety_orders'],
            'active_safety_orders_count' => $bot['active_safety_orders_count'],
            'safety_order_step_percentage' => $bot['safety_order_step_percentage'],
            'take_profit_type' => $bot['take_profit_type'],
            'trailing_enabled' => $bot['trailing_enabled'],
            'trailing_deviation' => $bot['trailing_deviation'],
            'strategy_list' => json_encode(['strategy' => $bot['strategy_list'][0]['strategy']]),
            'bot_id' => $bot['id'],
            'leverage_type' =>  $bot['leverage_type'] ,
            'leverage_custom_value' => $bot['leverage_custom_value'],
            'start_order_type' => $bot['start_order_type']

        ];
    
        $update = $xcommas->update_bot($bot['id'] , $data);
    } 

    $dataMapper->update_bo_size($internal_account_id , $_REQUEST['size']);

    echo 'Size update to '.$size.'. Please review your settings on 3Commas!';
}


/**
 * Load all bots on account
 */
if($action == 'load_bots') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $xcommas = new MC3Commas\threeCommas(BASE_URL , $account_info['api_key'] , $account_info['api_secret']); 
    $bots = $xcommas->get_all_bots(['account_id' => $account_info['bot_account_id'] , 'limit' => 100]);

    array_multisort(array_column($bots, 'name'),  SORT_ASC , $bots);

    $table = new STable();
    $table->class = 'table table-hover table-striped table-bordered';
    $table->width = '100%';
    $table->id = 'manage_bots_table';

    echo '<h2> Manage bots </h2>';

    echo '<form method="POST" id="change_bots" onsubmit="return a_change_bots();">';

    echo '<input type="submit" name="submit_form" class="submit_mb" value="Save all changes">';
    echo '<input type="hidden" name="account_id" value="'.$internal_account_id.'"/>';
   
         
    $table->thead()
        ->th('Bot (name):')
        ->th('Pair:')
        ->th('Start condition')
        ->th('Max SO:')
        ->th('Active SO:')
        ->th('BO/SO Type:')
        ->th('BO Size:')
        ->th('SO Size:')
        ->th('Safety order step:')
        ->th('SO Volume:')
        ->th('SO Step scale:')
        ->th('Take profit %:')
        ->th('Trailing enabled?:')
        ->th('Trailing %:')
        ->th('Cooldown:')
        ->th('Leverage type')
        ->th('Leverage setting')
        ->th('Enabled?');

  

    $table->tr('global_header')
        ->td('Change for all bots :' , '' , 'colspan=2')
        ->td(create_dropdown_options(['limit' , 'market'] , '' , 'so_type_all' , '' , 'limit'))
        ->td(create_input_number('' , 'max_so_all' , '' , ''))
        ->td(create_input_number('' , 'act_so_all' , '' , ''))
        ->td(create_dropdown_options(['fixed' , 'percentage'] , '' , 'size_type_all' , 'cross' , ''))
        ->td(create_input_float('' , 'bo_size_all' , '' , ''))
        ->td(create_input_float('' , 'so_size_all' , '' , ''))
        ->td(create_input_float('' , 'so_perc_all' , '' , ''))
        ->td(create_input_float('' , 'so_volume_all' , '' , ''))
        ->td(create_input_float('' , 'so_step_all' , '' , ''))
        ->td(create_input_float('' , 'tp_all' , '' , ''))
        ->td(create_dropdown_options([0,1] , '' , 'ttp_all' , '' , '1'))
        ->td(create_input_float('' , 'ttp_deviation_all' , '' , ''))
        ->td(create_input_number('' , 'cooldown_all' , '' , '0'))
        ->td(create_dropdown_options(['cross' , 'isolated'] , '' , 'lev_type_all' , 'cross' , ''))
        ->td(create_dropdown_number(0 , 125 , '' , 'lev_value_all' , '' , '20'))
        ->td(create_dropdown_options([0,1] , '' , 'is_enabled_all' , '' , '1'));

    

    foreach ($bots as $bot) {

        if($bot['base_order_volume_type'] == 'percent') {
            $size_type = 'percentage';
        } else {
            $size_type = 'fixed';
        }

        $table->tr()
            ->td($bot['name'])
            ->td($bot['pairs'][0])
            ->td(create_dropdown_options(['limit' , 'market'] , 'so_type_bots_'.$bot['id'] , 'so_type_bots' , 'bot_'.$bot['id'] , $bot['start_order_type']))
            ->td(create_input_number('max_so_bots_'.$bot['id'] , 'max_so_bots' , 'bot_'.$bot['id'] , $bot['max_safety_orders']))
            ->td(create_input_number('act_so_bots_'.$bot['id'] , 'act_so_bots' , 'bot_'.$bot['id'] , $bot['active_safety_orders_count']))
            ->td(create_dropdown_options(['fixed' , 'percentage'] ,  'size_type_bots_'.$bot['id'] , 'size_type_bots' , 'bot_'.$bot['id'] , $size_type))
            ->td(create_input_float('bo_size_bots_'.$bot['id'] , 'bo_size_bots' , 'bot_'.$bot['id'] , $bot['base_order_volume']))
            ->td(create_input_float('so_size_bots_'.$bot['id'] , 'so_size_bots' , 'bot_'.$bot['id'] , $bot['safety_order_volume']))
            ->td(create_input_float('so_perc_bots_'.$bot['id'] , 'so_perc_bots' , 'bot_'.$bot['id'] , $bot['safety_order_step_percentage']))
            ->td(create_input_float('so_volume_bots_'.$bot['id'] , 'so_volume_bots' , 'bot_'.$bot['id'] , $bot['martingale_volume_coefficient']))
            ->td(create_input_float('so_step_bots_'.$bot['id'] , 'so_step_bots' , 'bot_'.$bot['id'] , $bot['martingale_step_coefficient'] ))
            ->td(create_input_float('tp_bots_'.$bot['id'] , 'tp_bots' , 'bot_'.$bot['id'] , $bot['take_profit'] , 0.05))
            ->td(create_dropdown_options([0,1] ,  'ttp_bots_'.$bot['id'] , 'ttp_bots' , 'bot_'.$bot['id'] , $bot['trailing_enabled']))
            ->td(create_input_float('ttp_deviation_bots_'.$bot['id'] , 'ttp_deviation_bots' , 'bot_'.$bot['id'] , $bot['trailing_deviation'] , 0.01))
            ->td(create_input_number('cooldown_bots_'.$bot['id'] , 'cooldown_bots' , 'bot_'.$bot['id'] , $bot['cooldown']))
            ->td(create_dropdown_options(['cross' , 'isolated'] ,  'lev_type_bots_'.$bot['id'] , 'lev_type_bots' , 'bot_'.$bot['id'] , $bot['leverage_type']))
            ->td(create_dropdown_number(0 , 125 ,  'lev_value_bots_'.$bot['id'] , 'lev_value_bots' , 'bot_'.$bot['id'] , $bot['leverage_custom_value']))
            ->td(create_dropdown_options([0 ,1] ,  'is_enabled_bots_'.$bot['id'] , 'is_enabled_bots' , 'bot_'.$bot['id'] , $bot['is_enabled']));
    }

    echo $table->getTable();

    echo '</form>';

}


/**
 * Change all bots
 */
if($action == 'change_bots') {

    $internal_account_id = $_POST['account_id'];
    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $post_data = $_POST;

    $keys = array_keys($post_data);

    // Get all the bot ids
    foreach ($keys as $key) {
        $bot_ids[] = end(preg_split("/_/",$key));     
    }

    $bot_ids = array_unique($bot_ids);



    $xcommas = new MC3Commas\threeCommas(BASE_URL , $account_info['api_key'] , $account_info['api_secret']); 
    $bots = $xcommas->get_all_bots(['account_id' => $account_info['bot_account_id'] , 'limit' => 100]);
    $errors = 0;
    foreach ($bots as $bot) {

        // Double check if the bot id from 3commas is in the POST array
        if(in_array($bot['id'] , $bot_ids)) {

            if($_POST['size_type_bots_'.$bot['id']] == 'percentage') {
                $size_type = 'percent';
            } else {
                $size_type = 'quote_currency';
            }

            $data = [
                // General
                'name' => $bot['name'],
                'pairs' => json_encode($bot['pairs']),
                'start_order_type' => $_POST['so_type_bots_'.$bot['id']], 
                'strategy_list' => json_encode(['strategy' => $bot['strategy_list'][0]['strategy']]),
                'bot_id' => $bot['id'],
                'is_enabled' => $_POST['is_enabled_bots_'.$bot['id']],
                
                 // Volumes

                'base_order_volume' => $_POST['bo_size_bots_'.$bot['id']],
                'safety_order_volume' => $_POST['so_size_bots_'.$bot['id']],
                'safety_order_volume_type' => $size_type,
                'base_order_volume_type' => $size_type,

                // SO paramaters
                'safety_order_step_percentage' => $_POST['so_perc_bots_'.$bot['id']], 
                'max_safety_orders' => $_POST['max_so_bots_'.$bot['id']], 
                'active_safety_orders_count' => $_POST['act_so_bots_'.$bot['id']], 
                'martingale_volume_coefficient' => $_POST['so_volume_bots_'.$bot['id']], 
                'martingale_step_coefficient' => $_POST['so_step_bots_'.$bot['id']], 
                
                // Profit types
                'take_profit_type' => $bot['take_profit_type'],
                'take_profit' => $_POST['tp_bots_'.$bot['id']],
                'trailing_enabled' => $_POST['ttp_bots_'.$bot['id']], 
                'trailing_deviation' => $_POST['ttp_deviation_bots_'.$bot['id']], 

                'cooldown' => $_POST['cooldown_bots_'.$bot['id']],       

                // Leverage parameters
                'leverage_type' =>  $_POST['lev_type_bots_'.$bot['id']], //$bot['leverage_type'] ,
                'leverage_custom_value' => $_POST['lev_value_bots_'.$bot['id']], //$bot['leverage_custom_value'],
            ];
            
            if ($_POST['is_enabled_bots_'.$bot['id']] == "1") {
                $xcommas->enable_bot($bot['id']);
            } 
            if ($_POST['is_enabled_bots_'.$bot['id']] == "0") {
                $xcommas->disable_bot($bot['id']);
            }  

            try {
                $update = $xcommas->update_bot($bot['id'] , $data);
            } catch (Exception $e) {

                // If the update fails we want to disable the bot , in this way we are sure we don't overleverage
                $xcommas->disable_bot($bot['id']);

                $dataMapper->insert_log($account_info['bot_account_id'] , 0 , '' , 'Not able to update bot settings and disabling bot for '.$bot['name'].' with message : '.$e->getMessage());
                //echo $e->getMessage();
                $errors++;
            }    
        }
    } 

      
    if ($errors > 0) {
        echo '<span style="color : red">Not able to update all bots, please refer to the logbook for more details. </span><br />';
    }
    echo 'Bots updated. Please review your settings on 3Commas!';
}