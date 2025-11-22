<?php
// Complete test for the admin login system
session_start();

// Include admin functions
require_once 'AdminPanel_functions.php';

echo "<h1>🧪 Complete Admin Login System Test</h1>";

// Clear any existing session first
session_destroy();
session_start();

echo "<h2>1. Testing Login Function:</h2>";
echo "<pre>";

// Test 1: Correct credentials
echo "Test 1a - Login with 'admin'/'admin123': ";
$loginResult = adminLogin('admin', 'admin123');
echo ($loginResult ? "✅ SUCCESS" : "❌ FAILED") . "\n";

// Logout to test next scenario
adminLogout();

// Test 2: Correct credentials with email
echo "Test 1b - Login with 'admin@crowdzero.com'/'admin123': ";
$loginResult = adminLogin('admin@crowdzero.com', 'admin123');
echo ($loginResult ? "✅ SUCCESS" : "❌ FAILED") . "\n";

// Test 3: Wrong credentials
echo "Test 1c - Login with wrong credentials: ";
$loginResult = adminLogin('wrong', 'wrong');
echo ($loginResult ? "✅ SUCCESS" : "❌ FAILED") . " (Should fail)" . "\n";

echo "</pre>";

echo "<h2>2. Testing Session Management:</h2>";
echo "<pre>";

// Login successfully first
adminLogin('admin', 'admin123');

echo "Is admin logged in: " . (isAdminLoggedIn() ? "✅ Yes" : "❌ No") . "\n";

$adminDetails = getAdminDetails();
echo "Admin name: " . ($adminDetails['name'] ?? 'Not set') . "\n";
echo "Admin email: " . ($adminDetails['email'] ?? 'Not set') . "\n";
echo "Admin ID: " . ($adminDetails['id'] ?? 'Not set') . "\n";

echo "</pre>";

echo "<h2>3. Testing Dashboard Functions:</h2>";
echo "<pre>";

$stats = getAdminStats();
echo "Total sales: $" . number_format($stats['total_sales']) . "\n";
echo "New orders: " . number_format($stats['new_orders']) . "\n";
echo "Products: " . number_format($stats['products']) . "\n";
echo "Customers: " . number_format($stats['customers']) . "\n";

echo "</pre>";

echo "<h2>4. Testing Permissions:</h2>";
echo "<pre>";

echo "Can view dashboard: " . (hasPermission('dashboard') ? "✅ Yes" : "❌ No") . "\n";
echo "Can manage users: " . (hasPermission('users') ? "✅ Yes" : "❌ No") . "\n";
echo "Can manage settings: " . (hasPermission('settings') ? "✅ Yes" : "❌ No") . "\n";

echo "</pre>";

echo "<h2>5. Final Status:</h2>";
if (isAdminLoggedIn()) {
    echo "<p style='color: green; font-size: 18px;'>✅ All systems working! Admin is logged in.</p>";
    echo "<p>";
    echo "<a href='AdminPanel.php' style='margin-right: 10px;'>Go to Admin Panel</a> | ";
    echo "<a href='logout.php'>Logout</a>";
    echo "</p>";
} else {
    echo "<p style='color: red; font-size: 18px;'>❌ Something went wrong. Admin is not logged in.</p>";
}

echo "<hr>";
echo "<p><a href='Main.php'>Back to Login Page</a> | <a href='test.php'>Simple Test</a></p>";

?>