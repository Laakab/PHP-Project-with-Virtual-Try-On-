<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config.php';

$customerId = current_user_id();
if (!$customerId) {
    header('Location: login.php?redirect=' . urlencode('cart.php'));
    exit;
}

include 'includes/header.php';

// Initialize cart in session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$productModel = getProductModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'add';
    $productId = (int)($_POST['product_id'] ?? 0);
    $qty = max(1, (int)($_POST['quantity'] ?? 1));

    switch ($action) {
        case 'update':
            if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId] = $qty;
                $_SESSION['cart_notice'] = 'Cart updated successfully.';
            }
            break;
        case 'remove':
            if ($productId > 0) {
                unset($_SESSION['cart'][$productId]);
                $_SESSION['cart_notice'] = 'Item removed from cart.';
            }
            break;
        case 'clear':
            $_SESSION['cart'] = [];
            $_SESSION['cart_notice'] = 'Your cart has been cleared.';
            break;
        case 'add':
        default:
            if ($productId > 0) {
                $product = $productModel->getProductById($productId);
                if ($product) {
                    if (isset($_SESSION['cart'][$productId])) {
                        $_SESSION['cart'][$productId] += $qty;
                    } else {
                        $_SESSION['cart'][$productId] = $qty;
                    }
                    $price = (float)$product['price'];
                    $discount = (float)($product['discount'] ?? 0);
                    $discountedPrice = calculate_discounted_price($price, $discount);
                    try {
                        save_cart_row($customerId, $productId, $qty, $discountedPrice);
                    } catch (Throwable $e) {
                        error_log('Cart persistence failed: ' . $e->getMessage());
                    }
                    $_SESSION['cart_notice'] = 'Great choice! Item added to your cart.';
                }
            }
            break;
    }

    header('Location: cart.php');
    exit;
}

if (isset($_GET['remove'])) {
    $removeId = (int)$_GET['remove'];
    if ($removeId > 0 && isset($_SESSION['cart'][$removeId])) {
        unset($_SESSION['cart'][$removeId]);
        $_SESSION['cart_notice'] = 'Item removed from cart.';
    }
    header('Location: cart.php');
    exit;
}

$cartItems = [];
$grandTotal = 0;

foreach ($_SESSION['cart'] as $productId => $qty) {
    $product = $productModel->getProductById($productId);
    if (!$product) {
        unset($_SESSION['cart'][$productId]);
        continue;
    }
    $price = (float)$product['price'];
    $discount = (float)($product['discount'] ?? 0);
    $discountedPrice = calculate_discounted_price($price, $discount);
    $lineTotal = $discountedPrice * $qty;
    $grandTotal += $lineTotal;

    $cartItems[] = [
        'product' => $product,
        'qty' => $qty,
        'price' => $discountedPrice,
        'original_price' => $price,
        'discount' => $discount,
        'line_total' => $lineTotal,
        'image_url' => format_image_url($product['image'] ?? '')
    ];
}

$cartNotice = $_SESSION['cart_notice'] ?? null;
unset($_SESSION['cart_notice']);
?>

<section class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
  <div>
    <p class="text-uppercase text-primary small fw-semibold mb-1">Your shopping bag</p>
    <h2 class="mb-0">Cart overview</h2>
  </div>
  <?php if (!empty($cartItems)): ?>
    <form action="cart.php" method="POST">
      <input type="hidden" name="action" value="clear">
      <button type="submit" class="btn btn-outline-danger btn-sm">Clear cart</button>
    </form>
  <?php endif; ?>
</section>

<?php if ($cartNotice): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo htmlspecialchars($cartNotice); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<?php if (empty($cartItems)): ?>
  <div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
      <img src="images/placeholder-product.svg" class="empty-state-icon" alt="Empty cart">
      <h3 class="mt-4">Your cart is empty</h3>
      <p class="text-muted">Add a few products to see them here. Discounts apply automatically when available.</p>
      <a href="products.php" class="btn btn-primary btn-lg mt-2">Browse products</a>
    </div>
  </div>
<?php else: ?>
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="table-responsive shadow-sm rounded-3 bg-white">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Product</th>
              <th style="width: 160px;">Quantity</th>
              <th>Price</th>
              <th>Total</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cartItems as $item): ?>
              <tr>
                <td>
                  <div class="d-flex align-items-center gap-3">
                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" class="cart-thumb rounded" alt="<?php echo htmlspecialchars($item['product']['name']); ?>">
                    <div>
                      <strong><?php echo htmlspecialchars($item['product']['name']); ?></strong>
                      <?php if (!empty($item['product']['category_name'])): ?>
                        <div class="text-muted small"><?php echo htmlspecialchars($item['product']['category_name']); ?></div>
                      <?php endif; ?>
                      <?php if ($item['discount'] > 0): ?>
                        <span class="badge bg-success-subtle text-success mt-1"><?php echo $item['discount']; ?>% off</span>
                      <?php endif; ?>
                    </div>
                  </div>
                </td>
                <td>
                  <form action="cart.php" method="POST" class="d-flex gap-2 align-items-center">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="product_id" value="<?php echo (int)$item['product']['id']; ?>">
                    <input type="number" name="quantity" class="form-control form-control-sm" value="<?php echo (int)$item['qty']; ?>" min="1">
                    <button class="btn btn-outline-secondary btn-sm" type="submit">Update</button>
                  </form>
                </td>
                <td>
                  <div class="fw-semibold"><?php echo format_price($item['price']); ?></div>
                  <?php if ($item['discount'] > 0): ?>
                    <div class="text-muted small text-decoration-line-through"><?php echo format_price($item['original_price']); ?></div>
                  <?php endif; ?>
                </td>
                <td class="fw-semibold"><?php echo format_price($item['line_total']); ?></td>
                <td>
                  <form action="cart.php" method="POST">
                    <input type="hidden" name="action" value="remove">
                    <input type="hidden" name="product_id" value="<?php echo (int)$item['product']['id']; ?>">
                    <button class="btn btn-link text-danger p-0" type="submit">Remove</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h4 class="mb-4">Order summary</h4>
          <div class="d-flex justify-content-between mb-2">
            <span>Subtotal</span>
            <span><?php echo format_price($grandTotal); ?></span>
          </div>
          <div class="d-flex justify-content-between text-muted small mb-4">
            <span>Shipping</span>
            <span>Calculated at checkout</span>
          </div>
          <div class="d-flex justify-content-between align-items-center mb-4">
            <strong>Grand total</strong>
            <span class="fs-4 text-success"><?php echo format_price($grandTotal); ?></span>
          </div>
          <a href="checkout.php" class="btn btn-primary btn-lg w-100">Proceed to checkout</a>
          <p class="text-muted small text-center mt-3 mb-0">Need help? <a href="contact.php">Contact us</a></p>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
