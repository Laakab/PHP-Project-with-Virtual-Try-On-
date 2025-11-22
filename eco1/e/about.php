<?php
include 'includes/header.php';
require_once __DIR__ . '/config.php';

$productModel = getProductModel();
$categoryModel = getCategoryModel();
$productCount = count($productModel->getAllProducts());
$categoryCount = count($categoryModel->getAllCategories());
?>

<section class="page-hero">
  <p class="text-uppercase text-primary small fw-semibold mb-2">Our story</p>
  <h1 class="display-6 fw-bold mb-3">Building the most human e-commerce experience.</h1>
  <p class="lead text-muted mb-4">MyShop connects independent makers, heritage brands, and modern buyers through a curated storefront that blends thoughtful storytelling with lightning-fast fulfillment.</p>
  <div class="d-flex flex-wrap gap-3">
    <div class="stat-pill flex-fill text-start">
      <strong class="d-block h3 mb-0"><?php echo max(25, $categoryCount); ?>+</strong>
      <span class="text-muted small">Product categories</span>
    </div>
    <div class="stat-pill flex-fill text-start">
      <strong class="d-block h3 mb-0"><?php echo max(100, $productCount); ?>+</strong>
      <span class="text-muted small">Items curated</span>
    </div>
    <div class="stat-pill flex-fill text-start">
      <strong class="d-block h3 mb-0">98%</strong>
      <span class="text-muted small">On-time dispatch</span>
    </div>
  </div>
</section>

<section class="mt-5">
  <div class="section-heading">
    <h2 class="h4 mb-0">Why we built MyShop</h2>
    <small>Commerce that feels personal</small>
  </div>
  <div class="info-grid">
    <div class="info-card">
      <h5>Discovery with intent</h5>
      <p class="text-muted">We pair editorial storytelling with data-backed recommendations so visitors quickly find products that match their lifestyle.</p>
    </div>
    <div class="info-card">
      <h5>Local-first partnerships</h5>
      <p class="text-muted">Our sourcing team works directly with regional makers, ensuring ethical pricing and transparent production.</p>
    </div>
    <div class="info-card">
      <h5>Service that delights</h5>
      <p class="text-muted">From concierge chat to doorstep delivery, we keep communication crystal clear at every stage.</p>
    </div>
    <div class="info-card">
      <h5>Reliable fulfillment</h5>
      <p class="text-muted">Integrated inventory and logistics make sure items stay in stock, accurate, and ready to ship nationwide.</p>
    </div>
  </div>
</section>

<section class="mt-5">
  <div class="row g-4">
    <div class="col-lg-6">
      <div class="feature-card h-100">
        <p class="text-uppercase text-primary small fw-semibold mb-2">Roadmap</p>
        <h3>What’s next</h3>
        <ul class="text-muted list-unstyled">
          <li>• Launching experiential pop-ups in major cities</li>
          <li>• Introducing same-day delivery in Lahore & Karachi</li>
          <li>• Scaling loyalty with membership-only drops</li>
        </ul>
        <a href="contact.php" class="btn btn-outline-primary mt-3">Partner with us</a>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="feature-card h-100">
        <p class="text-uppercase text-primary small fw-semibold mb-2">Community first</p>
        <h3>What we value</h3>
        <p class="text-muted">We champion slower consumption, thoughtful design, and lasting relationships between buyers and sellers.</p>
        <div class="d-flex gap-3 flex-wrap mt-3">
          <span class="badge badge-soft px-3 py-2">Transparency</span>
          <span class="badge badge-soft px-3 py-2">Sustainability</span>
          <span class="badge badge-soft px-3 py-2">Inclusivity</span>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

