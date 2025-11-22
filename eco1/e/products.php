<?php
include 'includes/header.php';
require_once __DIR__ . '/config.php';

$productModel = getProductModel();
$products = $productModel->getAllProducts();
$productCount = count($products);
?>

<section class="page-hero text-center mb-5">
  <p class="text-uppercase text-primary mb-2 fw-semibold small">Shop without limits</p>
  <h1 class="display-5 fw-bold">Explore our curated catalog</h1>
  <p class="text-muted mb-0">Browse the latest arrivals and best sellers, all in one place.</p>
</section>

<?php if ($productCount === 0): ?>
  <div class="card shadow-sm border-0">
    <div class="card-body text-center py-5">
      <img src="images/placeholder-product.svg" class="empty-state-icon" alt="Empty state">
      <h3 class="mt-4">Products are on the way</h3>
      <p class="text-muted mb-4">We are curating a fresh collection. Please check back shortly.</p>
      <a href="index.php" class="btn btn-primary">Back to home</a>
    </div>
  </div>
<?php else: ?>
  <div class="section-heading flex-wrap">
    <h2 class="h4 mb-0">Showing <?php echo $productCount; ?> product<?php echo $productCount > 1 ? 's' : ''; ?></h2>
    <small>Images &amp; pricing sync live from your admin panel.</small>
  </div>

  <div class="row g-4">
    <?php foreach ($products as $p): ?>
      <?php
      $imageUrl = format_image_url($p['image'] ?? '');
      $price = (float)($p['price'] ?? 0);
      $discount = (float)($p['discount'] ?? 0);
      $finalPrice = calculate_discounted_price($price, $discount);
      $hasDiscount = $discount > 0;
      ?>
      <div class="col-sm-6 col-lg-4">
        <div class="product-card card h-100 shadow-sm border-0">
          <div class="position-relative">
            <img src="<?php echo htmlspecialchars($imageUrl); ?>" class="product-thumb" alt="<?php echo htmlspecialchars($p['name'] ?? 'Product image'); ?>" loading="lazy">
            <?php if ($hasDiscount): ?>
              <span class="badge discount-badge"><?php echo $discount; ?>% off</span>
            <?php endif; ?>
          </div>
          <div class="card-body d-flex flex-column">
            <div class="mb-2">
              <h5 class="card-title mb-1"><?php echo htmlspecialchars($p['name']); ?></h5>
              <?php if (!empty($p['category_name'])): ?>
                <span class="badge badge-soft">#<?php echo htmlspecialchars($p['category_name']); ?></span>
              <?php endif; ?>
            </div>
            <div class="pricing mb-3">
              <?php if ($hasDiscount): ?>
                <div class="text-muted text-decoration-line-through small"><?php echo format_price($price); ?></div>
                <div class="fs-5 fw-semibold text-success"><?php echo format_price($finalPrice); ?></div>
              <?php else: ?>
                <div class="fs-5 fw-semibold"><?php echo format_price($price); ?></div>
              <?php endif; ?>
            </div>
            <a href="product_detail.php?id=<?php echo (int)$p['id']; ?>" class="btn btn-outline-primary mt-auto">View details</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
