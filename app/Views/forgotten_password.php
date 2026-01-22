<?php
// Define the base
$base_url = "/keep-my-pet/";
$base_dir = __DIR__ . "/../../";

session_start();

// Load language system
require_once $base_dir . 'app/Config/language.php';
$current_lang = getCurrentLanguage();

$errors = [];
$success = false;

// Handle password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');

  if (empty($email)) {
    $errors[] = t('error_email_required');
  } else {
    try {
      // Connect to database
      include $base_dir . 'app/Models/connection_db.php';
      include $base_dir . 'app/Models/requests.users.php';

      // Find user by email
      $users = trouveParEmail($db, $email);

      if (empty($users)) {
        $errors[] = t('error_email_not_found');
      } else {
        $user = $users[0];

        // Generate password reset token
        $token = bin2hex(random_bytes(32));

        // Store token in database - use MySQL NOW() + INTERVAL for expiry
        try {
          $stmt = $db->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = ?");
          $stmt->execute([$token, $user['id']]);

          // Create reset link - use $_SERVER to get the proper host and protocol
          $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
          $host = $_SERVER['HTTP_HOST'];
          $reset_link = $protocol . $host . "/keep-my-pet/app/Views/reset_password.php?token=" . $token;

          // Email body
          $email_body = "
            <html>
            <body style='font-family: Arial, sans-serif; padding: 20px;'>
              <h2>Réinitialisation de mot de passe - KeepMyPet</h2>
              <p>Bonjour " . htmlspecialchars($user['first_name']) . ",</p>
              <p>Vous avez demandé une réinitialisation de votre mot de passe.</p>
              <p>Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe :</p>
              <p><a href='" . $reset_link . "' style='background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Réinitialiser mon mot de passe</a></p>
              <p>Ou copiez ce lien dans votre navigateur :</p>
              <p>" . $reset_link . "</p>
              <p>Ce lien expirera dans 1 heure.</p>
              <p>Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.</p>
              <hr>
              <p style='color: #666; font-size: 12px;'>KeepMyPet - Gardez vos animaux en sécurité</p>
            </body>
            </html>
          ";

          // Store email in debug_emails table
          $stmt = $db->prepare("INSERT INTO debug_emails (to_email, subject, body, token, created_at) VALUES (?, ?, ?, ?, NOW())");
          $stmt->execute([
            $user['email'],
            'Réinitialisation de votre mot de passe - KeepMyPet',
            $email_body,
            $token
          ]);

          $success = true;

          // Redirect to debug emails page after 2 seconds
          header('refresh:2; url=' . $base_url . 'app/Views/debug_emails.php');
        } catch (Exception $e) {
          $errors[] = t('error_server');
        }
      }
    } catch (Exception $e) {
      $errors[] = t('error_server');
    }
  }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KeepMyPet - <?php echo t('forgot_password_title'); ?></title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/log_in.css">
  <link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/Components/log_in.css">
</head>

<body>
  <div class="login-page">

    <!-- Logo -->
    <div class="logo-container">
      <img src="<?php echo $base_url; ?>public/assets/images/KeepMyPet_Logo.png" alt="Logo KeepMyPet" class="logo">
    </div>

    <!-- Form Container -->
    <div class="form-container">
      <h1><?php echo t('forgot_password_title'); ?></h1>
      <p class="subtitle"><?php echo t('forgot_password_subtitle'); ?></p>

      <?php if ($success): ?>
        <div class="success-box" style="background-color: #f0fdf4; border: 2px solid #86efac; border-radius: 10px; padding: 15px; margin-bottom: 25px;">
          <p style="color: #15803d; font-size: 14px;"><?php echo t('forgot_password_success'); ?></p>
        </div>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <div class="errors-box">
          <?php foreach ($errors as $err): ?>
            <p class="error-msg"><?php echo htmlspecialchars($err); ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if (!$success): ?>
        <form method="POST" action="" class="login-form">
          <div class="form-group">
            <label for="email"><?php echo t('email'); ?></label>
            <input
              type="email"
              id="email"
              name="email"
              placeholder="<?php echo t('email_placeholder'); ?>"
              value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
              required>
          </div>

          <button type="submit" class="btn-login"><?php echo t('reset_password_button'); ?></button>
        </form>
      <?php endif; ?>

      <div class="form-links">
        <p>
          <?php echo t('remember_password'); ?>
          <a href="<?php echo $base_url; ?>app/Views/log_in.php"><?php echo t('login_link'); ?></a>
        </p>
      </div>
    </div>

    <!-- Background shapes -->
    <div class="shapes-container">
      <div class="shape circle" id="c1"></div>
      <div class="shape circle" id="c2"></div>
      <div class="shape circle" id="c3"></div>
      <div class="shape circle" id="c4"></div>
      <div class="shape circle" id="c5"></div>
      <div class="shape circle" id="c6"></div>
      <div class="shape circle" id="c7"></div>
      <div class="shape circle" id="c8"></div>
      <div class="shape circle" id="c9"></div>
      <div class="shape circle" id="c10"></div>
      <div class="shape triangle" id="t1"></div>
      <div class="shape triangle" id="t2"></div>
      <div class="shape triangle" id="t3"></div>
      <div class="shape square" id="s1"></div>
      <div class="shape square" id="s2"></div>
    </div>
  </div>
</body>

</html>