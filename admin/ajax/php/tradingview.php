<?php
/*****

(c) 2021 - Lemmod

Shows all the correct TradingView messages needed to fire them to SSB

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

if($action == 'load_tv_alerts') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];
    $strategy_id = $_REQUEST['strat'];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $xcommas = new MC3Commas\threeCommas(BASE_URL , $account_info['api_key'] , $account_info['api_secret']); 
    $bots = $xcommas->get_all_bots(['account_id' => $account_info['bot_account_id'] , 'limit' => 100]);

    array_multisort(array_column($bots, 'created_at'),  SORT_DESC , $bots);

    echo '<h2> Trading view alerts </h2>';

    echo '<h3> General alerts</h3>';

    $table = new STable();
    $table->class = 'table table-hover table-striped table-bordered';
    $table->width = '100%';

    $account_info['bot_account_id'] = DEMO_MODE ? 654321 : (int)$account_info['bot_account_id'];

    $table->thead()
    ->th('Type:')
    ->th('Alert');



    $table->tr()
    ->td('Enable account')
    ->td(json_encode(['account_id' => $account_info['bot_account_id'] , 'message' => 'enable_bots']  ));

    $table->tr()
    ->td('Disable account')
    ->td(json_encode(['account_id' => $account_info['bot_account_id'] , 'message' => 'disable_bots']  ));

    $table->tr()
    ->td('Check open deals (uses Telegram)')
    ->td(json_encode(['account_id' => $account_info['bot_account_id'] , 'message' => 'check_open_deals']  ));

    echo $table->getTable();

    echo '<h3> Strategy alerts (Update time frames) </h3>';
    echo '<h4> You don\'t have to update your alers when changing the label. The tf_id is the one that triggers the setting</h4>';

    $table = new STable();
    $table->class = 'table table-hover table-striped table-bordered';
    $table->width = '100%';

    $table->thead()
    ->th('Label')
    ->th('Description')
    ->th('Alert short')
    ->th('Alert long');

    $time_frames = $dataReader->get_time_frames($internal_account_id);



    foreach($time_frames as $time_frame) {
        $table->tr()
        ->td($time_frame['label'])
        ->td($time_frame['description'])
        ->td(json_encode(['account_id' => $account_info['bot_account_id'] , 'message' => 'set_tf' , 'tf_id' => $time_frame['time_frame_id'] , 'label' => $time_frame['label'] , 'type' => 'short']))
        ->td(json_encode(['account_id' => $account_info['bot_account_id'] , 'message' => 'set_tf' , 'tf_id' => $time_frame['time_frame_id'] , 'label' => $time_frame['label'] , 'type' => 'long']));
    }


    echo $table->getTable();

    $table = '';
         
    
    echo '<h3> Bot specific alerts</h3>';

    $strategies = $dataReader->get_strategies($internal_account_id , true);

    $strat_list[0] = 'no strategy';
    foreach ($strategies as $strat) {
        $strat_list[$strat['strategy_id']] = $strat['strategy_name'];
    }


    echo '<strong>Show alerts for specific strategy : </strong>' . create_dropdown_options($strat_list, '' , 'alert_strategy' , 'account_'.$internal_account_id , $strategy_id , true);



    $table = new STable();
    $table->class = 'table table-hover table-striped table-bordered';
    $table->id = 'bot_spec_alerts';
    $table->width = '100%';

    $table->thead()
        ->th('Bot (name):')
        ->th('Created :')
        ->th('Pair :')
        ->th('Alert');
        //->th('');

    foreach ($bots as $bot) {

        $bot['id'] = DEMO_MODE ? 123456 : (int)$bot['id'];
        if ($strategy_id == 0) {
            $result['account_id'] = $account_info['bot_account_id'];
            $result['bot_id'] = $bot['id'];
            $result['pair'] = $bot['pairs'][0];
        } else {
            $strat_info = $dataReader->get_strategy_info($strategy_id);

            $result['account_id'] = $account_info['bot_account_id'];
            $result['bot_id'] = $bot['id'];
            $result['pair'] = $bot['pairs'][0];
            $result['strategy_id'] = $strat_info['strategy_id'];
            $result['strategy_name'] = $strat_info['strategy_name'];
        }
        //$result['mode'] = 'safe';

        $table->tr()
            ->td($bot['id']. ' ('.$bot['name'].')')
            ->td($bot['created_at'])
            ->td($bot['pairs'][0])
            ->td(json_encode($result));
            //->td('<button onclick="copyText(\''.json_encode($result).'\')" id="'.$bot['id'].'">Copy</button>');
    }

    echo $table->getTable();

}

?>




