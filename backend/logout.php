<?php
session_start();

// Clear all session data
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to the public index page
header("Location: ../frontend/index.php");
exit();
