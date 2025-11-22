<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: Main.php");
    exit();
}

// Get admin details from session
$adminName = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin';
$adminEmail = isset($_SESSION['admin_email']) ? $_SESSION['admin_email'] : '';
$adminId = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 0;

require_once __DIR__ . '/models/Database.php';
$pdo = (new Database())->getConnection();

$totalSales = (float)$pdo->query('SELECT COALESCE(SUM(total),0) FROM orders')->fetchColumn();
$newOrders = (int)$pdo->query('SELECT COUNT(*) FROM orders WHERE order_date >= CURDATE()')->fetchColumn();
$productsCount = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$customersCount = (int)$pdo->query('SELECT COUNT(*) FROM signup')->fetchColumn();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Crowd Zero</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-bg: #020617;
            --sidebar-bg-soft: #020617;
            --sidebar-link: #e5e7eb;
            --sidebar-link-active-bg: #0f172a;
            --sidebar-link-active: #f97316;
        }

        body.admin-layout.no-scroll {
            overflow: hidden;
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(2, 6, 23, 0.6);
            backdrop-filter: blur(3px);
            z-index: 1040;
        }

        .sidebar-backdrop.active {
            display: block;
        }

        @media (max-width: 991.98px) {
            #sidebar {
                position: fixed;
                top: 0;
                left: 0;
                width: 260px;
                height: 100vh;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1050;
            }

            #sidebar.active {
                transform: translateX(0);
            }

            #mainContent {
                margin-left: 0 !important;
            }
        }

        body.admin-layout {
            font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f3f4f6;
            color: #111827;
        }

        /* Sidebar */
        #sidebar {
            background: radial-gradient(circle at top left, #0f172a, #020617);
            box-shadow: 4px 0 18px rgba(15, 23, 42, 0.45);
        }

        .sidebar-brand {
            letter-spacing: .08em;
            font-weight: 700;
            font-size: .95rem;
        }

        #sidebar .list-group-item {
            background: transparent;
            color: var(--sidebar-link);
            border-radius: .5rem;
            margin-bottom: .25rem;
            padding: .55rem .75rem;
            display: flex;
            align-items: center;
            font-size: .9rem;
            border: 0;
            transition: background-color .15s ease, color .15s ease, transform .1s ease;
        }

        #sidebar .list-group-item i {
            width: 1.25rem;
            text-align: center;
            font-size: .95rem;
        }

        #sidebar .list-group-item:hover {
            background-color: rgba(148, 163, 184, 0.16);
            color: #fff;
            transform: translateX(2px);
        }

        #sidebar .list-group-item.active {
            background: linear-gradient(90deg, var(--sidebar-link-active-bg), #0b1120);
            color: var(--sidebar-link-active);
            font-weight: 600;
            box-shadow: inset 2px 0 0 var(--sidebar-link-active);
        }

        /* Top navbar */
        .top-navbar {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .nav-icon-btn {
            border-radius: 999px;
            border: none;
            color: #6b7280;
        }

        .nav-icon-btn:hover {
            background-color: #f3f4f6;
            color: #111827;
        }

        .admin-avatar {
            box-shadow: 0 0 0 2px #e5e7eb;
        }

        .badge-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
        }

        /* Content area */
        .admin-content-wrapper {
            min-height: 100vh;
            background: radial-gradient(circle at top left, #eff6ff, #f9fafb);
        }

        .page {
            animation: fadeIn .25s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card-stat {
            border: 0;
            border-radius: 1rem;
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.08);
        }

        .card-stat-icon {
            width: 3rem;
            height: 3rem;
            border-radius: .9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .welcome-card {
            border-radius: 1.25rem;
            border: 0;
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.12);
        }

        .welcome-gradient {
            background: linear-gradient(135deg, #0f172a, #1f2937, #f97316);
            color: #f9fafb;
        }

        .welcome-gradient h1 {
            font-weight: 600;
        }

        .welcome-chip {
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .16em;
        }

        @media (max-width: 767.98px) {
            #sidebar {
                box-shadow: 4px 0 24px rgba(15, 23, 42, 0.75);
            }
        }

        /* Chat styles */
        .chat-customer-item {
            border-radius: .6rem;
            padding: .5rem .6rem;
            cursor: pointer;
            transition: background-color .15s ease, transform .1s ease;
        }

        .chat-customer-item:hover {
            background-color: #f3f4f6;
            transform: translateY(-1px);
        }

        .chat-customer-item.active {
            background-color: #e5f0ff;
            box-shadow: inset 2px 0 0 #2563eb;
        }

        .chat-messages {
            background-color: #f9fafb;
            height: 360px;
            overflow-y: auto;
        }

        .message-row {
            display: flex;
            margin-bottom: .5rem;
        }

        .message-row.outgoing {
            justify-content: flex-end;
        }

        .message-bubble {
            max-width: 75%;
            border-radius: 1rem;
            padding: .4rem .75rem;
            font-size: .85rem;
        }

        .message-row.outgoing .message-bubble {
            background: #2563eb;
            color: #eff6ff;
            border-bottom-right-radius: .2rem;
        }

        .message-row.incoming .message-bubble {
            background: #e5e7eb;
            color: #111827;
            border-bottom-left-radius: .2rem;
        }

        .message-meta {
            font-size: .7rem;
            color: #9ca3af;
            margin-top: .1rem;
        }

        .chat-input-area {
            background-color: #ffffff;
        }

        .chat-input-area .form-control {
            border-radius: 999px 0 0 999px;
        }

        .chat-input-area .btn-primary {
            border-radius: 0 999px 999px 0;
        }
    </style>
</head>

<body class="admin-layout">
    <div class="container-fluid p-0">
        <!-- Sidebar -->
        <div class="row g-0">
            <div class="col-lg-2 col-md-3 col-12 text-white vh-100 position-fixed" id="sidebar">
                <div class="d-flex flex-column h-100">
                    <!-- Sidebar Header -->
                    <div class="p-3 border-bottom border-secondary border-opacity-25">
                        <div class="d-flex align-items-center gap-2">
                            <img src="./images/crowd.png" alt="Crowd Zero Logo" class="rounded-3 bg-white bg-opacity-10 p-1" width="40" height="40">
                            <div>
                                <div class="sidebar-brand">CROWD ZERO</div>
                                <small class="text-muted text-uppercase" style="font-size: .7rem;">Admin Console</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sidebar Menu -->
                    <div class="flex-grow-1 p-3 overflow-auto">
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action mb-1" data-page="dashboard">
                                <i class="fas fa-gauge-high me-2"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action mb-1" data-page="orders-board">
                                <i class="fas fa-clipboard-list me-2"></i>
                                <span>Orders Board</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action mb-1" data-page="cart-board">
                                <i class="fas fa-basket-shopping me-2"></i>
                                <span>Add to Cart Board</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action mb-1" data-page="process-flow">
                                <i class="fas fa-diagram-project me-2"></i>
                                <span>Ops Flowchart</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action mb-1" data-page="add-category">
                                <i class="fas fa-tags me-2"></i>
                                <span>Add Category</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action mb-1" data-page="add-product">
                                <i class="fas fa-box me-2"></i>
                                <span>Add Product</span>
                            </a>
                           
                            
                            <a href="#" class="list-group-item list-group-item-action mb-1" data-page="view-products">
                                <i class="fas fa-list me-2"></i>
                                <span>View Products</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action mb-1" data-page="admin-manage">
                                <i class="fas fa-users-cog me-2"></i>
                                <span>Admin Management</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action mb-1" data-page="showcategory">
                                <i class="fas fa-tags me-2"></i>
                                <span>Show Categories</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action mb-1" data-page="create-ads">
                                <i class="fas fa-ad me-2"></i>
                                <span>Create Ads</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action mb-1" data-page="show-ads">
                                <i class="fas fa-table me-2"></i>
                                <span>Show Ads Table</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action mb-1" data-page="create-offers">
                                <i class="fas fa-tag me-2"></i>
                                <span>Create Offers</span>
                            </a>

                        </div>
                    </div>
                </div>
            </div>
            <div class="sidebar-backdrop d-md-none" id="sidebarBackdrop"></div>
            
            <!-- Main Content -->
            <div class="col-lg-10 col-md-9 col-12 offset-lg-2 offset-md-3 admin-content-wrapper" id="mainContent">
                <!-- Header -->
                <nav class="navbar navbar-expand-lg top-navbar shadow-sm">
                    <div class="container-fluid">
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-outline-secondary me-2 d-md-none" id="mobileToggleSidebar">
                                <i class="fas fa-bars"></i>
                            </button>
                            <span class="navbar-brand mb-0 h5 d-none d-md-inline-flex align-items-center gap-2">
                                <i class="fas fa-gauge-high text-warning"></i>
                                <span>Admin Dashboard</span>
                            </span>
                        </div>

                        <div class="d-flex align-items-center ms-auto gap-2">
                            <button class="btn nav-icon-btn position-relative" type="button">
                                <i class="fas fa-bell"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge-dot bg-danger border border-white"></span>
                            </button>

                            <div class="dropdown">
                                <button class="btn btn-light border-0 d-flex align-items-center px-2" data-bs-toggle="dropdown">
                                    <div class="text-end me-2 d-none d-sm-block">
                                        <div class="fw-semibold small"><?php echo htmlspecialchars($adminName); ?></div>
                                        <?php if (!empty($adminEmail)): ?>
                                            <div class="text-muted" style="font-size: .75rem; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                <?php echo htmlspecialchars($adminEmail); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <img id="adminProfilePic" src="./images/crowd.png" alt="Admin User" 
                                         class="rounded-circle admin-avatar" width="40" height="40"
                                         onerror="this.src='./IMAGES/default-profile.jpg'">
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm mt-2">
                                    <li class="dropdown-header small text-muted">
                                        Signed in as<br>
                                        <span class="fw-semibold"><?php echo htmlspecialchars($adminName); ?></span>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
                
                <!-- Content Area -->
                <div class="container-fluid p-4">
                    <!-- Welcome Screen (shown by default) -->
                    <div class="row justify-content-center" id="welcome-screen">
                        <div class="col-xl-8 col-lg-9">
                            <div class="card welcome-card overflow-hidden">
                                <div class="row g-0">
                                    <div class="col-md-7 p-4 p-md-5 welcome-gradient">
                                        <span class="badge bg-white bg-opacity-10 rounded-pill mb-3 welcome-chip">
                                            <i class="fas fa-shield-alt me-1"></i> Secure Admin Area
                                        </span>
                                        <h1 class="mb-3">Welcome back, <?php echo htmlspecialchars($adminName); ?>.</h1>
                                        <p class="mb-4 text-sm">
                                            Monitor performance, manage products, categories, offers, ads and customer orders from a single, modern control panel.
                                        </p>
                                        <button class="btn btn-light btn-lg text-start px-4" id="show-dashboard">
                                            <span class="d-flex align-items-center">
                                                <i class="fas fa-gauge-high me-2 text-warning"></i>
                                                <span>Open Dashboard</span>
                                            </span>
                                        </button>
                                    </div>
                                    <div class="col-md-5 d-none d-md-flex align-items-center justify-content-center bg-white">
                                        <div class="text-center px-4 py-5">
                                            <div class="mb-3">
                                                <i class="fas fa-chart-line fa-3x text-primary"></i>
                                            </div>
                                            <p class="fw-semibold mb-1">Real-time insights</p>
                                            <p class="text-muted small mb-0">Quickly switch between modules using the navigation menu on the left.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dashboard Page -->
                    <div class="page" id="dashboard" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1>Dashboard Overview</h1>
                            <div>
                                <button class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-download me-1"></i> Export
                                </button>
                                <button class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Add New
                                </button>
                            </div>
                        </div>
                        
                        <div class="row g-4 mb-4">
                            <div class="col-md-3 col-sm-6">
                                <div class="card card-stat border-0">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="card-title text-muted text-uppercase" style="font-size: .7rem;">Total Sales</h6>
                                                <h3 class="card-text mb-1">PKR <?php echo number_format($totalSales); ?></h3>
                                                <small class="text-success">
                                                    <i class="fas fa-arrow-up me-1"></i> 12.5% from last month
                                                </small>
                                            </div>
                                            <div class="card-stat-icon bg-primary text-white">
                                                <i class="fas fa-shopping-cart"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="card card-stat border-0">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="card-title text-muted text-uppercase" style="font-size: .7rem;">New Orders</h6>
                                                <h3 class="card-text mb-1"><?php echo number_format($newOrders); ?></h3>
                                                <small class="text-success">
                                                    <i class="fas fa-arrow-up me-1"></i> 8.3% from last month
                                                </small>
                                            </div>
                                            <div class="card-stat-icon bg-success text-white">
                                                <i class="fas fa-box-open"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="card card-stat border-0">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="card-title text-muted text-uppercase" style="font-size: .7rem;">Products</h6>
                                                <h3 class="card-text mb-1"><?php echo number_format($productsCount); ?></h3>
                                                <small class="text-success">
                                                    <i class="fas fa-arrow-up me-1"></i> 5.2% from last month
                                                </small>
                                            </div>
                                            <div class="card-stat-icon bg-warning text-white">
                                                <i class="fas fa-boxes"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="card card-stat border-0">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="card-title text-muted text-uppercase" style="font-size: .7rem;">Customers</h6>
                                                <h3 class="card-text mb-1"><?php echo number_format($customersCount); ?></h3>
                                                <small class="text-danger">
                                                    <i class="fas fa-arrow-down me-1"></i> 2.1% from last month
                                                </small>
                                            </div>
                                            <div class="card-stat-icon bg-danger text-white">
                                                <i class="fas fa-users"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card border-0 shadow-sm rounded-4 mt-2">
                            <div class="card-body">
                                <p class="mb-1 fw-semibold">Quick tips</p>
                                <p class="mb-0 text-muted small">Use the left navigation to switch between modules. The dashboard above gives you a quick overview of the most important KPIs.</p>
                            </div>
                        </div>
                    </div>



                    <!-- Orders Board -->
                    <div class="page" id="orders-board" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1>Orders Board</h1>
                        </div>
                        <div class="card">
                            <div class="card-body p-0">
                                <iframe src="OrderBoard.php" style="width:100%; height:620px; border:none; border-radius:0 0 1.5rem 1.5rem;"></iframe>
                            </div>
                        </div>
                    </div>

                    <!-- Cart Board -->
                    <div class="page" id="cart-board" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1>Add to Cart Board</h1>
                        </div>
                        <div class="card">
                            <div class="card-body p-0">
                                <iframe src="CartBoard.php" style="width:100%; height:620px; border:none;"></iframe>
                            </div>
                        </div>
                    </div>

                    <!-- Process Flow Page -->
                    <div class="page" id="process-flow" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1>Order Journey Flowchart</h1>
                        </div>
                        <div class="card bg-dark text-white border-0">
                            <div class="card-body p-0">
                                <iframe src="ProcessFlow.php" style="width:100%; height:600px; border:none; border-radius:1rem;"></iframe>
                            </div>
                        </div>
                    </div>

                    <!-- Add Category Page -->
                    <div class="page" id="add-category" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1>Add Category</h1>
                        </div>
                        <div class="card">
                            <div class="card-body p-0">
                                <iframe src="AddCateGory.php" style="width:100%; height:600px; border:none;"></iframe>
                            </div>
                        </div>
                    </div>

                    <!-- Add Product Page -->
                    <div class="page" id="add-product" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1>Add Product</h1>
                        </div>
                        <div class="card">
                            <div class="card-body p-0">
                                <iframe src="AddProduct.php" style="width:100%; height:600px; border:none;"></iframe>
                            </div>
                        </div>
                    </div>



                    <!-- View Products Page -->
                    <div class="page" id="view-products" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1>View Products</h1>
                        </div>
                        <div class="card">
                            <div class="card-body p-0">
                                <iframe src="ViewProduct.php" style="width:100%; height:600px; border:none;"></iframe>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Management Page -->
                    <!-- <div class="page" id="admin-manage" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1>Admin Management</h1>
                        </div>
                        <div class="card">
                            <div class="card-body p-0">
                                <iframe src="AdminManage.php" style="width:100%; height:600px; border:none;"></iframe>
                            </div>
                        </div>
                    </div> -->

                    <!-- Show Category Page -->
                    <div class="page" id="showcategory" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1>Category Management</h1>
                        </div>
                        <div class="card">
                            <div class="card-body p-0">
                                <iframe src="./showCategory.php" style="width:100%; height:600px; border:none;"></iframe>
                            </div>
                        </div>
                    </div>

                    <!-- Create Ads Page -->
                    <div class="page" id="create-ads" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1>Create Ads</h1>
                        </div>
                        <div class="card">
                            <div class="card-body p-0">
                                <iframe src="./Addscreate.php" style="width:100%; height:600px; border:none;"></iframe>
                            </div>
                        </div>
                    </div>

                    <!-- Show Ads Table Page -->
                    <div class="page" id="show-ads" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1>Ads Table</h1>
                        </div>
                        <div class="card">
                            <div class="card-body p-0">
                                <iframe src="./AdsTable.php" style="width:100%; height:600px; border:none;"></iframe>
                            </div>
                        </div>
                    </div>

                    <!-- Create Offers Page -->
                    <div class="page" id="create-offers" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1>Create Offer</h1>
                        </div>
                        <div class="card">
                            <div class="card-body p-0">
                                <iframe src="./Offercreate.php" style="width:100%; height:600px; border:none;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Icon and Container -->
    <div class="position-fixed bottom-0 end-0 m-4">
        <button class="btn btn-primary rounded-circle p-3 shadow-lg position-relative" id="chatIcon">
            <i class="fas fa-comment-dots"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="unreadCount" style="display: none;">0</span>
        </button>
    </div>

    <div class="modal fade" id="chatModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-2">
                    <div>
                        <h5 class="modal-title">Inbox</h5>
                        <small class="text-muted">Chat with your customers in real time</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="row g-0">
                        <!-- Conversation list -->
                        <div class="col-md-4 border-end pe-md-2 mb-3 mb-md-0">
                            <div class="px-2 pb-2">
                                <input type="text" class="form-control form-control-sm mb-2" id="chatSearch" placeholder="Search users...">
                            </div>
                            <div class="px-2" id="customerList"></div>
                        </div>

                        <!-- Conversation panel -->
                        <div class="col-md-8 d-flex flex-column">
                            <div class="border-bottom px-3 py-2 d-flex align-items-center justify-content-between">
                                <div>
                                    <div id="activeContactName" class="fw-semibold small">Select a user</div>
                                    <div id="activeContactStatus" class="text-muted" style="font-size: .75rem;">No conversation selected</div>
                                </div>
                                <span class="badge bg-success-subtle text-success border border-success border-opacity-25" style="font-size: .7rem;">
                                    <i class="fas fa-circle text-success me-1" style="font-size: .5rem;"></i>Online
                                </span>
                            </div>

                            <div class="chat-messages p-3 flex-grow-1" id="chatMessages">
                                <div class="text-center text-muted mt-5" id="noConversation">
                                    Select a user on the left to view the conversation.
                                </div>
                            </div>

                            <div class="chat-input-area p-2 border-top" id="chatInputArea" style="display: none;">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" id="messageInput" placeholder="Type a message and press Enter...">
                                    <button class="btn btn-primary" id="sendBtn">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="/eel.js"></script>
    <script>
        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');
            const mobileToggleSidebar = document.getElementById('mobileToggleSidebar');
            const mainContent = document.getElementById('mainContent');
            
            function openSidebar() {
                sidebar.classList.add('active');
                sidebarBackdrop.classList.add('active');
                document.body.classList.add('no-scroll');
            }
            
            function closeSidebar() {
                sidebar.classList.remove('active');
                sidebarBackdrop.classList.remove('active');
                document.body.classList.remove('no-scroll');
            }
            
            if (mobileToggleSidebar) {
                mobileToggleSidebar.addEventListener('click', openSidebar);
            }
            if (sidebarBackdrop) {
                sidebarBackdrop.addEventListener('click', closeSidebar);
            }
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 992) {
                    closeSidebar();
                }
            });
            
            // Page navigation
            const menuItems = document.querySelectorAll('.list-group-item[data-page]');
            const pages = document.querySelectorAll('.page');
            const welcomeScreen = document.getElementById('welcome-screen');
            const showDashboardBtn = document.getElementById('show-dashboard');
            
            // Show dashboard when button is clicked
            showDashboardBtn.addEventListener('click', function() {
                welcomeScreen.style.display = 'none';
                document.getElementById('dashboard').style.display = 'block';
            });
            
            // Handle menu item clicks
            menuItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const pageId = this.getAttribute('data-page');
                    
                    // Active state on sidebar
                    menuItems.forEach(m => m.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Hide all pages and welcome screen
                    welcomeScreen.style.display = 'none';
                    pages.forEach(page => {
                        page.style.display = 'none';
                    });
                    
                    // Show selected page
                    document.getElementById(pageId).style.display = 'block';
                    
                    // Close sidebar on mobile after selection
                    if (window.innerWidth < 992) {
                        closeSidebar();
                    }
                });
            });
            
            // Chat functionality (in-page demo conversations)
            const chatIcon = document.getElementById('chatIcon');
            const chatModalElement = document.getElementById('chatModal');
            const chatModal = new bootstrap.Modal(chatModalElement);
            const customerList = document.getElementById('customerList');
            const chatMessages = document.getElementById('chatMessages');
            const chatInputArea = document.getElementById('chatInputArea');
            const messageInput = document.getElementById('messageInput');
            const sendBtn = document.getElementById('sendBtn');
            const unreadCount = document.getElementById('unreadCount');
            const noConversation = document.getElementById('noConversation');
            const activeContactName = document.getElementById('activeContactName');
            const activeContactStatus = document.getElementById('activeContactStatus');

            const demoUsers = [
                { id: 'u1', name: 'Ali Khan', email: 'ali@example.com' },
                { id: 'u2', name: 'Sara Ahmed', email: 'sara@example.com' },
                { id: 'u3', name: 'John Doe', email: 'john@example.com' }
            ];

            const conversations = {};
            let activeUserId = null;
            const unreadByUser = {};

            function renderCustomerList(filter = '') {
                customerList.innerHTML = '';
                const term = filter.toLowerCase();
                demoUsers.forEach(user => {
                    if (term && !user.name.toLowerCase().includes(term) && !user.email.toLowerCase().includes(term)) {
                        return;
                    }
                    const wrapper = document.createElement('div');
                    wrapper.className = 'chat-customer-item d-flex align-items-center justify-content-between';
                    wrapper.dataset.userId = user.id;

                    if (user.id === activeUserId) {
                        wrapper.classList.add('active');
                    }

                    const info = document.createElement('div');
                    info.innerHTML = `
                        <div class="fw-semibold" style="font-size:.85rem;">${user.name}</div>
                        <div class="text-muted" style="font-size:.7rem;">${user.email}</div>
                    `;

                    const badge = document.createElement('span');
                    const unread = unreadByUser[user.id] || 0;
                    if (unread > 0) {
                        badge.className = 'badge bg-danger rounded-pill';
                        badge.textContent = unread;
                    }

                    wrapper.appendChild(info);
                    if (unread > 0) wrapper.appendChild(badge);

                    wrapper.addEventListener('click', () => openConversation(user.id));
                    customerList.appendChild(wrapper);
                });
            }

            function updateGlobalUnreadBadge() {
                const totalUnread = Object.values(unreadByUser).reduce((sum, v) => sum + v, 0);
                if (totalUnread > 0) {
                    unreadCount.style.display = '';
                    unreadCount.textContent = totalUnread;
                } else {
                    unreadCount.style.display = 'none';
                }
            }

            function renderMessages(userId) {
                chatMessages.innerHTML = '';
                const msgs = conversations[userId] || [];

                if (msgs.length === 0) {
                    const empty = document.createElement('div');
                    empty.className = 'text-center text-muted mt-5';
                    empty.textContent = 'No messages yet. Start the conversation!';
                    chatMessages.appendChild(empty);
                    return;
                }

                msgs.forEach(msg => {
                    const row = document.createElement('div');
                    row.className = `message-row ${msg.sender === 'admin' ? 'outgoing' : 'incoming'}`;

                    const bubble = document.createElement('div');
                    bubble.className = 'message-bubble';
                    bubble.textContent = msg.text;

                    const meta = document.createElement('div');
                    meta.className = 'message-meta';
                    meta.textContent = msg.time;

                    const wrapper = document.createElement('div');
                    wrapper.appendChild(bubble);
                    wrapper.appendChild(meta);

                    row.appendChild(wrapper);
                    chatMessages.appendChild(row);
                });

                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            function openConversation(userId) {
                activeUserId = userId;
                const user = demoUsers.find(u => u.id === userId);
                if (!user) return;

                activeContactName.textContent = user.name;
                activeContactStatus.textContent = 'You are chatting with this customer';
                chatInputArea.style.display = '';
                if (noConversation) noConversation.style.display = 'none';

                // reset unread for this user
                unreadByUser[userId] = 0;
                renderCustomerList(document.getElementById('chatSearch').value || '');
                updateGlobalUnreadBadge();
                renderMessages(userId);
            }

            function addMessage(userId, sender, text) {
                const now = new Date();
                const time = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                if (!conversations[userId]) {
                    conversations[userId] = [];
                }
                conversations[userId].push({ sender, text, time });

                if (sender !== 'admin') {
                    if (!unreadByUser[userId]) unreadByUser[userId] = 0;
                    if (userId !== activeUserId || !chatModalElement.classList.contains('show')) {
                        unreadByUser[userId] += 1;
                        updateGlobalUnreadBadge();
                        renderCustomerList(document.getElementById('chatSearch').value || '');
                    }
                }

                if (userId === activeUserId) {
                    renderMessages(userId);
                }
            }

            function sendCurrentMessage() {
                const text = messageInput.value.trim();
                if (!text || !activeUserId) return;
                addMessage(activeUserId, 'admin', text);
                messageInput.value = '';

                // Demo auto-reply from user after a short delay
                setTimeout(() => {
                    addMessage(activeUserId, 'user', 'Thanks for your message, we will look into this.');
                }, 800);
            }

            chatIcon.addEventListener('click', function() {
                chatModal.show();
                // when chat opens, clear global unread (they will be cleared per user on open)
                updateGlobalUnreadBadge();
            });

            if (sendBtn) {
                sendBtn.addEventListener('click', sendCurrentMessage);
            }

            if (messageInput) {
                messageInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        sendCurrentMessage();
                    }
                });
            }

            const chatSearch = document.getElementById('chatSearch');
            if (chatSearch) {
                chatSearch.addEventListener('input', function() {
                    renderCustomerList(this.value);
                });
            }

            renderCustomerList();

            // Responsive sidebar handling
            function handleResize() {
                if (window.innerWidth >= 768) {
                    sidebar.classList.remove('d-none');
                    mainContent.classList.remove('col-12');
                    mainContent.classList.add('col-md-9', 'col-lg-10');
                } else {
                    sidebar.classList.add('d-none');
                    mainContent.classList.remove('col-md-9', 'col-lg-10');
                    mainContent.classList.add('col-12');
                }
            }
            
            // Initial call and event listener for resize
            handleResize();
            window.addEventListener('resize', handleResize);
        });
    </script>
    <script>
      (function(){
        const chatIcon = document.getElementById('chatIcon');
        const chatModalElement = document.getElementById('chatModal');
        const chatModal = new bootstrap.Modal(chatModalElement);
        const customerList = document.getElementById('customerList');
        const chatMessages = document.getElementById('chatMessages');
        const chatInputArea = document.getElementById('chatInputArea');
        const messageInput = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendBtn');
        const noConversation = document.getElementById('noConversation');
        const activeContactName = document.getElementById('activeContactName');
        const activeContactStatus = document.getElementById('activeContactStatus');

        let activeCustomerId = null;
        let pollTimer = null;

        async function fetchCustomers(term = ''){
          try {
            const res = await fetch('controllers/ChatController.php?action=list_customers');
            const customers = await res.json();
            renderCustomerList(customers, term);
          } catch(e) { console.error('list_customers failed', e); }
        }

        function renderCustomerList(customers, term){
          customerList.innerHTML = '';
          term = (term||'').toLowerCase();
          customers.forEach(c => {
            if (term && !c.name.toLowerCase().includes(term) && !c.email.toLowerCase().includes(term)) return;
            const wrapper = document.createElement('div');
            wrapper.className = 'chat-customer-item d-flex align-items-center justify-content-between';
            wrapper.dataset.userId = c.customer_id;
            if (String(c.customer_id) === String(activeCustomerId)) wrapper.classList.add('active');
            const info = document.createElement('div');
            info.innerHTML = `<div class="fw-semibold" style="font-size:.85rem;">${c.name}</div><div class="text-muted" style="font-size:.7rem;">${c.email}</div>`;
            wrapper.appendChild(info);
            wrapper.addEventListener('click', () => openConversation(c));
            customerList.appendChild(wrapper);
          });
        }

        async function loadMessages(customerId){
          try {
            const res = await fetch('controllers/ChatController.php?action=get_messages&customer_id=' + encodeURIComponent(customerId));
            const msgs = await res.json();
            renderMessages(msgs);
          } catch(e) { console.error('get_messages failed', e); }
        }

        function renderMessages(msgs){
          chatMessages.innerHTML = '';
          if (!msgs || msgs.length === 0) {
            const empty = document.createElement('div');
            empty.className = 'text-center text-muted mt-5';
            empty.textContent = 'No messages yet. Start the conversation!';
            chatMessages.appendChild(empty);
            return;
          }
          msgs.forEach(m => {
            const row = document.createElement('div');
            const outgoing = m.sender_type === 'admin';
            row.className = 'message-row ' + (outgoing ? 'outgoing' : 'incoming');
            const bubble = document.createElement('div');
            bubble.className = 'message-bubble';
            bubble.textContent = m.text;
            const meta = document.createElement('div');
            meta.className = 'message-meta';
            const dt = new Date(m.created_at);
            meta.textContent = dt.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            const wrapper = document.createElement('div');
            wrapper.appendChild(bubble);
            wrapper.appendChild(meta);
            row.appendChild(wrapper);
            chatMessages.appendChild(row);
          });
          chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        async function openConversation(customer){
          activeCustomerId = customer.customer_id;
          activeContactName.textContent = customer.name;
          activeContactStatus.textContent = 'You are chatting with this customer';
          chatInputArea.style.display = '';
          if (noConversation) noConversation.style.display = 'none';
          await loadMessages(activeCustomerId);
          if (pollTimer) clearInterval(pollTimer);
          pollTimer = setInterval(() => loadMessages(activeCustomerId), 3000);
        }

        async function sendCurrentMessage(){
          const text = messageInput.value.trim();
          if (!text || !activeCustomerId) return;
          try {
            const form = new FormData();
            form.append('action','send_admin_message');
            form.append('customer_id', activeCustomerId);
            form.append('text', text);
            await fetch('controllers/ChatController.php', { method: 'POST', body: form });
            messageInput.value = '';
            await loadMessages(activeCustomerId);
          } catch(e) { console.error('send_admin_message failed', e); }
        }

        chatIcon.addEventListener('click', function(){
          chatModal.show();
          fetchCustomers();
        });
        if (sendBtn) sendBtn.addEventListener('click', sendCurrentMessage);
        if (messageInput) {
          messageInput.addEventListener('keydown', function(e){ if (e.key === 'Enter') { e.preventDefault(); sendCurrentMessage(); } });
        }
        const chatSearch = document.getElementById('chatSearch');
        if (chatSearch) chatSearch.addEventListener('input', function(){ fetchCustomers(this.value); });
        chatModalElement.addEventListener('hidden.bs.modal', () => { if (pollTimer) { clearInterval(pollTimer); pollTimer = null; } });
      })();
    </script>
</body>

</html>