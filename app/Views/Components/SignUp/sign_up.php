<?php
// Define the base
$base_url = "/keep-my-pet/";  // For HTML links
$base_dir = __DIR__ . "/../../../../";  // For PHP includes
?>
 
<!DOCTYPE html>
<html lang="fr">
 
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Log In</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/sign_up.css">
</head>
 
<body>
  <div class="right">
    <label>MAIL</label>
    <input type="text" class="input">
 
    <div class="row">
      <div class="col">
        <label>NOM</label>
        <input type="text" class="input">
      </div>
 
      <div class="col">
        <label>PRÉNOM</label>
        <input type="text" class="input">
      </div>
    </div>
 
    <label>TÉLÉPHONE</label>
    <input type="text" class="input">
 
    <!-- Adresse -->
    <label>ADRESSE</label>
    <input type="text" class="input">
 
    <div class="row">
      <div class="col">
        <label>VILLE</label>
        <input type="text" class="input">
      </div>
 
      <div class="col">
        <label>CODE POSTAL</label>
        <input type="text" class="input">
      </div>
    </div>
    <label>MOT DE PASSE</label>
    <input type="text" class="input">
 
    <label>CONFIRMER LE MOT DE PASSE</label>
    <input type="text" class="input">
 
    <a href="#"><button class="btn">S'INSCRIRE</button></a>
 
    <div class="links">
      <a href="../../connexion.php">SE CONNECTER</a>
    </div>
  </div>
</body>
 
</html>

<?php
// FOOTER
include $base_dir . '/app/Views/footer.php';
?>

