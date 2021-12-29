<?php
/*****

(c) 2021 - Lemmod

*/
session_start();
session_destroy();
// Redirect to the login page:
$response = $_GET['response'];
header('Location: index.php?response='.$response);
?>