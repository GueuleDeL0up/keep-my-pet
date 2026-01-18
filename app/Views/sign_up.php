<?php
// Define the base
$base_url = "/keep-my-pet/";  // For HTML links
$base_dir = __DIR__ . "/../../";  // For PHP includes
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>KeepMyPet - Sign Up</title>
  <link rel="icon" type="image/png" href="<?php echo $base_url; ?>/public/assets/images/KeepMyPet_Logo.png" />
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/sign_up.css">
  <script src="<?php echo $base_url; ?>/public/assets/js/Components/signup.js" defer></script>
</head>

<body>

  <div class=container>
    <!-- Partie logo -->
    <div class="logo-containerr">
      <img src="../../public/assets/images/KeepMyPet_Logo.png" alt="Logo KeepMyPet" class="logo">
    </div>

    <div class="signup-container">
      <?php include 'Components/sign_up.php'; ?>
    </div>
  </div>

  <!-- Formes du fond -->
  <div class="shapes-container">
    <!-- Ronds -->
    <div class="shape circle" id="c1"></div>
    <div class="shape circle" id="c2"></div>
    <div class="shape circle" id="c3"></div>
    <div class="shape circle" id="c4"></div>
    <div class="shape circlee" id="c5"></div>
    <div class="shape circlee" id="c6"></div>
    <div class="shape circlee" id="c7"></div>
    <div class="shape circlee" id="c8"></div>

    <!-- Triangles -->
    <div class="shape triangle" id="t1"></div>
    <div class="shape triangle" id="t2"></div>
    <div class="shape triangle" id="t3"></div>
    <div class="shape triangle" id="t4"></div>
    <div class="shape trianglee" id="t5"></div>
    <div class="shape trianglee" id="t6"></div>
    <div class="shape trianglee" id="t7"></div>
  </div>
</body>

</html>
<div class="footer-container">
  <?php /* FOOTER */ include $base_dir . '/app/Views/Components/footer.php'; ?>
</div>