<?php
require_once __DIR__ . '/config.php';
include 'includes/header.php';

$errors = [];
$successMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;
    $profilePath = null;

    if ($name === '') {
        $errors[] = 'Please share your full name.';
    }

    if (!$email) {
        $errors[] = 'Please provide a valid email address.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($_FILES['profile_image']['name'] ?? '')) {
        $errors[] = 'Please upload a profile picture.';
    } elseif (!empty($_FILES['profile_image']['name'])) {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($_FILES['profile_image']['type'], $allowed, true)) {
            $errors[] = 'Profile picture must be a JPG, PNG, GIF, or WEBP image.';
        }

        if ($_FILES['profile_image']['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Profile picture must be smaller than 2MB.';
        }
    }

    if (empty($errors) && fetch_user_by_email($email)) {
        $errors[] = 'That email is already registered. Please log in instead.';
    }

    if (empty($errors)) {
        $filename = uniqid('profile_', true) . '.' . pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $destination = PROFILE_UPLOAD_DIR . '/' . $filename;
        if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $destination)) {
            $errors[] = 'Unable to save profile picture. Please try again.';
        } else {
            $profilePath = PROFILE_UPLOAD_BASE . $filename;
        }
    }

    if (empty($errors)) {
        try {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare('INSERT INTO signup (name, email, phone, password, profile_image, newsletter) VALUES (:name, :email, :phone, :password, :profile_image, :newsletter)');
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':phone' => $phone !== '' ? $phone : null,
                ':password' => password_hash($password, PASSWORD_BCRYPT),
                ':profile_image' => $profilePath,
                ':newsletter' => $newsletter
            ]);

            $successMessage = 'Account created successfully! You can now log in with your email and password.';
            $_POST = [];
        } catch (PDOException $e) {
            error_log('Signup failed: ' . $e->getMessage());
            $errors[] = 'Something went wrong while creating your account. Please try again.';
        }
    }
}
?>

<section class="auth-card mt-4">
  <p class="text-uppercase text-primary small fw-semibold mb-2">Create account</p>
  <h1 class="h3 mb-3">Join the MyShop community</h1>
  <p class="text-muted mb-4">Unlock personalized recommendations, price alerts, and early access to product drops.</p>

  <?php if (!empty($successMessage)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
  <?php endif; ?>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errors as $error): ?>
          <li><?php echo htmlspecialchars($error); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="POST" class="row g-3" enctype="multipart/form-data">
    <div class="col-12">
      <label class="form-label">Full name</label>
      <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
    </div>
    <div class="col-12">
      <label class="form-label">Email address</label>
      <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
    </div>
    <div class="col-12">
      <label class="form-label">Phone (optional)</label>
      <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" placeholder="+92 3XX XXX XXXX">
    </div>
    <div class="col-md-6">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required minlength="6">
    </div>
    <div class="col-md-6">
      <label class="form-label">Confirm password</label>
      <input type="password" name="confirm_password" class="form-control" required minlength="6">
    </div>
    <div class="col-12">
      <label class="form-label">Profile picture</label>
      <input type="file" name="profile_image" class="form-control" accept="image/*" required>
      <small class="text-muted">PNG, JPG, GIF, or WEBP up to 2MB.</small>
    </div>
    <div class="col-12">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="1" id="newsletterOptIn" name="newsletter" <?php echo isset($_POST['newsletter']) ? 'checked' : ''; ?>>
        <label class="form-check-label" for="newsletterOptIn">
          Keep me posted about new product drops and events.
        </label>
      </div>
    </div>
    <div class="col-12 d-grid">
      <button type="submit" class="btn btn-primary btn-lg">Create account</button>
    </div>
    <div class="col-12 text-center text-muted">
      Already have an account? <a href="login.php">Log in</a>
    </div>
  </form>
</section>

<?php include 'includes/footer.php'; ?>

