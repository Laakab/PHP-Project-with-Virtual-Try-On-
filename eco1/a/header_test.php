<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once 'AdminPanel_functions.php';

// Test Case 1: Not logged in, should redirect to Main.php
if (!isAdminLoggedIn()) {
    // This will cause a "headers already sent" warning if there is any output before this.
    // We are expecting this to work correctly now.
    header("Location: Main.php?test_result=redirect_success");
    exit;
}

// Test Case 2: Logged in, should not redirect
echo "If you see this, the redirect was correctly skipped for logged in users.";

?>
<br><br>
<a href="Main.php">Go to Login</a>