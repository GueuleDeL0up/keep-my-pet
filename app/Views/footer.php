<?php
// Define the base
$base_url = "/App_dev_web/";  // For HTML links
$base_dir = __DIR__ . "/../../";  // For PHP includes
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>KeepMyPet - Footer</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/footer.css">
</head>

<body>

  <footer>
    <div class="logo-container">
      <img src="<?php echo $base_url; ?>/public/assets/images/G2Cidees_Logo.png" alt="G2Cidées Logo">
    </div>

    <div class="keepmypet-container">
      <h3>KeepMyPet</h3>
      <p>A propos de KeepMyPet</p>
      <p>Contact</p>
    </div>

    <div class="g2cidees-container">
      <h3>G2Cidées</h3>
      <p>A propos de G2Cidées</p>
      <p>Contact</p>
    </div>

    <div class="copyright-container">
      <h3>Copyright</h3>
      <p>©G2Cidées 2025</p>
    </div>
  </footer>
</body>

</html>