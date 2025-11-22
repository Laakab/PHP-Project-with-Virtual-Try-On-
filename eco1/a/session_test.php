<?php
// Test to verify session handling works correctly
session_start();

// Include admin functions
require_once 'AdminPanel_functions.php';

echo "<h1>Session Test</h1>";

// Test if session functions work without warnings
echo "<h3>Testing Session Functions:</h3>";
echo "<pre>";

// Test login function
$loginResult = adminLogin('admin', 'admin123');
echo "Login test: " . ($loginResult ? 'Success' : 'Failed') . "\n";

// Test session check
echo "Is admin logged in: " . (isAdminLoggedIn() ? 'Yes' : 'No') . "\n";

// Test admin details
$adminDetails = getAdminDetails();
echo "Admin details: " . print_r($adminDetails, true) . "\n";

echo "</pre>";

echo "<p>If you see this page without any session warnings, the fix was successful!</p>";

echo "<p><a href='test.php'>Go to full test page</a> | <a href='logout.php'>Logout</a></p>";

?>