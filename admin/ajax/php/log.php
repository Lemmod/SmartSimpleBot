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

if($action == 'load_logbook') {

    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);  

    $log_data = $dataReader->get_logbook($account_info['bot_account_id'] , 1);
    $log_data_system = $dataReader->get_logbook($account_info['bot_account_id'] , 1 , 1);

    echo '<h2> Deal messages </h2>';

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

    echo '<h2> System messages </h2>';

    $table = new STable();
    $table->class = 'table table-hover table-striped table-bordered';
    $table->id = 'logbook_table_system';
    $table->width = '100%';

    $table->thead()
    ->th('Date / time :')
    ->th('Message :');

    foreach($log_data_system as $log) {
        $table->tr()
        ->td($log['timestamp'])
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
