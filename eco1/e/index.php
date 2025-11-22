<?php
include 'includes/header.php';
require_once __DIR__ . '/config.php';

$productModel = getProductModel();
$allProducts = $productModel->getAllProducts();
$featuredProducts = array_slice($allProducts, 0, 4);
$categoryModel = getCategoryModel();
$topCategories = array_slice($categoryModel->getAllCategories(), 0, 4);
$productCount = count($allProducts);
$offerModel = getOfferModel();
$adModel = getAdModel();
$activeOffers = $offerModel->getActiveOffers();
$activeAds = $adModel->getActiveAds();
$adsAndOffers = array_merge($activeOffers ?: [], $activeAds ?: []);
usort($adsAndOffers, function($a, $b) {
  $ca = $a['created_at'] ?? '';
  $cb = $b['created_at'] ?? '';
  return strcmp($cb, $ca);
});
$adsAndOffers = array_slice($adsAndOffers, 0, 3);
?>

<section class="hero-section text-center text-lg-start">
  <div class="row align-items-center g-4">
    <div class="col-lg-6">
      <p class="text-uppercase text-primary small fw-semibold mb-2">New season drop</p>
      <h1 class="display-4 fw-bold">Everything you love, delivered fast.</h1>
      <p class="lead text-muted mb-4">Shop curated collections from trusted sellers. Enjoy secure checkout, flexible returns, and doorstep delivery across Pakistan.</p>
      <div class="d-flex flex-column flex-sm-row gap-3">
        <a href="products.php" class="btn btn-primary btn-lg px-4">Shop new arrivals</a>
        <a href="collections.php" class="btn btn-outline-secondary btn-lg px-4">Browse collections</a>
      </div>
      <div class="d-flex gap-4 mt-4 text-muted small flex-wrap">
        <div><strong class="d-block h4 mb-0"><?php echo max(12, $productCount); ?>+</strong> Curated items</div>
        <div><strong class="d-block h4 mb-0">48h</strong> Fast dispatch</div>
        <div><strong class="d-block h4 mb-0">7d</strong> Easy returns</div>
      </div>
    </div>
    <div class="col-lg-6 text-center">
      <div class="hero-card shadow-lg">
        <img src="../a/images/crowd.png" class="img-fluid" alt="Hero banner">
      </div>
    </div>
  </div>
</section>

<section class="mt-5">
  <div class="row g-4">
    <div class="col-md-4">
      <div class="feature-card">
        <div class="feature-icon bg-primary-subtle text-primary">🚚</div>
        <h5>Nationwide delivery</h5>
        <p class="text-muted mb-0">Trackable shipping to all major cities with cash on delivery support.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-card">
        <div class="feature-icon bg-success-subtle text-success">💳</div>
        <h5>Secure payments</h5>
        <p class="text-muted mb-0">Multiple payment options powered by bank-level encryption.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-card">
        <div class="feature-icon bg-warning-subtle text-warning">⭐</div>
        <h5>Top-rated support</h5>
        <p class="text-muted mb-0">Real humans, instant help. Message us anytime for order assistance.</p>
      </div>
    </div>
  </div>
</section>

