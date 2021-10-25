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


// Sent telegram test message
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