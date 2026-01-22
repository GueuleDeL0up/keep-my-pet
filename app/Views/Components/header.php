<?php
// Define the base
$base_url = "/keep-my-pet/";  // For HTML links
$base_dir = __DIR__ . "/../../../";  // For PHP includes

// Ensure session is started so we can check authentication
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Language is already loaded by parent view, don't reload
// Just ensure it's available
if (!function_exists('t')) {
  require_once $base_dir . '/app/Config/language.php';
}

// Determine auth link text and destination
if (!empty($_SESSION['user_id'])) {
  $auth_text = 'Mon compte';
  $auth_link = $base_url . '/app/Views/profile.php';
  $logout_link = $base_url . '/app/Views/logout.php';
} else {
  $auth_text = 'Se connecter';
  $auth_link = $base_url . '/app/Views/log_in.php';
  $logout_link = null;
}

// Active tab helper - improved detection
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$current_file = basename($request_uri);

// Check if empty or just the base directory
$is_home = empty($current_file) || $request_uri === '/keep-my-pet/' || $request_uri === '/keep-my-pet' || $current_file === 'home.php' || $current_file === 'index.php';
$is_ads = stripos($request_uri, 'advertisements') !== false || $current_file === 'advertisements.php';
$is_contact = stripos($request_uri, 'contact') !== false || $current_file === 'contact.php';
$is_profile = stripos($request_uri, 'profile') !== false || $current_file === 'profile.php' || stripos($request_uri, 'profile_settings') !== false;
?>

<link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/Components/header.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/global-preferences.css">
<script type="text/javascript" src="<?php echo $base_url; ?>/public/assets/js/theme.js"></script>
<script type="text/javascript" src="<?php echo $base_url; ?>/public/assets/js/Components/header.js"></script>

<header class="km-header">
  <div class="logo-container">
    <img src="<?php echo $base_url; ?>/public/assets/images/KeepMyPet_Logo.png" alt="Logo KeepMyPet">
  </div>

  <nav class="nav-bar">
    <button class="button-menu" type="button" aria-label="<?php echo t('open_menu'); ?>" onclick="myFunction()">≡</button>
    <div id="myLinks" class="nav-links">
      <a class="nav-pill <?php echo $is_home ? 'active' : ''; ?>" href="<?php echo $base_url; ?>/app/Views/home.php"><?php echo t('home'); ?></a>
      <a class="nav-pill <?php echo $is_ads ? 'active' : ''; ?>" href="<?php echo $base_url; ?>/app/Views/advertisements.php"><?php echo t('ads'); ?></a>
      <a class="nav-pill <?php echo $is_contact ? 'active' : ''; ?>" href="<?php echo $base_url; ?>/app/Views/contact.php"><?php echo t('contact'); ?></a>
      <?php if (!empty($_SESSION['user_id'])) : ?>
        <a class="nav-pill auth-pill <?php echo $is_profile ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($auth_link); ?>"><?php echo t('my_account'); ?></a>
        <a class="nav-pill" href="<?php echo htmlspecialchars($logout_link); ?>"><?php echo t('logout'); ?></a>
      <?php else: ?>
        <a class="nav-pill auth-pill" href="<?php echo htmlspecialchars($auth_link); ?>"><?php echo t('login'); ?></a>
      <?php endif; ?>
    </div>
  </nav>

  <form method="POST" action="<?php echo $base_url; ?>app/API/changeLanguage.php" style="display: inline;">
    <div class="lang-selector">
      <select id="lang-select" name="lang" onchange="this.form.submit();">
        <option value="fr" <?php echo $current_language === 'fr' ? 'selected' : ''; ?>>🇫🇷 FR</option>
        <option value="en" <?php echo $current_language === 'en' ? 'selected' : ''; ?>>🇬🇧 EN</option>
        <option value="es" <?php echo $current_language === 'es' ? 'selected' : ''; ?>>🇪🇸 ES</option>
      </select>
    </div>
  </form>
</header>