<section class="mt-5">
  <div class="section-heading">
    <div>
      <p class="text-uppercase text-primary fw-semibold small mb-1">Offers & ads</p>
      <h2 class="h4 mb-0">Hand-picked deals for you</h2>
    </div>
    <small>Seasonal highlights</small>
  </div>
  <?php if (empty($adsAndOffers)): ?>
    <p class="text-muted">No active offers or ads at the moment.</p>
  <?php else: ?>
    <div class="row g-4">
      <?php foreach ($adsAndOffers as $item): ?>
        <?php
          $isOffer = isset($item['discount']) || isset($item['product_id']) || isset($item['start_date']);
          $imageUrl = format_image_url($item['image_path'] ?? '');
          $title = htmlspecialchars($item['title'] ?? ($isOffer ? 'Offer' : 'Sponsored'));
          $desc = htmlspecialchars($item['description'] ?? '');
          if ($isOffer) {
            $ctaHref = !empty($item['product_id']) ? 'product_detail.php?id=' . (int)$item['product_id'] : 'products.php';
            $ctaLabel = 'View offer';
            $leftBadge = 'Offer';
            $rightBadge = 'Offer';
          } else {
            $ctaHref = 'ad_detail.php?id=' . (int)($item['id'] ?? 0);
            $ctaLabel = 'Learn more';
            $leftBadge = 'Sponsored';
            $rightBadge = 'Ad';
          }
        ?>
        <div class="col-sm-6 col-lg-4">
          <div class="product-card card h-100 border-0 shadow-sm">
            <img src="<?php echo htmlspecialchars($imageUrl); ?>" class="product-thumb" alt="<?php echo $title; ?>" loading="lazy">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge badge-soft"><?php echo htmlspecialchars($leftBadge); ?></span>
                <span class="text-muted small"><?php echo htmlspecialchars($rightBadge); ?></span>
              </div>
              <h5 class="card-title mb-2"><?php echo $title; ?></h5>
              <?php if (!empty($desc)): ?>
                <p class="text-muted mb-3"><?php echo $desc; ?></p>
              <?php endif; ?>
              <a href="<?php echo htmlspecialchars($ctaHref); ?>" class="btn btn-outline-primary w-100"><?php echo htmlspecialchars($ctaLabel); ?></a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<section class="mt-5">
  <div class="section-heading">
    <div>
      <p class="text-uppercase text-primary fw-semibold small mb-1">Shop by category</p>
      <h2 class="h4 mb-0">Collections built around you</h2>
    </div>
    <small>Updated live from your admin catalog</small>
  </div>
  <?php if (!empty($topCategories)): ?>
    <div class="info-grid">
      <?php foreach ($topCategories as $category): ?>
        <div class="info-card">
          <p class="text-uppercase text-primary small fw-semibold mb-1">Category</p>
          <h5 class="mb-2"><?php echo htmlspecialchars($category['name']); ?></h5>
          <p class="text-muted mb-3">Curated picks from <?php echo htmlspecialchars($category['name']); ?> suppliers, refreshed weekly.</p>
          <a href="products.php" class="btn btn-sm btn-outline-primary">Shop now</a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="text-muted">Add categories in the admin dashboard to highlight curated collections here.</p>
  <?php endif; ?>
</section>

<section class="mt-5">
  <div class="section-heading">
    <h2 class="h4 mb-0">Trending picks</h2>
    <a href="products.php" class="btn btn-link px-0">View all products</a>
  </div>
  <?php if (empty($featuredProducts)): ?>
    <p class="text-muted">No products have been published yet. Add some through the admin panel to showcase them here.</p>
  <?php else: ?>
    <div class="row g-4">
      <?php foreach ($featuredProducts as $p): ?>
        <?php
          $imageUrl = format_image_url($p['image'] ?? '');
          $price = (float)($p['price'] ?? 0);
          $discount = (float)($p['discount'] ?? 0);
          $finalPrice = calculate_discounted_price($price, $discount);
          $hasDiscount = $discount > 0;
        ?>
        <div class="col-sm-6 col-lg-3">
          <div class="product-card card h-100 border-0 shadow-sm">
            <img src="<?php echo htmlspecialchars($imageUrl); ?>" class="product-thumb" alt="<?php echo htmlspecialchars($p['name']); ?>" loading="lazy">
            <div class="card-body">
              <h5 class="card-title mb-1"><?php echo htmlspecialchars($p['name']); ?></h5>
              <?php if (!empty($p['category_name'])): ?>
                <p class="text-muted small mb-2"><?php echo htmlspecialchars($p['category_name']); ?></p>
              <?php endif; ?>
              <div class="d-flex align-items-center gap-2 mb-3">
                <span class="fw-semibold"><?php echo format_price($finalPrice); ?></span>
                <?php if ($hasDiscount): ?>
                  <span class="text-muted text-decoration-line-through small"><?php echo format_price($price); ?></span>
                <?php endif; ?>
              </div>
              <a href="product_detail.php?id=<?php echo (int)$p['id']; ?>" class="btn btn-outline-primary w-100">View details</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<section class="mt-5">
  <div class="row g-4">
    <div class="col-md-6">
      <div class="feature-card h-100">
        <p class="text-uppercase text-primary small fw-semibold mb-2">Trusted by modern shoppers</p>
        <h3>Service that feels bespoke</h3>
        <p class="text-muted">From pre-purchase advice to doorstep delivery, our concierge team tracks every order and keeps your customers updated at each milestone.</p>
        <ul class="text-muted list-unstyled mb-0">
          <li>• Live inventory syncing with admin portal</li>
          <li>• SMS and email tracking updates</li>
          <li>• Seamless returns with 7-day window</li>
        </ul>
      </div>
    </div>
    <div class="col-md-6">
      <div class="stat-grid">
        <div class="stat-pill">
          <strong class="d-block h3 mb-1">4.9/5</strong>
          <span class="text-muted small">Customer rating</span>
        </div>
        <div class="stat-pill">
          <strong class="d-block h3 mb-1">72h</strong>
          <span class="text-muted small">Average delivery</span>
        </div>
        <div class="stat-pill">
          <strong class="d-block h3 mb-1">120+</strong>
          <span class="text-muted small">Active sellers</span>
        </div>
        <div class="stat-pill">
          <strong class="d-block h3 mb-1">15</strong>
          <span class="text-muted small">Cities covered</span>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

