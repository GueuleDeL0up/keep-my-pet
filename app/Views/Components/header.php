<?php
// Define the base
$base_url = "/keep-my-pet/";  // For HTML links
$base_dir = __DIR__ . "/../../../";  // For PHP includes

// Ensure session is started so we can check authentication
if (session_status() === PHP_SESSION_NONE) {
  session_start();
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
?>

<link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/Components/header.css">
<script type="text/javascript" src="<?php echo $base_url; ?>/public/assets/js/Components/header.js"></script>

<header>
  <div class="logo-container">
    <img src="<?php echo $base_url; ?>/public/assets/images/KeepMyPet_Logo.png" alt="Logo KeepMyPet">
  </div>

  <nav class="nav-bar" onclick="myFunction()">
    <a class="button-menu">≡</a>
    <div id="myLinks">
      <a href="<?php echo $base_url; ?>/app/Views/home.php">Accueil</a>
      <p>|</p>
      <a href="<?php echo $base_url; ?>/app/Views/advertisements.php">Annonces</a>
      <p>|</p>
      <a href="<?php echo $base_url; ?>/app/Views/contact.php">Contact</a>
      <p>|</p>
      <?php if (!empty($_SESSION['user_id'])) : ?>
        <a href="<?php echo htmlspecialchars($auth_link); ?>"><?php echo htmlspecialchars($auth_text); ?></a>
        <p>|</p>
        <a href="<?php echo htmlspecialchars($logout_link); ?>">Déconnexion</a>
      <?php else: ?>
        <a href="<?php echo htmlspecialchars($auth_link); ?>"><?php echo htmlspecialchars($auth_text); ?></a>
      <?php endif; ?>
    </div>
  </nav>

  <div class="lang">
    <img src="<?php echo $base_url; ?>/public/assets/images/flags/fr.png" alt="Drapeau" class="flag" id="flag">
    <select id="lang-select" onchange="changeLanguage()">
      <option value="fr">FR</option>
      <option value="en">EN</option>
      <option value="es">ES</option>
    </select>
  </div>
</header>