<?php
// Test script to verify PHP functionality
session_start();

// Include admin functions
require_once 'AdminPanel_functions.php';

echo "<h1>Admin Panel PHP Conversion Test</h1>";
echo "<h2>Session Status:</h2>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive') . "\n";
echo "Admin Logged In: " . (isAdminLoggedIn() ? 'Yes' : 'No') . "\n";
echo "</pre>";

echo "<h2>Testing Admin Functions:</h2>";

// Test login function
echo "<h3>Testing Login:</h3>";
echo "<pre>";
$loginResult = adminLogin('admin', 'admin123');
echo "Login with correct credentials: " . ($loginResult ? 'Success' : 'Failed') . "\n";

$loginResult = adminLogin('wrong', 'wrong');
echo "Login with wrong credentials: " . ($loginResult ? 'Success' : 'Failed') . "\n";
echo "</pre>";

// Test admin details
echo "<h3>Admin Details:</h3>";
echo "<pre>";
$adminDetails = getAdminDetails();
echo "Admin Name: " . $adminDetails['name'] . "\n";
echo "Admin Email: " . $adminDetails['email'] . "\n";
echo "Admin ID: " . $adminDetails['id'] . "\n";
echo "</pre>";

// Test stats
echo "<h3>Admin Stats:</h3>";
echo "<pre>";
$stats = getAdminStats();
foreach ($stats as $key => $value) {
    echo ucfirst(str_replace('_', ' ', $key)) . ": " . $value . "\n";
}
echo "</pre>";

// Test permissions
echo "<h3>Testing Permissions:</h3>";
echo "<pre>";
echo "Can view dashboard: " . (hasPermission('view_dashboard') ? 'Yes' : 'No') . "\n";
echo "Can manage users: " . (hasPermission('manage_users') ? 'Yes' : 'No') . "\n";
echo "Can manage settings: " . (hasPermission('manage_settings') ? 'Yes' : 'No') . "\n";
echo "</pre>";

echo "<h2>Links:</h2>";
echo "<ul>";
echo "<li><a href='Main.php'>Login Page</a></li>";
echo "<li><a href='AdminPanel.php'>Admin Panel</a></li>";
echo "<li><a href='logout.php'>Logout</a></li>";
echo "</ul>";

?>