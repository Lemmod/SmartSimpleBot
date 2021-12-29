<?php
/*****

(c) 2021 - Lemmod

Shows all open deals per account on 3Commas (doesn't include exchange only deals)

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

if ($action == 'load_deals') {
    $explode = explode('_' , $_REQUEST['id']);
    $internal_account_id = $explode[1];

    $account_info = $dataReader->get_account_info_internal($internal_account_id);

    // Terminate if the user is nog logged in
    check_credentials($account_info['user_id']);

    $xcommas = new MC3Commas\threeCommas(BASE_URL , $account_info['api_key'] , $account_info['api_secret']); 
    $deals = $xcommas->get_deals(['account_id' => $account_info['bot_account_id'] , 'limit' => 100 ,  'scope' => 'active']);

    $table = new STable();
    $table->class = 'table table-hover table-striped table-bordered';
    $table->id = 'deals_table';
    $table->width = '100%';

    $table->thead()
    ->th('Pair')
    ->th('Created')
    ->th('Position size')
    ->th('Assets')
    ->th('Average price')
    ->th('Current price')
    ->th('Profit')
    ->th('PnL')
    ->th('SO (Compl./Active)');

    foreach ($deals as $deal) {

        if ($deal['actual_usd_profit'] > 0) {
            $color = 'positive';
        } else {
            $color = 'negative';
        }

        $dealinfo = $xcommas->get_deal_safety_orders($deal['id']);

        //$xcommas->cancel_order($deal['id'] , ["order_id" => 97355456730]);

        //pr($dealinfo);

        $table->tr()
        ->td($deal['pair'])
        ->td($deal['created_at'])
        ->td(number_format($deal['bought_volume'],2))
        ->td($deal['bought_amount'])
        ->td(number_format($deal['bought_average_price'],4))
        ->td(number_format($deal['current_price'],4))
        ->td($deal['actual_profit_percentage']. '%' , $color)
        ->td(number_format($deal['actual_usd_profit'],2) , $color)
        ->td($deal['completed_safety_orders_count'].'/'.$deal['active_safety_orders_count'] . ' <span style="font-size: 0.8em;"> m: '.$deal['completed_manual_safety_orders_count'].'/'.($deal['completed_manual_safety_orders_count']+$deal['active_manual_safety_orders']).'</span>');
      
    }

    echo $table->getTable();

}