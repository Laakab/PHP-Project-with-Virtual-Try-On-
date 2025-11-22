<?php
// This file contains PHP functions for admin panel functionality
// It replaces AdminPanel.js with server-side functionality

// Check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Handle admin login
function adminLogin($username, $password) {
    // This is a placeholder - you should replace this with actual database authentication
    // For demo purposes, using hardcoded credentials
    // Accepts both 'admin' username and 'admin@crowdzero.com' email
    if (($username === 'admin' || $username === 'admin@crowdzero.com') && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = 1;
        $_SESSION['admin_name'] = 'Administrator';
        $_SESSION['admin_email'] = 'admin@crowdzero.com';
        
        // Log successful login
        logAdminActivity(1, 'Login', 'Admin logged in successfully');
        
        return true;
    }
    
    return false;
}

// Get admin details
function getAdminDetails() {
    if (!isAdminLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['admin_id'] ?? 0,
        'name' => $_SESSION['admin_name'] ?? 'Admin',
        'email' => $_SESSION['admin_email'] ?? ''
    ];
}

// Get admin statistics for dashboard
function getAdminStats() {
    // This is a placeholder - you should replace this with actual database queries
    return [
        'total_sales' => 24780,
        'new_orders' => 1245,
        'products' => 356,
        'customers' => 8742,
        'sales_change' => 12.5,
        'orders_change' => 8.3,
        'products_change' => 5.2,
        'customers_change' => -2.1
    ];
}

// Handle admin logout
function adminLogout() {
    $_SESSION = array();
    session_destroy();
    header("Location: Main.php");
    exit();
}

// Verify admin session
function verifyAdminSession() {
    if (!isAdminLoggedIn()) {
        header("Location: Main.php");
        exit();
    }
}

// Get admin profile picture
function getAdminProfilePicture($adminId) {
    // This is a placeholder - you should implement actual database query
    // Return base64 encoded image or null
    return null;
}

// Update admin profile
function updateAdminProfile($adminId, $data) {
    // This is a placeholder - you should implement actual database update
    return true;
}

// Get admin permissions
function getAdminPermissions($adminId) {
    // This is a placeholder - you should implement actual permission system
    return [
        'dashboard' => true,
        'products' => true,
        'categories' => true,
        'orders' => true,
        'customers' => true,
        'admins' => true,
        'reports' => true,
        'settings' => true
    ];
}

// Check if admin has specific permission
function hasPermission($permission) {
    if (!isAdminLoggedIn()) {
        return false;
    }
    
    $permissions = getAdminPermissions($_SESSION['admin_id']);
    return isset($permissions[$permission]) && $permissions[$permission] === true;
}

// Log admin activity
function logAdminActivity($adminId, $activity, $details = '') {
    // This is a placeholder - you should implement actual logging system
    $logFile = 'admin_logs.txt';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] Admin ID: $adminId - Activity: $activity - Details: $details" . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// Get recent admin activities
function getRecentAdminActivities($adminId, $limit = 10) {
    // This is a placeholder - you should implement actual database query
    return [];
}

// Handle admin password change
function changeAdminPassword($adminId, $currentPassword, $newPassword) {
    // This is a placeholder - you should implement actual password change logic
    return true;
}

// Get admin notifications
function getAdminNotifications($adminId) {
    // This is a placeholder - you should implement actual notification system
    return [];
}

// Mark notification as read
function markNotificationAsRead($notificationId, $adminId) {
    // This is a placeholder - you should implement actual database update
    return true;
}