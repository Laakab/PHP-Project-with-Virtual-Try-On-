# Admin Panel - PHP Conversion

This project has been converted from static HTML/CSS/JavaScript to a dynamic PHP application with session management and authentication.

## Files Converted

### Original Files
- `AdminPanel.html` → `AdminPanel.php`
- `AdminPanel.js` → `AdminPanel_functions.php`
- `AdminPanel.css` (unchanged - still used for styling)

### New PHP Files
- `Main.php` - Login page with authentication
- `logout.php` - Logout functionality
- `AdminPanel_functions.php` - Server-side functions and session management

## Features Added

### 1. Session Management
- PHP sessions for user authentication
- Login/logout functionality
- Session-based user data storage

### 2. Authentication System
- Secure login with username/password
- Session validation on each page
- Automatic redirect to login for unauthorized access

### 3. Dynamic Content
- Admin details loaded from session
- Dashboard statistics can be populated from database
- Activity logging functionality

### 4. Security Features
- Session-based authentication
- Input validation
- Secure logout process

## Usage

### Default Login Credentials
- **Username:** admin
- **Password:** admin123

### How to Use
1. Navigate to `Main.php` to login
2. Use the admin credentials above
3. Access the admin dashboard at `AdminPanel.php`
4. Use the logout button to end the session

### Database Integration (Future Enhancement)
The current implementation uses hardcoded credentials for demonstration. To integrate with a database:

1. Update the `adminLogin()` function in `AdminPanel_functions.php`
2. Replace the hardcoded credentials with database queries
3. Update the `getAdminStats()` function to pull real data

## File Structure

```
Admin/
├── AdminPanel.php          # Main admin dashboard
├── AdminPanel_functions.php # PHP functions and session management
├── AdminPanel.css          # Styling (unchanged)
├── Main.php                # Login page
├── logout.php              # Logout functionality
└── README.md               # This file
```

## Security Notes

- Change the default admin credentials immediately
- Implement proper database connection for production use
- Add CSRF protection for forms
- Use HTTPS for production deployment
- Implement proper password hashing (use `password_hash()` and `password_verify()`)

## Testing

Place these files in your XAMPP htdocs directory and access through:
- Login: http://localhost/Admin/Main.php
- Dashboard: http://localhost/Admin/AdminPanel.php

The application will automatically redirect to login if not authenticated.