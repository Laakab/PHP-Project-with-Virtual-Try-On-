<?php
// Test the adminLogin function
session_start();

// Include admin functions
require_once 'AdminPanel_functions.php';

echo "<h1>Admin Login Function Test</h1>";

// Test 1: Correct credentials
echo "<h3>Test 1: Correct Credentials</h3>";
echo "<pre>";
$loginResult = adminLogin('admin', 'admin123');
echo "Login with 'admin'/'admin123': " . ($loginResult ? 'SUCCESS' : 'FAILED') . "\n";
echo "Session admin_logged_in: " . (isset($_SESSION['admin_logged_in']) ? $_SESSION['admin_logged_in'] : 'not set') . "\n";
echo "Session admin_name: " . (isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'not set') . "\n";
echo "</pre>";

// Logout first to test wrong credentials
adminLogout();

// Test 2: Wrong credentials
echo "<h3>Test 2: Wrong Credentials</h3>";
echo "<pre>";
$loginResult = adminLogin('wrong', 'wrong');
echo "Login with 'wrong'/'wrong': " . ($loginResult ? 'SUCCESS' : 'FAILED') . "\n";
echo "Session admin_logged_in: " . (isset($_SESSION['admin_logged_in']) ? $_SESSION['admin_logged_in'] : 'not set') . "\n";
echo "</pre>";

echo "<h3>Test Results:</h3>";
if (adminLogin('admin', 'admin123')) {
    echo "<p style='color: green;'>✅ Admin login function is working correctly!</p>";
    echo "<p>Now you can: <a href='AdminPanel.php'>Go to Admin Panel</a> | <a href='logout.php'>Logout</a></p>";
} else {
    echo "<p style='color: red;'>❌ Admin login function is not working properly.</p>";
}

?>