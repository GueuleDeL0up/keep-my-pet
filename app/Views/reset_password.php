<?php
// Define the base
$base_url = "/keep-my-pet/";
$base_dir = __DIR__ . "/../../";

session_start();

// Load language system
require_once $base_dir . 'app/Config/language.php';
$current_lang = getCurrentLanguage();

// Charger les dépendances
require $base_dir . 'app/Models/connection_db.php';
require $base_dir . 'app/Models/requests.users.php';

// Récupérer le token
$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$errors = [];
$success = false;
$user = null;

if (empty($token)) {
  $errors[] = t('error_invalid_token');
} else {
  // Valider le token
  try {
    $stmt = $db->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
      $errors[] = t('error_token_expired');
    }
  } catch (Exception $e) {
    $errors[] = t('error_server');
  }
}

// Traiter la soumission du nouveau mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($token) && $user) {
  $new_password = $_POST['new_password'] ?? '';
  $confirm_password = $_POST['confirm_password'] ?? '';

  // Validations
  if (empty($new_password) || empty($confirm_password)) {
    $errors[] = t('error_all_fields_required');
  } elseif (strlen($new_password) < 6) {
    $errors[] = t('error_password_min_length');
  } elseif ($new_password !== $confirm_password) {
    $errors[] = t('error_passwords_mismatch');
  } else {
    try {
      // Changer le mot de passe
      $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
      $stmt = $db->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
      $stmt->execute([$hashed_password, $user['id']]);

      $success = true;

      // Redirect to login after 3 seconds
      header('refresh:3; url=' . $base_url . 'app/Views/log_in.php');
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
  <title>KeepMyPet - <?php echo t('reset_password_title'); ?></title>
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
      <h1><?php echo t('reset_password_title'); ?></h1>
      <p class="subtitle"><?php echo t('reset_password_subtitle'); ?></p>

      <?php if ($success): ?>
        <div class="success-box" style="background-color: #f0fdf4; border: 2px solid #86efac; border-radius: 10px; padding: 15px; margin-bottom: 25px;">
          <p style="color: #15803d; font-size: 14px;"><?php echo t('reset_password_success'); ?></p>
        </div>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <div class="errors-box">
          <?php foreach ($errors as $err): ?>
            <p class="error-msg"><?php echo htmlspecialchars($err); ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if (!$success && $user): ?>
        <form method="POST" action="" class="login-form">
          <div class="form-group">
            <label for="new_password"><?php echo t('new_password'); ?></label>
            <input
              type="password"
              id="new_password"
              name="new_password"
              placeholder="<?php echo t('password_min_placeholder'); ?>"
              required>
          </div>

          <div class="form-group">
            <label for="confirm_password"><?php echo t('password_confirm'); ?></label>
            <input
              type="password"
              id="confirm_password"
              name="confirm_password"
              placeholder="<?php echo t('password_confirm_placeholder'); ?>"
              required>
          </div>

          <button type="submit" class="btn-login"><?php echo t('reset_password_button'); ?></button>
        </form>
      <?php endif; ?>

      <?php if (!$user && !empty($token)): ?>
        <div class="form-links">
          <p>
            <a href="<?php echo $base_url; ?>app/Views/forgotten_password.php"><?php echo t('request_new_link'); ?></a>
          </p>
        </div>
      <?php endif; ?>
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