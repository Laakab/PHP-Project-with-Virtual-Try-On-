<?php
require_once __DIR__ . '/models/Database.php';
$pdo = (new Database())->getConnection();
$totals = [
  'orders' => 0,
  'adds' => 0
];
$stmt = $pdo->query('SELECT COUNT(*) AS c FROM orders');
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$totals['orders'] = (int)($row['c'] ?? 0);
$stmt = $pdo->query('SELECT COALESCE(SUM(quantity),0) AS c FROM addtocart');
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$totals['adds'] = (int)($row['c'] ?? 0);
$days = [];
for ($i = 6; $i >= 0; $i--) {
  $days[] = date('Y-m-d', strtotime("-{$i} day"));
}
$ordersByDay = array_fill_keys($days, 0);
$addsByDay = array_fill_keys($days, 0);
$stmt = $pdo->prepare('SELECT DATE(order_date) AS d, COUNT(*) AS c FROM orders WHERE order_date >= CURDATE() - INTERVAL 6 DAY GROUP BY d');
$stmt->execute();
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
  $d = $r['d'];
  if (isset($ordersByDay[$d])) $ordersByDay[$d] = (int)$r['c'];
}
$stmt = $pdo->prepare('SELECT DATE(created_at) AS d, COALESCE(SUM(quantity),0) AS c FROM addtocart WHERE created_at >= CURDATE() - INTERVAL 6 DAY GROUP BY d');
$stmt->execute();
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
  $d = $r['d'];
  if (isset($addsByDay[$d])) $addsByDay[$d] = (int)$r['c'];
}
$maxVal = max(1, max($ordersByDay), max($addsByDay));
function segmentsFor($value, $max) {
  $seg = (int)ceil(($value / $max) * 12);
  if ($seg < 0) $seg = 0;
  if ($seg > 12) $seg = 12;
  return $seg;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Activity Graph</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-3 p-md-4">
  <div class="container">
    <div class="mb-4">
      <p class="text-uppercase text-primary fw-semibold small mb-1">Dashboard</p>
      <h1 class="h4 mb-0">Orders & Add to Cart (Last 7 days)</h1>
    </div>
    <div class="row g-3 mb-4">
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h5 class="card-title mb-0">Total Orders</h5>
              <span class="badge bg-success"><?php echo number_format($totals['orders']); ?></span>
            </div>
            <p class="text-muted mb-0">All-time count of orders</p>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h5 class="card-title mb-0">Total Added to Cart</h5>
              <span class="badge bg-warning text-dark"><?php echo number_format($totals['adds']); ?></span>
            </div>
            <p class="text-muted mb-0">All-time quantity added</p>
          </div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="card-title mb-0">Daily Activity</h5>
          <span class="text-muted small">Scaled to 12 segments</span>
        </div>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Day</th>
                <th>Orders</th>
                <th>Add to Cart</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($days as $d): ?>
                <?php $o = $ordersByDay[$d]; $a = $addsByDay[$d]; ?>
                <?php $os = segmentsFor($o, $maxVal); $as = segmentsFor($a, $maxVal); ?>
                <tr>
                  <td class="text-muted small"><?php echo date('D, M j', strtotime($d)); ?></td>
                  <td>
                    <div class="row g-1">
                      <?php for ($i = 0; $i < $os; $i++): ?>
                        <div class="col-1">
                          <div class="bg-success rounded-1 py-2"></div>
                        </div>
                      <?php endfor; ?>
                      <div class="col text-end"><span class="badge bg-success"><?php echo $o; ?></span></div>
                    </div>
                  </td>
                  <td>
                    <div class="row g-1">
                      <?php for ($i = 0; $i < $as; $i++): ?>
                        <div class="col-1">
                          <div class="bg-warning rounded-1 py-2"></div>
                        </div>
                      <?php endfor; ?>
                      <div class="col text-end"><span class="badge bg-warning text-dark"><?php echo $a; ?></span></div>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

