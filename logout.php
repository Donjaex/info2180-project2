<?php
session_start(); 

// Destroy the session
session_unset(); // Remove all session variables
session_destroy(); // Destroy the session itself

// Redirect to login page
header("Location: login_index.html");
exit();
?>
