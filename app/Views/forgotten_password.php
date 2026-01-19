<?php
session_start();

// Define the base
$base_url = "/keep-my-pet/";  // For HTML links
$base_dir = __DIR__ . "/../../";  // For PHP includes
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KeepMyPet - Réinitialiser le mot de passe</title>
</head>

<body style="margin: 0; padding: 0;">
  <?php include $base_dir . '/app/Views/Components/forgotten_password.php'; ?>
</body>

</html>

<?php
// FOOTER
include $base_dir . '/app/Views/Components/footer.php';
?>