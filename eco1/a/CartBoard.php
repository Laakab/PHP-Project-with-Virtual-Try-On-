<?php
require_once __DIR__ . '/models/Database.php';

$pdo = (new Database())->getConnection();
$alert = null;
$alertType = 'success';

function fetchCartRows(PDO $pdo): array {
    $stmt = $pdo->query("
        SELECT c.*, s.name AS user_name, s.email AS user_email,
               p.name AS product_name
        FROM addtocart c
        LEFT JOIN signup s ON c.user_id = s.id
        LEFT JOIN products p ON c.product_id = p.id
        ORDER BY c.created_at DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $rowId = (int)($_POST['row_id'] ?? 0);

    if (!$rowId) {
        $alert = 'Invalid record selected.';
        $alertType = 'danger';
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM addtocart WHERE id = :id');
        $stmt->execute([':id' => $rowId]);
        $alert = 'Cart row removed.';
    } elseif ($action === 'update') {
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));
        $unitPrice = (float)($_POST['unit_price'] ?? 0);
        $stmt = $pdo->prepare('UPDATE addtocart SET quantity = :qty, unit_price = :price, line_total = :total WHERE id = :id');
        $stmt->execute([
            ':qty' => $quantity,
            ':price' => $unitPrice,
            ':total' => $quantity * $unitPrice,
            ':id' => $rowId
        ]);
        $alert = 'Cart row updated.';
    }
}

$cartRows = fetchCartRows($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add to Cart Monitor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f8fafc;
        }
        .card {
            border: none;
            box-shadow: 0 12px 40px rgba(15, 23, 42, 0.08);
        }
        .table thead th {
            text-transform: uppercase;
            font-size: .75rem;
            letter-spacing: .05em;
            color: #64748b;
        }
        .actions-column {
            min-width: 220px;
        }
        @media (max-width: 768px) {
            table {
                font-size: .85rem;
            }
        }
    </style>
</head>
<body class="p-3 p-md-4">

<div class="card rounded-4">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <p class="text-uppercase text-primary fw-semibold small mb-1">Live carts</p>
                <h2 class="h4 mb-0">Customer basket monitor</h2>
            </div>
            <span class="badge bg-dark-subtle text-dark"><?php echo count($cartRows); ?> rows tracked</span>
        </div>

        <?php if ($alert): ?>
            <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($alert); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit price</th>
                    <th>Total</th>
                    <th class="actions-column">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($cartRows)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No cart entries yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($cartRows as $cart): ?>
                        <tr>
                            <td class="fw-semibold">#<?php echo str_pad($cart['id'], 4, '0', STR_PAD_LEFT); ?></td>
                            <td>
                                <div class="fw-semibold"><?php echo htmlspecialchars($cart['user_name'] ?? 'User #' . $cart['user_id']); ?></div>
                                <div class="text-muted small"><?php echo htmlspecialchars($cart['user_email'] ?? ''); ?></div>
                            </td>
                            <td>
                                <div class="fw-semibold"><?php echo htmlspecialchars($cart['product_name'] ?? 'Product #' . $cart['product_id']); ?></div>
                                <div class="text-muted small">Product ID: <?php echo (int)$cart['product_id']; ?></div>
                            </td>
                            <td><?php echo (int)$cart['quantity']; ?></td>
                            <td>PKR <?php echo number_format($cart['unit_price'], 0); ?></td>
                            <td class="fw-semibold">PKR <?php echo number_format($cart['line_total'], 0); ?></td>
                            <td>
                                <form method="POST" class="d-flex flex-column flex-lg-row gap-2 align-items-stretch align-items-lg-center">
                                    <input type="hidden" name="row_id" value="<?php echo (int)$cart['id']; ?>">
                                    <input type="hidden" name="action" value="update">
                                    <input type="number" name="quantity" class="form-control form-control-sm" min="1" value="<?php echo (int)$cart['quantity']; ?>">
                                    <input type="number" name="unit_price" class="form-control form-control-sm" step="0.01" value="<?php echo (float)$cart['unit_price']; ?>">
                                    <button class="btn btn-sm btn-primary" type="submit">Update</button>
                                </form>
                                <form method="POST" class="mt-2" onsubmit="return confirm('Delete this cart row?');">
                                    <input type="hidden" name="row_id" value="<?php echo (int)$cart['id']; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button class="btn btn-sm btn-outline-danger w-100" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

