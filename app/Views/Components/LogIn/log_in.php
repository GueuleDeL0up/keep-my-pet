<?php
// Define the base
$base_url = "/App_dev_web/";  // For HTML links
$base_dir = __DIR__ . "/../../../../";  // For PHP includes
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Log In</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/log_in.css">
</head>

<body>

  <div class="right">
    <label>IDENTIFIANT</label>
    <input type="text" class="input">

    <label>MOT DE PASSE</label>
    <input type="password" class="input">


    <a href="../../Utilisateurs/accueil.php"><button class="btn">Se connecter</button></a>


    <div class="links">
      <a href="#">Mot de passe oublié</a> –
      <a href="../../inscription.php">S’inscrire</a>
    </div>
  </div>
</body>

</html>