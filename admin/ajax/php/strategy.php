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

if($action  == 'load_strategies') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    $strategies = $dataReader->get_strategies($internal_account_id);
    $time_frames = $dataReader->get_time_frames($internal_account_id);

    // Load all strategies
    echo '<h2> Strategies </h2>';

    $table = new STable();
    $table->class = 'table table-hover table-striped table-bordered';
    $table->id = 'debug_table';
    $table->width = '100%';

    $table->thead()
    ->th('Name')
    ->th('Max active deals')
    ->th('Actions');
   
    foreach($strategies as $strategy) {

        
        $table->tr('stratrow_account_'.$internal_account_id.'_strat_'.$strategy['strategy_id']);

        $set_bots = $dataReader->get_set_bots($strategy['strategy_id']);

        $message = '';
        if ($set_bots == 0) {
            $message = '<span style="color : red"> <strong>!</strong> No bot settings found for this strategy. When changing to this strategy bot settings will remain unchanged , please choose "Manage bots" to set it for this strategy </span>';
        }

        if($strategy['locked']) {
            $table->td($strategy['strategy_name']);
            $table->td($strategy['max_active_deals']);
            $table->td('All alerts will be disabled. No change on 3commas will be made for this setting.');
        } else {  
            $table->td('<input type="text" id="account_'.$internal_account_id.'_strat_'.$strategy['strategy_id'].'" class="strat_update" value="'.$strategy['strategy_name'].'" />');
            $table->td(create_dropdown_number_with_id(0 , 20 , 'mad_dropdown_strategy' , 'mad_dropdown_strategy' , 'account_'.$internal_account_id.'_strategy_'.$strategy['strategy_id'] , $strategy['max_active_deals']));         
            $table->td('<a class="manage_bots_strategy_link" id="mbots_'.$internal_account_id.'_strat_'.$strategy['strategy_id'].'"><i class="fas fa-robot"></i> Manage bots </a> | <a class="delete_strategy_link" id="account_'.$internal_account_id.'_strat_'.$strategy['strategy_id'].'"><i class="fas fa-trash"></i> Delete </a> '.$message);
        } 

        
    }

    $tf_id = $dataReader->get_time_frame_id($internal_account_id , 'med_tf');
   

    // Load all timeframes
    echo $table->getTable();

    echo '<i class="fas fa-plus"></i> <a class="add_strategy_link" id="account_'.$internal_account_id.'"> Add strategy</a>';

    echo '<h2> Time frames </h2>';

    $table = new STable();
    $table->class = 'table table-hover table-striped table-bordered';
    $table->id = 'debug_table';
    $table->width = '100%';

    $direction_options = ['long_short' => 'long' , 'short_long' => 'short' , 'both' => 'both'];

    $table->thead()
    ->th('Label')
    ->th('Description')
    ->th('Validation')
    ->th('Actions');

    foreach($time_frames as $time_frame) {

        $table->tr()
        ->td('<input type="text" id="account_'.$internal_account_id.'_timeframe_'.$time_frame['time_frame_id'].'" class="tf_update" value="'.$time_frame['label'].'" />')
        ->td('<input type="text" id="account_'.$internal_account_id.'_timeframe_'.$time_frame['time_frame_id'].'" class="tf_desc_update" value="'.$time_frame['description'].'" />')
        ->td('Valid for <input size="2" type="text" id="account_'.$internal_account_id.'_timeframe_'.$time_frame['time_frame_id'].'" class="tf_time_update" value="'.$time_frame['validation_time'].'" /> minutes in the direction '.create_dropdown_options($direction_options, '' , 'tf_direction_update' , 'account_'.$internal_account_id.'_timeframe_'.$time_frame['time_frame_id'] , $time_frame['validation_direction'] , true))
        ->td('<a class="delete_time_frame_link" id="account_'.$internal_account_id.'_timeframe_'.$time_frame['time_frame_id'].'"><i class="fas fa-trash"></i> Delete </a> | <a class="force_time_frame_link" id="account_'.$internal_account_id.'_timeframe_'.$time_frame['time_frame_id'].'_side_long"> Force <span style="color: green">Long</span></a> | <a class="force_time_frame_link" id="account_'.$internal_account_id.'_timeframe_'.$time_frame['time_frame_id'].'_side_short">Force <span style="color: red">Short</span></a>');
  
    }

    echo $table->getTable();

    echo '<i class="fas fa-plus"></i> <a class="add_timeframe_link" id="account_'.$internal_account_id.'"> Add timeframe</a>';

    // Load the strategy matrix
    echo '<h2> Smart Strategy Matrix </h2>';

    $strategy_settings = $dataReader->get_strategy_settings($internal_account_id);

    $table = new STable();
    $table->class = 'table table-hover table-striped table-bordered';
    $table->id = 'debug_table';
    $table->width = '100%';

    $table->thead()
    ->th('Strategy Name');

    foreach($time_frames as $time_frame ) {
        $table->th($time_frame['label']);
    }
    $table->th('Actions');

    $strategies = $dataReader->get_strategies($internal_account_id);

    $strat_list = [];
    foreach ($strategies as $strat) {
        $strat_list[$strat['strategy_id']] = $strat['strategy_name'];
    }

     
    foreach ($strategy_settings as $strat_set) {
        $table->tr();

        $table->td(create_dropdown_options($strat_list, '' , 'strat_setting_mode' , 'account_'.$internal_account_id.'_combination_'.$strat_set['combination_id'] ,$strat_set['strategy_id'] ,  true));

        //$table->td($strat_set['strategy_name']);

        foreach($time_frames as $time_frame ) {

            $type = $dataReader->get_strategy_timeframe_type($strat_set['combination_id'] , $time_frame['time_frame_id']);
            //create_dropdown_options(['short','long'] , 'strategy_setting_type' , 'strategy_setting_type', 'account_'.$internal_account_id.'_combination_'.$strat_set['combination_id'].'_timeframe_'.$time_frame['time_frame_id'] , $type['type'])
            
            $checked = $type['type'] == 'long' ? 'checked' : '';
            $id = 'account_'.$internal_account_id.'_combination_'.$strat_set['combination_id'].'_timeframe_'.$time_frame['time_frame_id'];

            $checkbox = '<label class="switch">
                            <input class="strategy_setting_type" id="'.$id.'" type="checkbox" '.$checked.' id="togBtn">
                            <div class="slider round">
                                <span class="on">Long</span>
                                <span class="off">Short</span>
                            </div>
                        </label>';
            
            $table->td($checkbox);
        }

        $table->td('<a class="delete_matrix_row_link" id="account_'.$internal_account_id.'_combination_'.$strat_set['combination_id'].'"><i class="fas fa-trash"></i> Delete </a>');
    }

    echo $table->getTable();

    echo '<i class="fas fa-plus"></i> <a class="add_matrix_link" id="account_'.$internal_account_id.'"> Add row to the matrix</a>';
}

