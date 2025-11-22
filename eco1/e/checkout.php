<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config.php';

$customerId = current_user_id();
if (!$customerId) {
    header('Location: login.php?redirect=' . urlencode('checkout.php'));
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$orderPlaced = false;
$orderId = null;
$formErrors = [];
$formSuccess = null;
$productModel = getProductModel();
$items = [];
$total = 0;
$cartSource = $_SESSION['cart'];

foreach ($cartSource as $productId => $qty) {
    $product = $productModel->getProductById((int)$productId);
    if (!$product) {
        continue;
    }
    $price = (float)$product['price'];
    $discount = (float)($product['discount'] ?? 0);
    $discountedPrice = calculate_discounted_price($price, $discount);
    $lineTotal = $discountedPrice * $qty;
    $total += $lineTotal;
    $items[] = [
        'product' => $product,
        'qty' => $qty,
        'line_total' => $lineTotal,
        'unit_price' => $discountedPrice,
        'discount' => $discount,
        'image_url' => format_image_url($product['image'] ?? '')
    ];
}

$canCheckout = !empty($items);

$formData = [
    'name' => $_SESSION['user_name'] ?? '',
    'email' => $_SESSION['user_email'] ?? '',
    'phone' => '',
    'address' => '',
    'city' => '',
    'province' => '',
    'country' => 'Pakistan',
    'payment_method' => 'cod'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'name' => trim($_POST['name'] ?? ''),
        'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?: '',
        'phone' => trim($_POST['phone'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'city' => trim($_POST['city'] ?? ''),
        'province' => trim($_POST['province'] ?? ''),
        'country' => trim($_POST['country'] ?? 'Pakistan'),
        'payment_method' => $_POST['payment_method'] ?? 'cod'
    ];

    if (!$canCheckout) {
        $formErrors[] = 'Your cart is empty. Please add items before checking out.';
    }

    if ($formData['name'] === '') {
        $formErrors[] = 'Full name is required.';
    }

    if ($formData['email'] === '' || !filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $formErrors[] = 'A valid email is required.';
    }

    if ($formData['address'] === '') {
        $formErrors[] = 'Delivery address is required.';
    }

    $allowedPayments = ['cod', 'card'];
    if (!in_array($formData['payment_method'], $allowedPayments, true)) {
        $formErrors[] = 'Please select a valid payment method.';
    }

    if (empty($formErrors)) {
        try {
            $orderId = persist_order(
                $customerId,
                [
                    'name' => $formData['name'],
                    'email' => $formData['email'],
                    'phone' => $formData['phone'],
                    'address' => $formData['address'],
                    'city' => $formData['city'],
                    'province' => $formData['province'],
                    'country' => $formData['country'],
                    'payment_method' => $formData['payment_method'],
                    'delivery' => 0,
                    'status' => 'Pending'
                ],
                array_map(function ($item) {
                    return [
                        'product_id' => $item['product']['id'],
                        'name' => $item['product']['name'],
                        'qty' => $item['qty'],
                        'unit_price' => $item['unit_price'],
                        'line_total' => $item['line_total']
                    ];
                }, $items),
                $total
            );

            $_SESSION['cart'] = [];
            $items = [];
            $total = 0;
            $canCheckout = false;
            $orderPlaced = true;
            $formSuccess = 'Order placed successfully! Reference ID #' . $orderId . '. We will contact you shortly.';
        } catch (Throwable $e) {
            error_log('Order persistence failed: ' . $e->getMessage());
            $formErrors[] = 'We could not process your order right now. Please try again.';
        }
    }
}

include 'includes/header.php';
?>

<section class="mb-4">
  <p class="text-uppercase text-primary small fw-semibold mb-1">Almost there</p>
  <h2 class="mb-0">Secure checkout</h2>
</section>

<?php if (!empty($formErrors)): ?>
  <div class="alert alert-danger shadow-sm">
    <ul class="mb-0">
      <?php foreach ($formErrors as $error): ?>
        <li><?php echo htmlspecialchars($error); ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<?php if ($formSuccess): ?>
  <div class="alert alert-success shadow-sm">
    <?php echo htmlspecialchars($formSuccess); ?>
  </div>
<?php endif; ?>

<div class="row g-4">
  <div class="col-md-7">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body">
        <h4 class="mb-3">Billing details</h4>
        <?php if (!$canCheckout): ?>
          <div class="alert alert-info">Add at least one product to your cart before completing checkout.</div>
        <?php endif; ?>
        <form action="" method="POST" class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($formData['name']); ?>" <?php echo $canCheckout ? 'required' : 'disabled'; ?>>
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($formData['email']); ?>" <?php echo $canCheckout ? 'required' : 'disabled'; ?>>
          </div>
          <div class="col-md-6">
            <label class="form-label">Phone (optional)</label>
            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($formData['phone']); ?>" <?php echo $canCheckout ? '' : 'disabled'; ?>>
          </div>
          <div class="col-md-6">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($formData['address']); ?>" <?php echo $canCheckout ? 'required' : 'disabled'; ?>>
          </div>
          <div class="col-md-6">
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($formData['city']); ?>" <?php echo $canCheckout ? '' : 'disabled'; ?>>
          </div>
          <div class="col-md-6">
            <label class="form-label">Province</label>
            <input type="text" name="province" class="form-control" value="<?php echo htmlspecialchars($formData['province']); ?>" <?php echo $canCheckout ? '' : 'disabled'; ?>>
          </div>
          <div class="col-md-6">
            <label class="form-label">Country</label>
            <input type="text" name="country" class="form-control" value="<?php echo htmlspecialchars($formData['country']); ?>" <?php echo $canCheckout ? '' : 'disabled'; ?>>
          </div>
          <div class="col-md-6">
            <label class="form-label">Payment Method</label>
            <select name="payment_method" class="form-select" <?php echo $canCheckout ? '' : 'disabled'; ?>>
              <option value="cod" <?php echo $formData['payment_method'] === 'cod' ? 'selected' : ''; ?>>Cash on Delivery</option>
              <option value="card" <?php echo $formData['payment_method'] === 'card' ? 'selected' : ''; ?>>Credit Card</option>
            </select>
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-success btn-lg w-100" <?php echo $canCheckout ? '' : 'disabled'; ?>>
              Place order securely
            </button>
            <p class="text-muted small text-center mt-3 mb-0">By placing your order, you agree to our terms &amp; return policy.</p>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-5">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body">
        <h4 class="mb-3">Order summary</h4>
        <?php if (empty($items)): ?>
          <p class="text-muted">Your cart is empty. Browse products to add items.</p>
        <?php else: ?>
          <ul class="list-group mb-3">
            <?php foreach ($items as $item): ?>
              <li class="list-group-item border-0 px-0 py-3">
                <div class="d-flex gap-3">
                  <img src="<?php echo htmlspecialchars($item['image_url']); ?>" class="cart-thumb rounded" alt="<?php echo htmlspecialchars($item['product']['name']); ?>">
                  <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                      <div>
                        <strong><?php echo htmlspecialchars($item['product']['name']); ?></strong>
                        <div class="text-muted small">Qty: <?php echo (int)$item['qty']; ?></div>
                      </div>
                      <span><?php echo format_price($item['line_total']); ?></span>
                    </div>
                    <?php if ($item['discount'] > 0): ?>
                      <span class="badge bg-success-subtle text-success mt-2"><?php echo $item['discount']; ?>% off applied</span>
                    <?php endif; ?>
                  </div>
                </div>
              </li>
            <?php endforeach; ?>
            <li class="list-group-item d-flex justify-content-between border-top">
              <strong>Total</strong>
              <strong><?php echo format_price($total); ?></strong>
            </li>
          </ul>
        <?php endif; ?>
        <p class="text-muted small mb-0">Transactions are secured with SSL encryption.</p>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
