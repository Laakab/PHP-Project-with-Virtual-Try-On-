<?php
require_once __DIR__ . '/models/Database.php';

$pdo = (new Database())->getConnection();
$alert = null;
$alertType = 'success';

function fetchOrders(PDO $pdo): array {
    $stmt = $pdo->query("
        SELECT o.*, s.name AS user_name, s.email AS user_email
        FROM orders o
        LEFT JOIN signup s ON o.user_id = s.id
        ORDER BY o.order_date DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $orderId = (int)($_POST['order_id'] ?? 0);

    if (!$orderId) {
        $alert = 'Invalid order selected.';
        $alertType = 'danger';
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM orders WHERE id = :id');
        $stmt->execute([':id' => $orderId]);
        $alert = 'Order removed successfully.';
    } elseif ($action === 'update') {
        $status = $_POST['status'] ?? 'Pending';
        $allowedStatuses = ['Pending', 'Processing', 'Completed', 'Cancelled'];
        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'Pending';
        }
        $stmt = $pdo->prepare('UPDATE orders SET status1 = :status WHERE id = :id');
        $stmt->execute([
            ':status' => $status,
            ':id' => $orderId
        ]);
        $alert = 'Order status updated.';
    }
}

$orders = fetchOrders($pdo);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Control</title>
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
        .order-items {
            font-size: .85rem;
            color: #475569;
        }
        .badge-status {
            font-size: .75rem;
            padding: .35rem .65rem;
            border-radius: 999px;
        }
        .badge-status.Pending { background: rgba(251, 191, 36, 0.2); color: #b45309; }
        .badge-status.Processing { background: rgba(59, 130, 246, 0.2); color: #1e40af; }
        .badge-status.Completed { background: rgba(34, 197, 94, 0.2); color: #166534; }
        .badge-status.Cancelled { background: rgba(239, 68, 68, 0.2); color: #991b1b; }
        @media (max-width: 768px) {
            table {
                font-size: .85rem;
            }
            .actions-column {
                min-width: 180px;
            }
        }
    </style>
</head>
<body class="p-3 p-md-4">

<div class="card rounded-4">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <p class="text-uppercase text-primary fw-semibold small mb-1">Operations</p>
                <h2 class="h4 mb-0">Orders overview</h2>
            </div>
            <span class="badge bg-dark-subtle text-dark"><?php echo count($orders); ?> orders synced</span>
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
                    <th>Customer</th>
                    <th>Payment</th>
                    <th>Total</th>
                    <th>Items</th>
                    <th>Status</th>
                    <th class="actions-column">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            No orders have been recorded yet.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <?php
                        $itemsSnapshot = json_decode($order['items_snapshot'] ?? '[]', true) ?? [];
                        ?>
                        <tr>
                            <td class="fw-semibold">#<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?></td>
                            <td>
                                <div class="fw-semibold"><?php echo htmlspecialchars($order['customer_name'] ?? $order['user_name'] ?? 'Guest'); ?></div>
                                <div class="text-muted small"><?php echo htmlspecialchars($order['customer_email'] ?? $order['user_email'] ?? ''); ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark text-uppercase"><?php echo htmlspecialchars($order['payment_method']); ?></span>
                            </td>
                            <td>
                                <div class="fw-semibold">PKR <?php echo number_format($order['total'], 0); ?></div>
                                <div class="text-muted small"><?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></div>
                            </td>
                            <td>
                                <ul class="list-unstyled mb-0 order-items">
                                    <?php foreach ($itemsSnapshot as $snapshot): ?>
                                        <li>• <?php echo htmlspecialchars($snapshot['name'] ?? 'Product'); ?> × <?php echo (int)($snapshot['qty'] ?? 1); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                            <td>
                                <span class="badge-status <?php echo htmlspecialchars($order['status1']); ?>">
                                    <?php echo htmlspecialchars($order['status1']); ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" class="d-flex flex-column flex-md-row gap-2">
                                    <input type="hidden" name="order_id" value="<?php echo (int)$order['id']; ?>">
                                    <input type="hidden" name="action" value="update">
                                    <select name="status" class="form-select form-select-sm">
                                        <?php
                                        $states = ['Pending', 'Processing', 'Completed', 'Cancelled'];
                                        foreach ($states as $state):
                                        ?>
                                            <option value="<?php echo $state; ?>" <?php echo $order['status1'] === $state ? 'selected' : ''; ?>>
                                                <?php echo $state; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-sm btn-primary" type="submit">Save</button>
                                </form>
                                <form method="POST" class="mt-2" onsubmit="return confirm('Delete this order?');">
                                    <input type="hidden" name="order_id" value="<?php echo (int)$order['id']; ?>">
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

