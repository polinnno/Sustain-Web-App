<?php
session_start();

// Unset all the session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect the user back to the home page or any other page
header("Location: home.php");
exit();

