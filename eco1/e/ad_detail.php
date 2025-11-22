<?php
include 'includes/header.php';
require_once __DIR__ . '/config.php';

$adId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$adData = null;
if ($adId > 0) {
    $resp = getAdModel()->getAdById($adId);
    if (is_array($resp) && !empty($resp['success']) && !empty($resp['ad'])) {
        $adData = $resp['ad'];
    }
}

$imageUrl = format_image_url($adData['image_path'] ?? '');
$title = htmlspecialchars($adData['title'] ?? 'Advertisement');
$desc = htmlspecialchars($adData['description'] ?? '');
$company = htmlspecialchars($adData['company_name'] ?? '');
$email = htmlspecialchars($adData['email'] ?? '');
$phone = htmlspecialchars($adData['phone'] ?? '');
$link = $adData['link'] ?? '';
$status = htmlspecialchars($adData['status'] ?? '');
$start = htmlspecialchars($adData['start_datetime'] ?? '');
$end = htmlspecialchars($adData['end_datetime'] ?? '');
?>

<section class="page-hero mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <p class="text-uppercase text-primary fw-semibold small mb-1">Ad detail</p>
      <h2 class="h4 mb-0"><?php echo $title; ?></h2>
    </div>
    <a href="index.php" class="btn btn-outline-secondary">Back to home</a>
  </div>
</section>

<?php if (!$adData): ?>
  <div class="feature-card">
    <p class="text-muted mb-0">This advertisement could not be found.</p>
  </div>
<?php else: ?>
  <div class="row g-4">
    <div class="col-lg-6">
      <div class="product-card card h-100 border-0 shadow-sm">
        <img src="<?php echo htmlspecialchars($imageUrl); ?>" class="product-thumb" alt="<?php echo $title; ?>" loading="lazy">
      </div>
    </div>
    <div class="col-lg-6">
      <div class="feature-card h-100">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="badge badge-soft">Sponsored</span>
          <span class="text-muted small"><?php echo $status; ?></span>
        </div>
        <h3 class="mb-2"><?php echo $title; ?></h3>
        <?php if (!empty($company)): ?>
          <p class="text-muted mb-2">By <?php echo $company; ?></p>
        <?php endif; ?>
        <?php if (!empty($desc)): ?>
          <p class="text-muted mb-3"><?php echo $desc; ?></p>
        <?php endif; ?>
        <div class="d-flex flex-wrap gap-3 mb-3">
          <?php if (!empty($email)): ?>
            <span class="badge badge-soft">Email: <?php echo $email; ?></span>
          <?php endif; ?>
          <?php if (!empty($phone)): ?>
            <span class="badge badge-soft">Phone: <?php echo $phone; ?></span>
          <?php endif; ?>
        </div>
        <div class="d-flex flex-wrap gap-3 mb-4">
          <?php if (!empty($start)): ?>
            <span class="badge badge-soft">Starts: <?php echo $start; ?></span>
          <?php endif; ?>
          <?php if (!empty($end)): ?>
            <span class="badge badge-soft">Ends: <?php echo $end; ?></span>
          <?php endif; ?>
        </div>
        <div class="d-flex gap-2">
          <?php if (!empty($link)): ?>
            <a href="<?php echo htmlspecialchars($link); ?>" target="_blank" class="btn btn-primary">Visit link</a>
          <?php endif; ?>
          <a href="index.php" class="btn btn-outline-secondary">Back</a>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>