/**
 * Update max acticve deals strategy
 */
if($action == 'add_strategy') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $result = $dataMapper->insert_strategy($internal_account_id);

}

/**
 * Update max acticve deals strategy
 */
if($action == 'add_timeframe') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $time_frame_id = $dataMapper->insert_timeframe($internal_account_id);

    $dataMapper->insert_strategy_settings_timeframe_id($time_frame_id , $internal_account_id);
}

/**
 * Update max acticve deals strategy
 */
if($action == 'force_timeframe_status') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];
    $tf_id = $explode[3];
    $type = $explode[5];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $dataMapper->insert_timeframe_status($account_info['internal_account_id'] , $tf_id , $type);
}

/**
 * Update max acticve deals strategy
 */
if($action == 'add_matrix_row') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $dataMapper->insert_strategy_settings($internal_account_id);

}



/**
 * Update max acticve deals strategy
 */
if($action == 'update_max_active_deals_strategy') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];
    $strategy_id = $explode[3];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $dataMapper->update_max_active_deals_strategy($strategy_id , $_REQUEST['deals']);
}

/**
 * Update time_frame_settings
 */
if($action == 'update_strategy_settings') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];
    $combination_id = $explode[3];
    $time_frame_id = $explode[5];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $dataMapper->update_strategy_settings($combination_id , $time_frame_id , $_REQUEST['type']);
}

