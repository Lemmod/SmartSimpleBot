<?php
/*****

(c) 2021 - Lemmod

*/

$response = $_GET['response'];

switch($response) {
    case "notloggedin":
        $message = "Not logged in , provide username and password";
        break;
    case "wrongcredentials":
        $message = "Wrong username / password";
        break;
    case "incorrect_ajax_call":
        $message = "User tried to change other users paramaters, not allowed";
        break;
}


?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Simple Smart Bot</title>
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
        <link rel="stylesheet" href="css/style.css">
	</head>
	<body>
		<div class="login">
			<h1>Login</h1>
			<form action="authenticate.php" method="post">
				<label for="username">
					<i class="fas fa-user"></i>
				</label>
				<input type="text" name="username" placeholder="Username" id="username" required>
				<label for="password">
					<i class="fas fa-lock"></i>
				</label>
				<input type="password" name="password" placeholder="Password" id="password" required>
				<input type="submit" value="Login">
			</form>

            <div class="message">
                <?php 
                if(isset($message)) { 
                    echo $message; 
                }
                ?>
            </div>
		</div>
	</body>
</html>