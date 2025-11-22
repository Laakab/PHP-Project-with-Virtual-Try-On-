</main>
<footer class="site-footer mt-5 pt-5 pb-4 text-white">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4">
        <h4 class="fw-bold mb-3">MyShop</h4>
        <p class="text-white-50 mb-4">Premium lifestyle essentials, delivered with concierge-level service. From everyday basics to statement-making gear, we curate products you can trust.</p>
        <div class="d-flex gap-3">
          <a href="https://www.facebook.com" target="_blank" class="social-pill">Fb</a>
          <a href="https://www.instagram.com" target="_blank" class="social-pill">Ig</a>
          <a href="https://www.twitter.com" target="_blank" class="social-pill">X</a>
        </div>
      </div>
      <div class="col-6 col-lg-2">
        <h6 class="text-uppercase small fw-semibold text-white-50">Shop</h6>
        <ul class="list-unstyled footer-links">
          <li><a href="products.php">All products</a></li>
          <li><a href="collections.php">Collections</a></li>
          <li><a href="inspiration.php">Inspiration</a></li>
          <li><a href="services.php">Services</a></li>
          <li><a href="support.php">Support hub</a></li>
        </ul>
      </div>
      <div class="col-6 col-lg-3">
        <h6 class="text-uppercase small fw-semibold text-white-50">Company</h6>
        <ul class="list-unstyled footer-links">
          <li><a href="about.php">About us</a></li>
          <li><a href="contact.php">Contact</a></li>
          <li><a href="signup.php">Create account</a></li>
          <li><a href="login.php">Log in</a></li>
          <li><a href="checkout.php">Checkout</a></li>
        </ul>
      </div>
      <div class="col-lg-3">
        <h6 class="text-uppercase small fw-semibold text-white-50">Stay in the loop</h6>
        <p class="text-white-50">Early access to drops, community events, and private sale alerts straight to your inbox.</p>
        <form class="newsletter-form" action="contact.php" method="POST">
          <div class="input-group">
            <input type="email" name="newsletter_email" class="form-control" placeholder="you@email.com" required>
            <button class="btn btn-primary" type="submit">Join</button>
          </div>
        </form>
      </div>
    </div>
    <div class="footer-bottom mt-4 pt-4 border-top border-light border-opacity-25 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
      <p class="mb-0 text-white-50">© <?php echo date('Y'); ?> MyShop. All rights reserved.</p>
      <div class="d-flex gap-3 small">
        <a href="support.php" class="text-white-50">Help center</a>
        <a href="services.php" class="text-white-50">Shipping</a>
        <a href="about.php" class="text-white-50">Company</a>
      </div>
    </div>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>