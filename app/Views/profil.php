<?php
// Define the base
$base_url = "/keep-my-pet/";  // For HTML links
$base_dir = __DIR__ . "/../../";  // For PHP includes
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil - Keep My Pet</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/main.css">
  <link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/profil.css">
  <link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/Components/profil.css">
</head>

<body>

  <?php
  // HEADER
  include $base_dir . "/app/Views/Components/header.php";
  ?>

  <main class="profile-page">
    <div class="profile-container">
      <?php
      // Profile component
      include $base_dir . "/app/Views/Components/profil.php";
      ?>
    </div>
  </main>

  <?php
  // FOOTER
  include $base_dir . "/app/Views/Components/footer.php";
  ?>

</body>

</html>