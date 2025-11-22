<?php
include 'includes/header.php';
require_once __DIR__ . '/config.php';

$productModel = getProductModel();
$allProducts = $productModel->getAllProducts();

$collections = [];
foreach ($allProducts as $product) {
    $categoryName = $product['category_name'] ?? 'Featured picks';
    if (!isset($collections[$categoryName])) {
        $collections[$categoryName] = [
            'name' => $categoryName,
            'count' => 0,
            'cover' => null,
            'description' => 'Hand-selected products refreshed weekly.'
        ];
    }
    $collections[$categoryName]['count']++;
    $imageCandidate = format_image_url($product['image'] ?? '');
    if ($collections[$categoryName]['cover'] === null && $imageCandidate !== PLACEHOLDER_IMAGE) {
        $collections[$categoryName]['cover'] = $imageCandidate;
    }
}

$collections = array_values($collections);
$featuredCollections = array_slice($collections, 0, 6);
$highlightProducts = array_slice($allProducts, 0, 6);
?>

<section class="page-hero">
  <p class="text-uppercase text-primary small fw-semibold mb-2">Curated journeys</p>
  <h1 class="display-6 fw-bold mb-3">Collections inspired by how people actually live.</h1>
  <p class="text-muted mb-0">Bundle trending items, spotlight seasonal edits, and give each category its own visual identity without touching the codebase.</p>
</section>

<section class="mt-5">
  <div class="section-heading">
    <h2 class="h4 mb-0">Featured collections</h2>
    <small>Directly powered by your product catalog</small>
  </div>
  <?php if (empty($featuredCollections)): ?>
    <div class="alert alert-info mb-0">Add products with categories to auto-build editorial collections.</div>
  <?php else: ?>
    <div class="row g-4">
      <?php foreach ($featuredCollections as $collection): ?>
        <div class="col-sm-6 col-lg-4">
          <div class="card border-0 shadow-sm h-100">
            <?php if ($collection['cover']): ?>
              <img src="<?php echo htmlspecialchars($collection['cover']); ?>" class="product-thumb" alt="<?php echo htmlspecialchars($collection['name']); ?>" loading="lazy">
            <?php endif; ?>
            <div class="card-body">
              <p class="text-uppercase text-primary small fw-semibold mb-1">Collection</p>
              <h5 class="mb-2"><?php echo htmlspecialchars($collection['name']); ?></h5>
              <p class="text-muted mb-2"><?php echo htmlspecialchars($collection['description']); ?></p>
              <div class="text-muted small mb-3"><?php echo $collection['count']; ?> product<?php echo $collection['count'] === 1 ? '' : 's'; ?></div>
              <a href="products.php" class="btn btn-outline-primary w-100">View products</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<section class="mt-5">
  <div class="section-heading">
    <h2 class="h4 mb-0">Highlights from the catalog</h2>
    <a href="products.php" class="btn btn-link px-0">Browse catalog</a>
  </div>
  <?php if (empty($highlightProducts)): ?>
    <p class="text-muted">No products yet. Add items from the admin dashboard to showcase them here.</p>
  <?php else: ?>
    <div class="row g-4">
      <?php foreach ($highlightProducts as $product): ?>
        <?php
          $imageUrl = format_image_url($product['image'] ?? '');
          $price = (float)($product['price'] ?? 0);
          $discount = (float)($product['discount'] ?? 0);
          $finalPrice = calculate_discounted_price($price, $discount);
          $hasDiscount = $discount > 0;
        ?>
        <div class="col-sm-6 col-lg-4">
          <div class="product-card card border-0 h-100 shadow-sm">
            <div class="position-relative">
              <img src="<?php echo htmlspecialchars($imageUrl); ?>" class="product-thumb" alt="<?php echo htmlspecialchars($product['name'] ?? 'Product'); ?>" loading="lazy">
              <?php if ($hasDiscount): ?>
                <span class="badge discount-badge"><?php echo $discount; ?>% off</span>
              <?php endif; ?>
            </div>
            <div class="card-body">
              <p class="text-uppercase text-primary small fw-semibold mb-1"><?php echo htmlspecialchars($product['category_name'] ?? 'Featured'); ?></p>
              <h5 class="mb-2"><?php echo htmlspecialchars($product['name']); ?></h5>
              <div class="d-flex align-items-center gap-2 mb-3">
                <span class="fw-semibold"><?php echo format_price($finalPrice); ?></span>
                <?php if ($hasDiscount): ?>
                  <span class="text-muted text-decoration-line-through small"><?php echo format_price($price); ?></span>
                <?php endif; ?>
              </div>
              <a href="product_detail.php?id=<?php echo (int)$product['id']; ?>" class="btn btn-outline-primary w-100">View details</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>

