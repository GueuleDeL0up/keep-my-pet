<?php
// Define the base
$base_url = "/App_dev_web/";  // For HTML links
$base_dir = __DIR__ . "/../../";  // For PHP includes
?>
<!DOCTYPE html
  <html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>KeepMyPet - Login</title>
  <!-- link rel="stylesheet" href="../../public/assets/css/main.css"-->
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/forgotten_password.css">
</head>

<body>

  <div class=container>
    <!-- Partie logo -->
    <div class="logo-container">
      <img src="../../public/assets/images/KeepMyPet_Logo.png" alt="Logo KeepMyPet" class="logo">
    </div>

    <div class="login-container">
      <?php include 'Components/SignUp/sign_up.php'; ?>
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
  </div>

</body>

</html>

<?php
// FOOTER
include $base_dir . '/app/Views/footer.php';
?>