/**
 * Update strategy name
 */
if($action == 'update_strat_name') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];
    $strategy_id = $explode[3];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $dataMapper->update_strat_name($strategy_id , $_REQUEST['name']);
}

/**
 * Update time_frame_settings
 */
if($action == 'update_tf_label') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];
    $time_frame_id = $explode[3];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $dataMapper->update_tf_label($time_frame_id , $_REQUEST['label']);
}

/**
 * Update time_frame_settings
 */
if($action == 'update_tf_description') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];
    $time_frame_id = $explode[3];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $dataMapper->update_tf_dscription($time_frame_id , $_REQUEST['description']);
}

/**
 * Update time_frame validation time
 */
if($action == 'update_tf_valid_time') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];
    $time_frame_id = $explode[3];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $dataMapper->update_tf_valid_time($time_frame_id , $_REQUEST['min']);
}

/**
 * Update time_frame validation direction
 */
if($action == 'update_tf_valid_direction') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];
    $time_frame_id = $explode[3];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $dataMapper->update_tf_valid_direction($time_frame_id , $_REQUEST['direction']);
}



if($action == 'update_strat_setting_mode') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];
    $combination_id = $explode[3];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    $strategy_id = $_REQUEST['value'];

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $dataMapper->update_strat_setting_strategy($combination_id , $strategy_id);

}

/**
 * Delete strategy
 */
if($action == 'delete_strategy') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];
    $strategy_id = $explode[3];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $dataMapper->delete_strategy($strategy_id);

    echo 'Strategy deleted';
}

/**
 * Delete strategy
 */
if($action == 'delete_time_frame') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];
    $time_frame_id = $explode[3];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $dataMapper->delete_time_frame($time_frame_id);

    echo 'Time frame deleted';
}

/**
 * Delete strategy
 */
if($action == 'delete_matrix_row') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];
    $combination_id = $explode[3];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);



    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $dataMapper->delete_matrix_row($combination_id);

    echo 'Row deleted';
}

/**
 * Load all bots on account
 */
