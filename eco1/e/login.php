<?php
require_once __DIR__ . '/config.php';
include 'includes/header.php';

$errors = [];
$successMessage = null;
$redirectTo = $_POST['redirect'] ?? $_GET['redirect'] ?? 'index.php';

function regenerate_login_captcha(): string {
    $first = random_int(1, 9);
    $second = random_int(1, 9);
    $_SESSION['login_captcha_answer'] = $first + $second;
    return "{$first} + {$second} = ?";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password'] ?? '');
    $captchaAnswer = trim($_POST['captcha_answer'] ?? '');
    $notRobot = isset($_POST['not_robot']);

    if (!$email) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }

    if (!$notRobot) {
        $errors[] = 'Please confirm that you are not a robot.';
    }

    $expectedCaptcha = $_SESSION['login_captcha_answer'] ?? null;
    if ($expectedCaptcha === null || (int)$captchaAnswer !== (int)$expectedCaptcha) {
        $errors[] = 'Captcha answer is incorrect.';
    }

    if (empty($errors)) {
        $user = fetch_user_by_email($email);
        if (!$user || !password_verify($password, $user['password'])) {
            $errors[] = 'Invalid email or password. Please try again.';
        } else {
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_avatar'] = $user['profile_image'] ?? null;
            $successMessage = 'Welcome back! You are now logged in.';

            if (!empty($redirectTo)) {
                header('Location: ' . $redirectTo);
                exit;
            }
        }
    }
}

$captchaQuestion = regenerate_login_captcha();
?>

<section class="auth-card mt-4">
  <p class="text-uppercase text-primary small fw-semibold mb-2">Sign in</p>
  <h1 class="h3 mb-3">Access your account</h1>
  <p class="text-muted mb-4">Track orders, manage saved items, and enjoy a faster checkout experience.</p>

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

  <form method="POST" class="row g-3" novalidate>
    <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirectTo); ?>">
    <div class="col-12">
      <label class="form-label">Email address</label>
      <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
    </div>
    <div class="col-12">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required minlength="6">
      <small class="text-muted">Minimum 6 characters. Add numbers & symbols for stronger security.</small>
    </div>
    <div class="col-12">
      <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" value="1" id="notRobot" name="not_robot" <?php echo isset($_POST['not_robot']) ? 'checked' : ''; ?> required>
        <label class="form-check-label" for="notRobot">I am not a robot</label>
      </div>
      <label class="form-label">Captcha: <?php echo htmlspecialchars($captchaQuestion); ?></label>
      <input type="number" name="captcha_answer" class="form-control" required>
      <small class="text-muted">Solve the equation to continue.</small>
    </div>
    <div class="col-12 d-flex justify-content-between align-items-center">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="1" id="rememberMe" name="remember">
        <label class="form-check-label" for="rememberMe">Remember me</label>
      </div>
      <a href="support.php" class="small">Forgot password?</a>
    </div>
    <div class="col-12 d-grid">
      <button type="submit" class="btn btn-primary btn-lg">Continue</button>
    </div>
    <div class="col-12 text-center text-muted">
      New here? <a href="signup.php">Create an account</a>
    </div>
  </form>
</section>

<?php include 'includes/footer.php'; ?>

