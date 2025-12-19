<?php
// Define the base
$base_url = "/keep-my-pet/";  // For HTML links
$base_dir = __DIR__ . "/../../../";  // For PHP includes
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mot de passe oublié</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/forgotten_password.css">
</head>

<body>

  <div class="right">

    <!-- FORMULAIRE -->
    <form id="forgot-form">
      <label>Adresse mail</label>
      <input type="email" class="input" required>

      <button type="submit" class="btn">Envoyer</button>
    </form>

    <!-- MESSAGE CACHÉ -->
    <div id="success-message">
      <h2>📧 Mail envoyé</h2>
      <p>Un email de réinitialisation a été envoyé.</p>

      <div class="links">
        <a href="../../../app/Views/connexion.php">Se connecter</a>
      </div>
    </div>

  </div>

  <script>
    const form = document.getElementById("forgot-form");
    const message = document.getElementById("success-message");

    form.addEventListener("submit", function(e) {
      e.preventDefault(); // empêche le rechargement
      form.style.display = "none";
      message.style.display = "block";
    });
  </script>

</body>

</html>