if($action == 'load_bots_strategy') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];
    $strategy_id = $explode[3];

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

    echo '<div id="manage_bots_strategy">';

    echo '<h2> Manage bots </h2>';

    echo '<form method="POST" id="change_bots_strategy" onsubmit="return a_change_bots_strategy();">';

    echo '<input type="submit" name="submit_form" class="submit_mb" value="Save all changes">';
    echo '<input type="hidden" name="account_id" value="'.$internal_account_id.'"/>';
    echo '<input type="hidden" name="strategy_id" value="'.$strategy_id.'"/>';
   
         
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

  

    $table->tr('global_header_strategies')
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
        ->td(create_dropdown_options([0,1] , '' , 'ttp_all' , '' , '0'))
        ->td(create_input_float('' , 'ttp_deviation_all' , '' , ''))
        ->td(create_input_number('' , 'cooldown_all' , '' , ''))
        ->td(create_dropdown_options(['cross' , 'isolated'] , '' , 'lev_type_all' , 'cross' , ''))
        ->td(create_dropdown_number(0 , 125 , '' , 'lev_value_all' , '' , '0'))
        ->td(create_dropdown_options([0,1] , '' , 'is_enabled_all' , '' , '0'));

    

    foreach ($bots as $bot) {

        
        $bot_info = $dataReader->get_bot_settings($bot['id'] , $strategy_id);

        $bot_setting = array();
        foreach ($bot_info as $bi) {
            $bot_setting[$bi['label']] = $bi['value'];
        }

        
        if($bot_setting['base_order_volume_type'] == 'percent') {
            $size_type = 'percentage';
        } else {
            $size_type = 'fixed';
        }


        $table->tr()
            ->td($bot['name'])
            ->td($bot['pairs'][0])
            ->td(create_dropdown_options(['limit' , 'market'] , 'so_type_bots_'.$bot['id'] , 'so_type_bots' , 'bot_'.$bot['id'] , $bot_setting['start_order_type']))
            ->td(create_input_number('max_so_bots_'.$bot['id'] , 'max_so_bots' , 'bot_'.$bot['id'] , $bot_setting['max_safety_orders']))
            ->td(create_input_number('act_so_bots_'.$bot['id'] , 'act_so_bots' , 'bot_'.$bot['id'] , $bot_setting['active_safety_orders_count']))
            ->td(create_dropdown_options(['fixed' , 'percentage'] ,  'size_type_bots_'.$bot['id'] , 'size_type_bots' , 'bot_'.$bot_setting['id'] , $size_type))
            ->td(create_input_float('bo_size_bots_'.$bot['id'] , 'bo_size_bots' , 'bot_'.$bot['id'] , $bot_setting['base_order_volume']))
            ->td(create_input_float('so_size_bots_'.$bot['id'] , 'so_size_bots' , 'bot_'.$bot['id'] , $bot_setting['safety_order_volume']))
            ->td(create_input_float('so_perc_bots_'.$bot['id'] , 'so_perc_bots' , 'bot_'.$bot['id'] , $bot_setting['safety_order_step_percentage']))
            ->td(create_input_float('so_volume_bots_'.$bot['id'] , 'so_volume_bots' , 'bot_'.$bot['id'] , $bot_setting['martingale_volume_coefficient']))
            ->td(create_input_float('so_step_bots_'.$bot['id'] , 'so_step_bots' , 'bot_'.$bot['id'] , $bot_setting['martingale_step_coefficient'] ))
            ->td(create_input_float('tp_bots_'.$bot['id'] , 'tp_bots' , 'bot_'.$bot['id'] , $bot_setting['take_profit'] , 0.05))
            ->td(create_dropdown_options([0,1] ,  'ttp_bots_'.$bot['id'] , 'ttp_bots' , 'bot_'.$bot['id'] , $bot_setting['trailing_enabled']))
            ->td(create_input_float('ttp_deviation_bots_'.$bot['id'] , 'ttp_deviation_bots' , 'bot_'.$bot['id'] , $bot_setting['trailing_deviation'] , 0.01))
            ->td(create_input_number('cooldown_bots_'.$bot['id'] , 'cooldown_bots' , 'bot_'.$bot['id'] , $bot_setting['cooldown']))
            ->td(create_dropdown_options(['cross' , 'isolated'] ,  'lev_type_bots_'.$bot['id'] , 'lev_type_bots' , 'bot_'.$bot['id'] , $bot_setting['leverage_type']))
            ->td(create_dropdown_number(0 , 125 ,  'lev_value_bots_'.$bot['id'] , 'lev_value_bots' , 'bot_'.$bot['id'] , $bot_setting['leverage_custom_value']))
            ->td(create_dropdown_options([0 ,1] ,  'is_enabled_bots_'.$bot['id'] , 'is_enabled_bots' , 'bot_'.$bot['id'] , $bot_setting['is_enabled']));

    }

    echo $table->getTable();

    echo '</form>';

    echo '</div>';

}

/**
 * Change all bots
 */
if($action == 'update_bots_strategy') {

    $internal_account_id = $_POST['account_id'];
    $strategy_id = $_POST['strategy_id'];
    
    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $post_data = $_POST;

    $keys = array_keys($post_data);

    $dataMapper->delete_bot_settings($strategy_id);


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
                'enabled' => $_POST['is_enabled_bots_'.$bot['id']]
            ];

            $dataMapper->insert_bot_strategy($strategy_id , $bot['id'] , $data);

        }
    } 

    echo 'Bots updated for the specific strategy!';
}