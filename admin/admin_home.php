<?php
error_reporting(E_ALL);
date_default_timezone_set('Europe/Amsterdam');
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php?response=notloggedin');
	die;
}

include ('../app/Config.php');
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<title>Smart Simple Bot</title>
		
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4-4.0.0/jq-3.2.1/dt-1.10.16/r-2.2.1/datatables.min.css"/>
		<link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
        <link href="css/style.css" rel="stylesheet" type="text/css">
	
		<script type="text/javascript" src="https://cdn.datatables.net/v/bs4-4.0.0/jq-3.2.1/dt-1.10.16/r-2.2.1/datatables.min.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>	
		<script type="text/javascript" src="js/general.js"></script>

		<?php
		// Load all JS files
		$handle = opendir("ajax/js/");
		while (($file = readdir($handle))!== false) {
			if (substr($file , -2 , 2) == 'js') {
				echo '<script type="text/javascript" src="ajax/js/' . $file . '"></script>'.PHP_EOL;
			}
		}
		closedir($handle);
		?>
		

		<script>
		$(document).ready(function(){
			var $loading = $('.loading').hide();
				$(document)
				.ajaxStart(function () {
					$loading.show();
				})
				.ajaxComplete(function(){
					$(".input_float").inputFilter(function(value) {
 						return /^-?\d*[.]?\d{0,2}$/.test(value); 
					}) ,
					$(".input_number").inputFilter(function(value) {
						return /^-?\d*$/.test(value); 
					}),
					$(".strat_update").inputFilter(function(value) {
						return /^[A-Za-z0-9_-]+$/.test(value); 
					}),
					$(".tf_update").inputFilter(function(value) {
						return /^[A-Za-z0-9_-]+$/.test(value); 
					})
				})
				.ajaxStop(function () {
					$loading.hide();
			});

            $( function() {
                $( "#dialog" ).dialog({
                    autoOpen: false
                });
            } );

			$.ajax({ 
				url: "ajax/php/account.php?action=load_all_accounts",
				context: document.body,
				success: function (response) {
                    if (response == 'ERROR_NOT_LOGGED_IN') {
                        location.href = 'logout.php?response=incorrect_ajax_call';
                    } else {
					    $('#accounts').prepend(response);
                    }
				}
			});

			
		});	
		</script>
	</head>
	
	<body class="loggedin">

		<div class="loading">
			<div class="spinner">
			Loading...
			</div>
		</div>

		<nav class="navtop">
			<div>
				<h1>Smart Simple Bot</h1>
                <a class="debug_log_link"><i class="fas fa-bug"></i>Debug log</a>
				<a class="logout_link" href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
				
			</div>
		</nav>

		<div class="content">
			<?php 
			if (DEMO_MODE == 1) { 
				echo '<span style="color: red; font-weight: bold;">Running in demo mode.</span>'; 
			} 
			?>
        
            <div class="home hide"><a class="back_home_link"><i class="fas fa-home"></i> Back to home</a></div>

            <div class="workspace">
                <h2>Accounts</h2>

                <div id="accounts">
                    <i class="fas fa-plus"></i>  <a class="add_account_link" ac_id="1"> Add account</a>
                    </div>

                    <div id="dialog" title="Notice">
                </div>
            </div>
               
			<div class="workspace hide">

				<!-- Div for adding an account -->
				<div class="add_account hide">

					<h2> Add an account </h2>
               
					<form method="POST" id="add_account" onsubmit="return a_add_account();">
						<input type="hidden" name="action" id="action" value="add_account" />
                        <div class="field">
							<label> 3commas account ID:  </label>
							<input type="text" name="bot_account_id" id="name" /> 
						</div>
						<div class="field">
							<label> Name:  </label>
							<input type="text" name="account_name" id="name" /> 
						</div>
						<div class="field">
							<label> API Key:  </label>
							<input type="text" name="api_key" id="api_key" />
						</div> 
						<div class="field">
							<label> API Secret: </label>
							<input type="text" name="api_secret" id="api_secret" /> 
						</div>
						<input type="submit" name="submit_form" value="Submit">
					</form>
				</div>

				<!-- Edit account -->
				<div class="manage_bots hide">
                </div>

                <!-- TV Alerts -->
				<div class="tv_alerts hide">
                </div>

                <!-- Telegram settings -->
				<div class="telegram_settings hide">
                </div>

                <!-- Logbook -->
				<div class="logbook hide">
                </div>

                <!-- Debug log -->
				<div class="debug_log hide">
                </div>

				<!-- Strategy overview -->
				<div class="strategies hide">
                </div>

			</div>
		</div>
	</body>
</html>