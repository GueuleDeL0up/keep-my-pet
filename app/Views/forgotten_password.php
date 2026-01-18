<?php
// Define the base
$base_url = "/keep-my-pet/";  // For HTML links
$base_dir = __DIR__ . "/../../";  // For PHP includes
?>
<!DOCTYPE html
  <html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>KeepMyPet - forgotten_password</title>
  <link rel="icon" type="image/png" href="<?php echo $base_url; ?>/public/assets/images/KeepMyPet_Logo.png" />
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/forgotten_password.css">
</head>

<body>

  <div class=container>
    <!-- Partie logo -->
    <div class="logo-containerr">
      <img src="../../public/assets/images/KeepMyPet_Logo.png" alt="Logo KeepMyPet" class="logo">
    </div>

    <div class="reset-container">
      <?php include $base_dir . '/app/Views/Components/forgotten_password.php'; ?>
    </div>
  </div>

  <!-- Formes du fond -->
  <div class="shapes-container">
    <!-- Ronds -->
    <div class="shape circle" id="c1"></div>
    <div class="shape circle" id="c2"></div>
    <div class="shape circle" id="c3"></div>
    <div class="shape circle" id="c4"></div>
    <div class="shape circle" id="c5"></div>
    <div class="shape circle" id="c6"></div>
    <div class="shape circle" id="c7"></div>
    <div class="shape circle" id="c8"></div>

    <!-- Triangles -->
    <div class="shape triangle" id="t1"></div>
    <div class="shape triangle" id="t2"></div>
    <div class="shape triangle" id="t3"></div>
    <div class="shape triangle" id="t4"></div>
    <div class="shape triangle" id="t5"></div>
    <div class="shape triangle" id="t6"></div>
    <div class="shape triangle" id="t7"></div>
    <div class="shape triangle" id="t8"></div>
    <div class="shape triangle" id="t9"></div>
    <div class="shape triangle" id="t10"></div>
  </div>

</body>

</html>
<div class="footer-container">
  <?php
  // FOOTER
  include $base_dir . '/app/Views/Components/footer.php';
  ?>
</div>