<?php if (current_user_id()): ?>
  <div class="position-fixed bottom-0 end-0 m-4">
    <button class="btn btn-primary rounded-circle p-3 shadow" id="userChatBtn">
      <span class="visually-hidden">Chat</span>
      💬
    </button>
  </div>

  <div class="modal fade" id="userChatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Chat with Admin</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="userChatMessages" class="d-flex flex-column gap-2"></div>
        </div>
        <div class="modal-footer">
          <div class="input-group">
            <input type="text" class="form-control" id="userChatInput" placeholder="Type a message...">
            <button class="btn btn-primary" id="userChatSend">Send</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    (function(){
      const btn = document.getElementById('userChatBtn');
      const modalEl = document.getElementById('userChatModal');
      const messagesEl = document.getElementById('userChatMessages');
      const inputEl = document.getElementById('userChatInput');
      const sendBtn = document.getElementById('userChatSend');
      let pollTimer = null;

      const modal = new bootstrap.Modal(modalEl);

      function renderMessages(msgs){
        messagesEl.innerHTML = '';
        if (!msgs || msgs.length === 0) {
          const empty = document.createElement('div');
          empty.className = 'text-muted';
          empty.textContent = 'No messages yet.';
          messagesEl.appendChild(empty);
          return;
        }
        msgs.forEach(m => {
          const row = document.createElement('div');
          const isMine = m.sender_type === 'customer';
          row.className = 'd-flex ' + (isMine ? 'justify-content-end' : 'justify-content-start');
          const bubble = document.createElement('div');
          bubble.className = 'badge ' + (isMine ? 'bg-primary' : 'bg-secondary');
          bubble.textContent = m.text;
          row.appendChild(bubble);
          messagesEl.appendChild(row);
        });
      }

      async function loadMessages(){
        try {
          const res = await fetch('../a/controllers/ChatController.php?action=get_customer_messages');
          const data = await res.json();
          renderMessages(data);
        } catch (e) {
          console.error('Chat load failed', e);
        }
      }

      async function sendMessage(){
        const text = (inputEl.value || '').trim();
        if (!text) return;
        try {
          const form = new FormData();
          form.append('action','send_customer_message');
          form.append('text', text);
          await fetch('../a/controllers/ChatController.php', { method: 'POST', body: form });
          inputEl.value = '';
          await loadMessages();
        } catch (e) { console.error('Chat send failed', e); }
      }

      btn.addEventListener('click', () => {
        modal.show();
        loadMessages();
        if (pollTimer) clearInterval(pollTimer);
        pollTimer = setInterval(loadMessages, 3000);
      });

      sendBtn.addEventListener('click', sendMessage);
      inputEl.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
          e.preventDefault();
          sendMessage();
        }
      });

      modalEl.addEventListener('hidden.bs.modal', () => {
        if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
      });
    })();
  </script>
<?php endif; ?>