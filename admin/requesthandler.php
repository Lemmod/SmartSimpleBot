<?php
error_reporting(E_ERROR);
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php?response=notloggedin');
	die;
}

include ('../app/Config.php');
include ('../app/Core.php');
include ('../app/3CommasConnector.php');
include ('../app/DataMapper.php');
include ('../app/DataReader.php');
include ('../app/Table.php');
include ('../app/functions.php');

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

            $stats_usdt = '$ '.number_format($stats['today_stats']['USDT'],2); ;
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
        $account_response[$i]['daily_usdt_profit'] = $stats_usdt;
        $account_response[$i]['open_deals'] = $deals_running;
 
        $i++;
    }

    $table = new STable();
    $table->class = 'table table-hover table-striped table-bordered';
    $table->width = '100%';

    $table->thead()
        ->th('Account ID')
        ->th('Name')
        ->th('Max deals')
        ->th('BO/SO Size')
        ->th('Manage bots')
        ->th('Status')
        ->th('TV Alerts')
        ->th('Telegram')
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

        $table->tr()
        ->td($response['3c_id'])
        ->td($response['internal_name'])
        ->td(create_dropdown_number_with_id(0 , 20 , 'mad_dropdown' , 'mad_dropdown' , 'account_'.$response['internal_id'] , $response['mad']))
        ->td(create_dropdown_number_with_id(5 , 200 , 'bo_size' , 'bo_size' , 'account_'.$response['internal_id'] , $response['bo_size']))
        ->td('<a class="manage_bots_link" id="mbots_'.$response['internal_id'].'"><i class="fas fa-robot"></i> Manage bots </a>')
        ->td($switch)
        ->td('<a class="tv_alerts_link" id="account_'.$response['internal_id'].'"><i class="fas fa-chart-bar"></i>  Trading View alerts</a>')
        ->td('<a class="telegram_settings_link" id="account_'.$response['internal_id'].'"><i class="fas fa-comment-dots"></i>  Telegram settings</a> | <a class="test_message_link" id="sentmsg_'.$response['internal_id'].'"><i class="fas fa-paper-plane"></i> Test msg.</a>')
        ->td('<a class="logbook_link" id="account_'.$response['internal_id'].'"><i class="fas fa-book"></i>  Logbook</a>')
        ->td('<a class="delete_account_link" id="account_'.$response['internal_id'].'"><i class="fas fa-trash"></i>  Delete</a>')
        ->td($response['open_deals'])
        ->td($response['daily_usdt_profit']);
    }

    echo $table->getTable();

}

/**
 * Add an account
 */
if($action == 'add_account') {

    $dataMapper->insert_account($_SESSION['user_id'] , $_REQUEST['bot_account_id'] , $_REQUEST['account_name'] , $_REQUEST['api_key'] , $_REQUEST['api_secret']);

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
            ->td($bot['id'].' ('.$bot['name'].')')
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

if($action == 'load_tv_alerts') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $xcommas = new MC3Commas\threeCommas(BASE_URL , $account_info['api_key'] , $account_info['api_secret']); 
    $bots = $xcommas->get_all_bots(['account_id' => $account_info['bot_account_id'] , 'limit' => 100]);

    array_multisort(array_column($bots, 'created_at'),  SORT_DESC , $bots);

    echo '<h2> Trading view alerts </h2>';

    echo '<h3> General alerts</h2>';

    $table = new STable();
    $table->class = 'table table-hover table-striped table-bordered';
    $table->width = '100%';

    $table->thead()
    ->th('Type:')
    ->th('Alert');



    $table->tr()
    ->td('Enable account')
    ->td(json_encode(['account_id' => (int)$account_info['bot_account_id'] , 'message' => 'enable_bots']  ));

    $table->tr()
    ->td('Disable account')
    ->td(json_encode(['account_id' => (int)$account_info['bot_account_id'] , 'message' => 'disable_bots']  ));

    $table->tr()
    ->td('Check open deals (uses Telegram)')
    ->td(json_encode(['account_id' => (int)$account_info['bot_account_id'] , 'message' => 'check_open_deals']  ));

    echo $table->getTable();

    $table = '';
         
    
    echo '<h3> Bot specific alerts</h2>';

    $table = new STable();
    $table->class = 'table table-hover table-striped table-bordered';
    $table->id = 'bot_spec_alerts';
    $table->width = '100%';

    $table->thead()
        ->th('Bot (name):')
        ->th('Created :')
        ->th('Pair :')
        ->th('Alert');
    
   
    foreach ($bots as $bot) {

        //$bot['id'] = '87654321';

        $result['account_id'] = (int)$account_info['bot_account_id'];
        $result['bot_id'] = (int)$bot['id'];
        $result['pair'] = $bot['pairs'][0];

        $table->tr()
            ->td($bot['id']. ' ('.$bot['name'].')')
            ->td($bot['created_at'])
            ->td($bot['pairs'][0])
            ->td('<span class="copy_text">'.json_encode($result).'</span>');
    }

    echo $table->getTable();

}

