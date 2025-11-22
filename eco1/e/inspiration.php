<?php
include 'includes/header.php';
require_once __DIR__ . '/config.php';

$productModel = getProductModel();
$allProducts = $productModel->getAllProducts();
$lookbookProducts = array_slice($allProducts, 0, 6);
$editorialSets = [
    [
        'title' => 'Workspace reset',
        'description' => 'Minimal desks, breathable seating, and statement lighting for hybrid offices.',
        'badge' => 'Home & Office'
    ],
    [
        'title' => 'Weekend escapes',
        'description' => 'Travel-ready luggage, all-weather sneakers, and layering essentials.',
        'badge' => 'Travel'
    ],
    [
        'title' => 'City athleisure',
        'description' => 'Performance fabrics meet elevated tailoring for all-day comfort.',
        'badge' => 'Fashion'
    ],
];
?>

<section class="page-hero">
  <p class="text-uppercase text-primary small fw-semibold mb-2">Inspiration hub</p>
  <h1 class="display-6 fw-bold mb-3">Visual stories that pair complementary products together.</h1>
  <p class="text-muted mb-0">Use this space to highlight how items work together, educate your audience, and drive larger basket sizes.</p>
</section>

<section class="mt-5">
  <div class="section-heading">
    <h2 class="h4 mb-0">Editorial sets</h2>
    <small>Update copy & imagery from the admin panel</small>
  </div>
  <div class="row g-4">
    <?php foreach ($editorialSets as $set): ?>
      <div class="col-md-4">
        <div class="guide-card h-100">
          <span class="badge badge-soft mb-3"><?php echo htmlspecialchars($set['badge']); ?></span>
          <h5><?php echo htmlspecialchars($set['title']); ?></h5>
          <p class="text-muted mb-0"><?php echo htmlspecialchars($set['description']); ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="mt-5">
  <div class="section-heading">
    <h2 class="h4 mb-0">Lookbook: trending pairings</h2>
    <a href="products.php" class="btn btn-link px-0">View all</a>
  </div>
  <?php if (empty($lookbookProducts)): ?>
    <p class="text-muted">Add products via the admin console to populate this lookbook.</p>
  <?php else: ?>
    <div class="row g-4">
      <?php foreach ($lookbookProducts as $product): ?>
        <?php
          $imageUrl = format_image_url($product['image'] ?? '');
          $price = (float)($product['price'] ?? 0);
          $discount = (float)($product['discount'] ?? 0);
          $finalPrice = calculate_discounted_price($price, $discount);
          $hasDiscount = $discount > 0;
        ?>
        <div class="col-sm-6 col-lg-4">
          <div class="product-card card border-0 shadow-sm h-100">
            <div class="position-relative">
              <img src="<?php echo htmlspecialchars($imageUrl); ?>" class="product-thumb" alt="<?php echo htmlspecialchars($product['name'] ?? 'Product'); ?>" loading="lazy">
              <?php if ($hasDiscount): ?>
                <span class="badge discount-badge"><?php echo $discount; ?>% off</span>
              <?php endif; ?>
            </div>
            <div class="card-body">
              <p class="text-uppercase text-primary small fw-semibold mb-1"><?php echo htmlspecialchars($product['category_name'] ?? 'Featured'); ?></p>
              <h5 class="mb-2"><?php echo htmlspecialchars($product['name']); ?></h5>
              <p class="text-muted small mb-3">Pair it with complimentary accessories to build a complete look.</p>
              <div class="d-flex align-items-center gap-2 mb-3">
                <span class="fw-semibold"><?php echo format_price($finalPrice); ?></span>
                <?php if ($hasDiscount): ?>
                  <span class="text-muted text-decoration-line-through small"><?php echo format_price($price); ?></span>
                <?php endif; ?>
              </div>
              <a href="product_detail.php?id=<?php echo (int)$product['id']; ?>" class="btn btn-outline-primary w-100">See details</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>

