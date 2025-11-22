// Client-side JavaScript functions for admin panel
// This replaces the original AdminPanel.js functionality

document.addEventListener('DOMContentLoaded', function() {
    // Initialize admin panel
    initializeAdminPanel();
    
    // Initialize chat if admin is logged in
    initializeAdminChat();
    
    // Load admin profile
    loadAdminProfile();
});
zzzzzd
// Initialize admin panel functionality
function initializeAdminPanel() {
    // Toggle sidebar
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const toggleSidebar = document.getElementById('toggleSidebar');
    const mobileToggleSidebar = document.getElementById('mobileToggleSidebar');
    const sidebarClose = document.getElementById('sidebarClose');
    const welcomeScreen = document.getElementById('welcome-screen');
    const showDashboardBtn = document.getElementById('show-dashboard');

    // Initially hide all pages and show welcome screen
    const pages = document.querySelectorAll('.page');
    pages.forEach(page => page.classList.remove('active'));
    welcomeScreen.style.display = 'flex';

    // Toggle sidebar from header button (desktop)
    toggleSidebar?.addEventListener('click', function() {
        sidebar.classList.toggle('sidebar-collapsed');
        mainContent.classList.toggle('main-content-expanded');
        
        // Change icon based on state
        const icon = this.querySelector('i');
        if (sidebar.classList.contains('sidebar-collapsed')) {
            icon.classList.remove('fa-chevron-left');
            icon.classList.add('fa-chevron-right');
        } else {
            icon.classList.remove('fa-chevron-right');
            icon.classList.add('fa-chevron-left');
        }
    });

    // Toggle sidebar from mobile menu button
    mobileToggleSidebar?.addEventListener('click', function() {
        sidebar.classList.toggle('active');
    });

    // Close sidebar from sidebar close button (mobile)
    sidebarClose?.addEventListener('click', function() {
        sidebar.classList.remove('active');
    });

    // Handle menu item clicks
    const menuItems = document.querySelectorAll('.menu-item');
    
    menuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all menu items
            menuItems.forEach(i => i.classList.remove('active'));
            
            // Add active class to clicked menu item
            this.classList.add('active');
            
            // Get the page to show
            const pageId = this.getAttribute('data-page');
            
            // Hide welcome screen and all pages
            welcomeScreen.style.display = 'none';
            pages.forEach(page => page.classList.remove('active'));
            
            // Show the selected page
            const activePage = document.getElementById(pageId);
            if (activePage) {
                activePage.classList.add('active');
            }
            
            // Close sidebar on mobile after selection
            if (window.innerWidth <= 992) {
                sidebar.classList.remove('active');
            }
        });
    });

    // Show dashboard when button is clicked
    showDashboardBtn?.addEventListener('click', function() {
        welcomeScreen.style.display = 'none';
        pages.forEach(page => page.classList.remove('active'));
        document.getElementById('dashboard').classList.add('active');
        
        // Set dashboard menu item as active
        menuItems.forEach(i => i.classList.remove('active'));
        document.querySelector('[data-page="dashboard"]').classList.add('active');
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 992) {
            if (!sidebar.contains(e.target) && 
                !mobileToggleSidebar.contains(e.target) && 
                !sidebarClose.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 992) {
            sidebar.classList.remove('active');
        }
    });
}

// Initialize admin chat functionality
async function initializeAdminChat() {
    try {
        // Get admin ID from session
        const adminId = adminData.id;
        if (adminId) {
            const adminUser = { id: adminId, name: "Admin" };
            initChat(adminUser);
        } else {
            console.error("Admin not found in session");
        }
    } catch (error) {
        console.error("Error initializing admin chat:", error);
    }
}

// Load admin profile information
async function loadAdminProfile() {
    try {
        const adminName = adminData.name;
        const adminNameDisplay = document.getElementById('adminNameDisplay');
        if (adminNameDisplay) {
            adminNameDisplay.textContent = adminName;
        }
        
        // Load profile picture if available
        const adminId = adminData.id;
        if (adminId) {
            // You can implement profile picture loading here
            const profilePic = document.getElementById('adminProfilePic');
            if (profilePic) {
                // Set default profile picture or load from server
                profilePic.src = './IMAGES/logo.jpg';
            }
        }
    } catch (error) {
        console.error("Error loading admin profile:", error);
    }
}

// Handle logout
function handleLogout() {
    window.location.href = 'logout.php';
}

// Update dashboard statistics
function updateDashboardStats() {
    // This function can be used to update stats via AJAX calls
    // For now, it uses the static values from PHP
    const stats = adminData.stats;
    
    // Update the DOM with new stats if needed
    console.log('Dashboard stats:', stats);
}

// Check admin permissions
function checkPermission(permission) {
    const permissions = adminData.permissions;
    return permissions[permission] === true;
}

// Log admin activity
function logActivity(activity, details = '') {
    const adminId = adminData.id;
    if (adminId) {
        // You can implement AJAX call to log activity
        console.log(`Admin ${adminId} - Activity: ${activity} - Details: ${details}`);
    }
}