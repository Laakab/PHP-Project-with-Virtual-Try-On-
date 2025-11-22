<?php
include 'includes/header.php';

$messageSent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $messageSent = true;
}
?>

<section class="page-hero mb-4">
  <p class="text-uppercase text-primary small fw-semibold mb-2">Support team</p>
  <h2 class="mb-2">We are here for you 7 days a week</h2>
  <p class="text-muted mb-0">Leave us your query and our customer success team will call or email you back within 24 hours.</p>
</section>

<?php if ($messageSent): ?>
  <div class="alert alert-success">Thank you! Your message has been received. We will get back to you shortly.</div>
<?php endif; ?>

<div class="row g-4">
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body">
        <h4 class="mb-3">Send us a message</h4>
        <form action="contact.php" method="POST" class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="col-12">
            <label class="form-label">Topic</label>
            <select name="topic" class="form-select">
              <option value="order">Order status</option>
              <option value="returns">Returns &amp; refunds</option>
              <option value="partnerships">Partnerships</option>
              <option value="other">Something else</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Message</label>
            <textarea name="message" rows="5" class="form-control" required></textarea>
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary">Send message</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body">
        <h4 class="mb-3">Visit or call</h4>
        <ul class="list-unstyled">
          <li class="mb-3">
            <strong>Storefront</strong>
            <div class="text-muted">123 Commerce Street, Lahore, Pakistan</div>
          </li>
          <li class="mb-3">
            <strong>Customer care</strong>
            <div class="text-muted">+92 300 1234567</div>
          </li>
          <li class="mb-3">
            <strong>Email</strong>
            <div class="text-muted">support@myshop.pk</div>
          </li>
          <li>
            <strong>Hours</strong>
            <div class="text-muted">Monday - Sunday, 9:00 AM - 10:00 PM PKT</div>
          </li>
        </ul>
        <div class="ratio ratio-16x9 rounded overflow-hidden mt-3">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d108887.15449770558!2d74.22955625000001!3d31.5820453!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39190483b57de5a9%3A0xc8da8a7e5b28b467!2sLahore!5e0!3m2!1sen!2s!4v1700000000!5m2!1sen!2s"
            width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>