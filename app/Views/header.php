<?php
// Define the base
$base_url = "/App_dev_web/";  // For HTML links
$base_dir = __DIR__ . "/../../";  // For PHP includes
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>KeepMyPet - Header</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/header.css">
</head>

<body>
  <header>
    <div class="logo-container">
      <img src="<?php echo $base_url; ?>/public/assets/images/KeepMyPet_Logo.png" alt="Logo KeepMyPet">
    </div>

    <nav class="nav-bar">
      <a href="<?php echo $base_url; ?>/app/Views/Visiteurs/home.php">Accueil</a>
      <p>|</p>
      <a href="#">Annonces</a>
      <p>|</p>
      <a href="#">Contact</a>
      <p>|</p>
      <a href="<?php echo $base_url; ?>/app/Views/log_in.php">Connexion</a>
    </nav>

    <div class="lang">
      <img src="<?php echo $base_url; ?>/public/assets/images/flags/french.png" alt="Drapeau" class="flag">
      <p>FR</p>
    </div>
  </header>

</body>

</html>

<style>
  /* Debugging Borders */
  * {
    border: 1px solid red;
  }
</style>