if($action == 'load_telegram_settings') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    $settings = $dataReader->get_account_settings($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    if($settings['notify_telegram'] == 1) {
        $checked = 'checked';
    } else {
        $checked = '';
    }

    echo '<h2> Telegram settings </h2>';

    echo '<form method="POST" id="change_telegram_settings" onsubmit="return a_change_telegram_settings();">';

    echo '<input type="hidden" name="account_id" value="'.$internal_account_id.'"/>';

    echo '<div class="field">
            <label> Use telegram : </label>
            <input type="checkbox" name="notify_telegram" '.$checked.' id="notify_telegram" /> 
        </div>';

    echo '<div class="field">
        <label> Telegram bot hash : </label>
        <input type="text" name="telegram_bot_hash" value="'.$settings['telegram_bot_hash'].'" />
    </div>';

    echo '<div class="field">
        <label> Telegram Chat-ID  </label>
        <input type="text" name="telegram_chat_id" value="'.$settings['telegram_chat_id'].'" />
    </div>';
    
    echo '<input type="submit" value="Save settings" />';
    echo '</form>';

}

if($action == 'load_logbook') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);  

    $log_data = $dataReader->get_logbook($account_info['bot_account_id']);

    echo '<h2> Logbook </h2>';

    $table = new STable();
    $table->class = 'table table-hover table-striped table-bordered';
    $table->id = 'logbook_table';
    $table->width = '100%';

    $table->thead()
    ->th('Date / time :')
    ->th('Pair :')
    ->th('Message :');

    foreach($log_data as $log) {
        $table->tr()
        ->td($log['timestamp'])
        ->td($log['pair'])
        ->td($log['message']);

    }

    echo $table->getTable();
}

if($action == 'load_debuglog') {

    $log_data = $dataReader->get_debuglog();

    
    echo '<h2> Debug log </h2>';

    $table = new STable();
    $table->class = 'table table-hover table-striped table-bordered';
    $table->id = 'debug_table';
    $table->width = '100%';

    $table->thead()
    ->th('Date / time :')
    ->th('Jobs :')
    ->th('Alerts :')
    ->th('Calls to 3C :')
    ->th('Calls / Alerts :')
    ->th('Errors :')
    ->th('Avg. job time :')
    ->th('Max. job time :')
    ->th('Exceed 15 secs :')
    ->th('Exceed 30 secs :');

    foreach($log_data as $log) {
        $table->tr()
        ->td($log['time'])
        ->td($log['jobs'])
        ->td($log['alerts'])
        ->td($log['calls'])
        ->td($log['average_calls'])
        ->td($log['errors'])
        ->td($log['avg_job_time'])
        ->td($log['max_job_time'])
        ->td($log['exceed_15s'])
        ->td($log['exceed_30s']);

    }

    echo $table->getTable();
}

if($action == 'sent_telegram_msg') {

    

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    $settings = $dataReader->get_account_settings($internal_account_id);


    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $telegram_bot_hash = $settings['telegram_bot_hash'];
    $telegram_chat_id = $settings['telegram_chat_id'];
    $msg = 'Account : '.$account_info['account_name'].'. This is a test message from Smart Simple Bot';

    telegram($telegram_bot_hash , $telegram_chat_id , $msg);

    echo 'Test message sent to Telegram. Check the response';

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
                   

            $update = $xcommas->update_bot($bot['id'] , $data);

        }
    } 

    echo 'Bots updated. Please review your settings on 3Commas!';
}

// Change telegram settings
if($action == 'change_telegram_settings') {

    $internal_account_id = $_POST['account_id'];
    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    if($_POST['notify_telegram'] == 'on') {
        $notify = 1;
    } else {
        $notify = 0;
    }

    $dataMapper->update_telegram_settings($internal_account_id , $notify , $_POST['telegram_bot_hash'] , $_POST['telegram_chat_id']);

    echo 'Telegram settings updated.';
}
?> 
