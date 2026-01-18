<?php
// Define the base
$base_url = "/keep-my-pet/";  // For HTML links
$base_dir = __DIR__ . "/../../../";  // For PHP includes
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/Components/log_in.css">
</head>

<body>

  <div class="right">
    <label>IDENTIFIANT</label>
    <input type="text" class="input">

    <label>MOT DE PASSE</label>
    <input type="password" class="input">

    <a href="<?php echo $base_url; ?>/app/Views/home.php"><button class="btn">Se connecter</button></a>

    <div class="links">
      <a href="<?php echo $base_url; ?>/app/Views/forgotten_password.php">Mot de passe oublié</a> -
      <a href="<?php echo $base_url; ?>/app/Views/sign_up.php">S'inscrire</a>
    </div>
  </div>
</body>

</html>