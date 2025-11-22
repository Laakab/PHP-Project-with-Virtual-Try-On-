<?php
include 'includes/header.php';
require_once __DIR__ . '/config.php';

$productModel = getProductModel();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = $id > 0 ? $productModel->getProductById($id) : null;
?>

<?php if (!$product): ?>
  <div class="alert alert-danger">Product not found.</div>
<?php else: ?>
  <?php
    $imageUrl = format_image_url($product['image'] ?? '');
    $price = (float)$product['price'];
    $discount = (float)($product['discount'] ?? 0);
    $discountedPrice = calculate_discounted_price($price, $discount);
    $hasDiscount = $discount > 0;
    $inStock = (int)($product['quantity'] ?? 0) > 0;
    $relatedProducts = getRelatedProducts((int)$product['id'], 4);
  ?>
  <div class="row g-5 align-items-start product-detail">
    <div class="col-lg-6">
      <div class="product-visual card border-0 shadow-sm">
        <div class="ratio ratio-4x3">
          <img src="<?php echo htmlspecialchars($imageUrl); ?>" class="rounded-top object-fit-cover" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <div class="p-3 d-flex justify-content-between align-items-center">
          <span class="text-muted small">SKU #<?php echo str_pad($product['id'], 5, '0', STR_PAD_LEFT); ?></span>
          <span class="badge <?php echo $inStock ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'; ?>">
            <?php echo $inStock ? 'In stock' : 'Out of stock'; ?>
          </span>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <p class="text-uppercase text-primary small fw-semibold mb-1"><?php echo htmlspecialchars($product['category_name'] ?? 'Trending pick'); ?></p>
      <h1 class="mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>

      <div class="d-flex align-items-center gap-3 mb-4">
        <?php if ($hasDiscount): ?>
          <div>
            <div class="text-muted small text-decoration-line-through"><?php echo format_price($price); ?></div>
            <div class="h2 mb-0 text-success"><?php echo format_price($discountedPrice); ?></div>
          </div>
          <span class="badge bg-success-subtle text-success px-3 py-2"><?php echo $discount; ?>% OFF</span>
        <?php else: ?>
          <div class="h2 mb-0"><?php echo format_price($price); ?></div>
        <?php endif; ?>
      </div>

      <?php if (!empty($product['description'])): ?>
        <p class="lead text-muted"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
      <?php endif; ?>

      <ul class="list-unstyled feature-list">
        <?php if (!empty($product['color'])): ?><li><strong>Color:</strong> <?php echo htmlspecialchars($product['color']); ?></li><?php endif; ?>
        <?php if (!empty($product['size'])): ?><li><strong>Size:</strong> <?php echo htmlspecialchars($product['size']); ?></li><?php endif; ?>
        <?php if (!empty($product['quantity'])): ?><li><strong>Stock:</strong> <?php echo (int)$product['quantity']; ?> pcs available</li><?php endif; ?>
        <?php if (!empty($product['return_days'])): ?><li><strong>Returns:</strong> <?php echo htmlspecialchars($product['return_days']); ?></li><?php endif; ?>
        <?php if (!empty($product['delivery_price'])): ?><li><strong>Delivery:</strong> <?php echo format_price($product['delivery_price']); ?></li><?php endif; ?>
      </ul>

      <form action="cart.php" method="POST" class="mt-4 card border-0 shadow-sm">
        <div class="card-body">
          <input type="hidden" name="product_id" value="<?php echo (int)$product['id']; ?>">
          <input type="hidden" name="action" value="add">
          <div class="row g-3 align-items-end">
            <div class="col-sm-6">
              <label class="form-label">Quantity</label>
              <input type="number" name="quantity" class="form-control form-control-lg" value="1" min="1" max="<?php echo max(1, (int)($product['quantity'] ?? 1)); ?>">
            </div>
            <div class="col-sm-6 d-grid">
              <button type="submit" class="btn btn-success btn-lg" <?php echo $inStock ? '' : 'disabled'; ?>>
                <?php echo $inStock ? 'Add to cart' : 'Out of stock'; ?>
              </button>
            </div>
          </div>
          
          <div class="mt-3 d-grid">
              <a href="../virtual-try-on/frontend/index.html?product_image=<?php echo urlencode($imageUrl); ?>&product_name=<?php echo urlencode($product['name']); ?>" class="btn btn-outline-primary btn-lg">
                  Virtual Try-On
              </a>
          </div>

          <p class="text-muted small mt-3 mb-0">Free exchanges &amp; buyer protection are included with every purchase.</p>
        </div>
      </form>
    </div>
  </div>

  <?php if (!empty($relatedProducts)): ?>
    <section class="mt-5">
      <div class="section-heading">
        <h3 class="h4 mb-0">You may also like</h3>
        <a href="products.php" class="btn btn-link px-0">See catalog</a>
      </div>
      <div class="row g-4">
        <?php foreach ($relatedProducts as $related): ?>
          <?php
            $relatedImage = format_image_url($related['image'] ?? '');
            $relatedPrice = (float)($related['price'] ?? 0);
            $relatedDiscount = (float)($related['discount'] ?? 0);
            $relatedFinal = calculate_discounted_price($relatedPrice, $relatedDiscount);
            $relatedHasDiscount = $relatedDiscount > 0;
          ?>
          <div class="col-sm-6 col-lg-3">
            <div class="product-card card border-0 h-100 shadow-sm">
              <div class="position-relative">
                <img src="<?php echo htmlspecialchars($relatedImage); ?>" class="product-thumb" alt="<?php echo htmlspecialchars($related['name'] ?? 'Product'); ?>" loading="lazy">
                <?php if ($relatedHasDiscount): ?>
                  <span class="badge discount-badge"><?php echo $relatedDiscount; ?>% off</span>
                <?php endif; ?>
              </div>
              <div class="card-body">
                <p class="text-uppercase text-primary small fw-semibold mb-1"><?php echo htmlspecialchars($related['category_name'] ?? 'Featured'); ?></p>
                <h5 class="mb-2"><?php echo htmlspecialchars($related['name']); ?></h5>
                <div class="d-flex align-items-center gap-2 mb-3">
                  <span class="fw-semibold"><?php echo format_price($relatedFinal); ?></span>
                  <?php if ($relatedHasDiscount): ?>
                    <span class="text-muted text-decoration-line-through small"><?php echo format_price($relatedPrice); ?></span>
                  <?php endif; ?>
                </div>
                <a href="product_detail.php?id=<?php echo (int)$related['id']; ?>" class="btn btn-outline-primary w-100">View details</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
