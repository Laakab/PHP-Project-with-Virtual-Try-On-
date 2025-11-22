<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentPage = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$navItems = [
    ['label' => 'Home', 'path' => 'index.php'],
    ['label' => 'Products', 'path' => 'products.php'],
    // ['label' => 'Collections', 'path' => 'collections.php'],
    // ['label' => 'Inspiration', 'path' => 'inspiration.php'],
    ['label' => 'Services', 'path' => 'services.php'],
    ['label' => 'Support', 'path' => 'support.php'],
    ['label' => 'About', 'path' => 'about.php'],
    ['label' => 'Team', 'path' => 'Team.php'],
    ['label' => 'Contact', 'path' => 'contact.php'],
    ['label' => 'Virtual try on', 'path' => '../virtual-try-on/frontend/index.html'],
];

$isActive = function (string $path) use ($currentPage): string {
    return $currentPage === $path ? 'active' : '';
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Shop curated products, discover collections, and enjoy a seamless experience across every touchpoint.">
  <title>My E-Commerce</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="site-body">
<header class="site-header">
  <div class="announcement-bar text-center text-white py-2">
    <span class="me-3">✨ Holiday edit now live</span>
    <span class="text-white-50">Free nationwide shipping above PKR 4,000</span>
  </div>
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top site-navbar">
    <div class="container">
      <a class="navbar-brand fw-bold text-dark d-flex align-items-center gap-2" href="index.php">
        <img src="../a/images/crowd.png" alt="Site logo" style="height:32px;width:auto;">
        <span>CROWD ZERO</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
          <?php foreach ($navItems as $item): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo $isActive($item['path']); ?>" href="<?php echo $item['path']; ?>">
                <?php echo htmlspecialchars($item['label']); ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
        <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0 ms-lg-3">
          <a href="cart.php" class="btn btn-outline-secondary btn-sm">Cart</a>
          <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id']): ?>
            <?php 
              $avatar = $_SESSION['user_avatar'] ?? null; 
              $avatarUrl = $avatar ? $avatar : 'images/placeholder-product.svg';
              $displayName = $_SESSION['user_name'] ?? 'Account';
            ?>
            <span class="d-inline-flex align-items-center gap-2 px-2 py-1 border rounded-pill">
              <img src="<?php echo htmlspecialchars($avatarUrl); ?>" alt="Avatar" style="width:28px;height:28px;border-radius:50%;object-fit:cover;">
              <span class="small fw-semibold"><?php echo htmlspecialchars($displayName); ?></span>
            </span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
          <?php else: ?>
            <a href="login.php" class="btn btn-outline-primary btn-sm">Log in</a>
            <a href="signup.php" class="btn btn-primary btn-sm">Sign up</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>
</header>

<main class="page-shell container py-5">