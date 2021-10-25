<?php
session_start();
// Change this to your connection info.
include ('../app/Config.php');
include ('../app/Core.php');
include ('../app/3CommasConnector.php');
include ('../app/DataMapper.php');
include ('../app/DataReader.php');
include ('../app/functions.php');

$dataReader = new DataReader();

$user_name = $_POST['username'];
$password = $_POST['password'];


// Now we check if the data from the login form was submitted, isset() will check if the data exists.
if ( !isset($user_name , $password) ) {
	// Could not get the data that should have been sent.
	die('Please fill both the username and password fields!');
}

$user_credentials = $dataReader->get_user_credentials($user_name);

//var_dump(count($user_credentials));

if (count($user_credentials) > 1) {

    if (password_verify($password, $user_credentials['password'])) {
        // Create sessions, so we know the user is logged in, they basically act like cookies but remember the data on the server.
        session_regenerate_id();
        $_SESSION['loggedin'] = TRUE;
        $_SESSION['name'] = $user_name;
        $_SESSION['user_id'] = $user_credentials['user_id'];
        header('Location: admin_home.php');
    } else {
        // Incorrect password
        header('Location: index.php?response=wrongcredentials');
    }
} else {
    // Incorrect username
    header('Location: index.php?response=wrongcredentials');
}
?>