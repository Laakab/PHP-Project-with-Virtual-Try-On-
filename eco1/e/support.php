<?php
include 'includes/header.php';
?>

<section class="page-hero">
  <p class="text-uppercase text-primary small fw-semibold mb-2">Support hub</p>
  <h1 class="display-6 fw-bold mb-3">Real humans, rapid responses.</h1>
  <p class="text-muted mb-0">Reach out via chat, email, or phone. We keep every order updated with proactive notifications.</p>
</section>

<section class="mt-5">
  <div class="row g-4">
    <div class="col-md-4">
      <div class="support-card h-100">
        <h5>Live chat</h5>
        <p class="text-muted mb-1">Available 9am – 11pm PKT</p>
        <a href="contact.php" class="btn btn-outline-primary btn-sm">Start chat</a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="support-card h-100">
        <h5>Call us</h5>
        <p class="text-muted mb-1">UAN: +92 42 1234 5678</p>
        <span class="text-muted small">Response time: &lt; 2 minutes</span>
      </div>
    </div>
    <div class="col-md-4">
      <div class="support-card h-100">
        <h5>Email</h5>
        <p class="text-muted mb-1">support@myshop.pk</p>
        <span class="text-muted small">We reply within 6 business hours.</span>
      </div>
    </div>
  </div>
</section>

<section class="mt-5">
  <div class="section-heading">
    <h2 class="h4 mb-0">FAQs</h2>
    <small>Answers to common product & order questions</small>
  </div>
  <div class="accordion" id="faqAccordion">
    <?php
    $faqs = [
      ['question' => 'How do I track my order?', 'answer' => 'Once your package ships you will receive an SMS and email with a live tracking link. You can also view the status from your account dashboard.'],
      ['question' => 'What is the return policy?', 'answer' => 'We offer free returns within 7 days of delivery. Initiate the process from the order history page or contact support.'],
      ['question' => 'Can I change my delivery address?', 'answer' => 'Yes, reach out within 6 hours of placing the order and our team will update the address before dispatch.']
    ];
    foreach ($faqs as $index => $faq):
      $collapseId = 'faq' . $index;
    ?>
      <div class="accordion-item">
        <h2 class="accordion-header" id="heading<?php echo $index; ?>">
          <button class="accordion-button <?php echo $index === 0 ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $collapseId; ?>">
            <?php echo htmlspecialchars($faq['question']); ?>
          </button>
        </h2>
        <div id="<?php echo $collapseId; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" data-bs-parent="#faqAccordion">
          <div class="accordion-body text-muted">
            <?php echo htmlspecialchars($faq['answer']); ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="mt-5">
  <div class="section-heading">
    <h2 class="h4 mb-0">Need something specific?</h2>
    <a href="contact.php" class="btn btn-primary">Open a ticket</a>
  </div>
  <div class="row g-4">
    <div class="col-md-6">
      <div class="info-card">
        <h5>Product consultations</h5>
        <p class="text-muted mb-0">Book a 15-minute session with our stylists or gear specialists to ensure the item is right for you.</p>
      </div>
    </div>
    <div class="col-md-6">
      <div class="info-card">
        <h5>Bulk & B2B orders</h5>
        <p class="text-muted mb-0">Need wholesale pricing or corporate gifting solutions? Our team curates bundles that ship nationwide.</p>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

