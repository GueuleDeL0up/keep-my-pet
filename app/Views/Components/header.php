<?php
// Define the base
$base_url = "/keep-my-pet/";  // For HTML links
$base_dir = __DIR__ . "/../../../";  // For PHP includes
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/Components/header.css">
  <script type="text/javascript" src="<?php echo $base_url; ?>/public/assets/js/Components/header.js"></script>
</head>

<body>
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
        <a href="<?php echo $base_url; ?>/app/Views/log_in.php">Connexion</a>
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

</body>

</html>