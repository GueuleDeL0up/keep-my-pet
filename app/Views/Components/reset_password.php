<?php
// Define the base
$base_url = "/App_dev_web/";  // For HTML links
$base_dir = __DIR__ . "/../../../";  // For PHP includes
?> 

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nouveau mot de passe</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/reset_password.css">
</head>

<body>

  <div class="right">

    <!-- FORMULAIRE -->
    <form id="reset-form">
      <h2>Nouveau mot de passe</h2>

      <label for="password">Nouveau mot de passe</label>
      <input
        type="password"
        id="password"
        class="input"
        required
      >

      <label for="confirm">Confirmer le mot de passe</label>
      <input
        type="password"
        id="confirm"
        class="input"
        required
      >

      <button type="submit" class="btn">Valider</button>
    </form>

    <!-- MESSAGE DE SUCCÈS (caché) -->
    <div id="success-message">
      <h2>✅ Mot de passe modifié</h2>
      <p>Votre mot de passe a été mis à jour avec succès.</p>

      <div class="links">
        <a href="connexion.php">Se connecter</a>
      </div>
    </div>

  </div>

  <script>
    const form = document.getElementById("reset-form");
    const success = document.getElementById("success-message");

    form.addEventListener("submit", function(e) {
      e.preventDefault();

      const pwd = document.getElementById("password").value;
      const confirm = document.getElementById("confirm").value;

      if (pwd !== confirm) {
        alert("Les mots de passe ne correspondent pas");
        return;
      }

      form.style.display = "none";
      success.style.display = "block";
    });
  </script>

</body>
</html>

<?php
// FOOTER
include $base_dir . '/app/Views/footer.php';
?>