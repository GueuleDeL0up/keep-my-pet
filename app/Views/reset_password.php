<?php
// Define the base
$base_url = "/keep-my-pet/";
$base_dir = __DIR__ . "/../../";

// Charger les dépendances
require $base_dir . 'app/Models/connection_db.php';
require $base_dir . 'app/Models/requests.password_reset.php';

// Récupérer le token
$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$error = null;
$user_id = null;

if (empty($token)) {
  $error = 'Lien invalide ou expiré';
} else {
  // Valider le token
  $user_id = validerTokenReinit($db, $token);
  if (!$user_id) {
    $error = 'Lien invalide ou expiré. Veuillez demander un nouveau lien de réinitialisation.';
  }
}

// Traiter la soumission du nouveau mot de passe
$success_message = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($token) && $user_id) {
  try {
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Validations
    if (empty($new_password) || empty($confirm_password)) {
      throw new Exception('Tous les champs sont obligatoires');
    }

    if (strlen($new_password) < 6) {
      throw new Exception('Le mot de passe doit contenir au moins 6 caractères');
    }

    if ($new_password !== $confirm_password) {
      throw new Exception('Les mots de passe ne correspondent pas');
    }

    // Changer le mot de passe
    if (changerMotDePasse($db, $user_id, $new_password)) {
      // Supprimer le token utilisé
      supprimerTokenReinit($db, $token);
      $success_message = 'Mot de passe réinitialisé avec succès! Redirection vers la connexion...';
    } else {
      throw new Exception('Erreur lors de la mise à jour du mot de passe');
    }
  } catch (Exception $e) {
    $error = $e->getMessage();
  }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Réinitialiser le mot de passe - KeepMyPet</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/reset_password_modern.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
  <div class="reset-container">
    <div class="reset-card">
      <!-- Header -->
      <div class="reset-header">
        <div class="logo">
          <img src="<?php echo $base_url; ?>/public/assets/images/KeepMyPet_Logo.png" alt="KeepMyPet Logo">
        </div>
        <h1>Réinitialiser le mot de passe</h1>
        <p>Créez un nouveau mot de passe pour accéder à votre compte</p>
      </div>

      <!-- Contenu -->
      <div class="reset-body">
        <?php if ($error): ?>
          <!-- Erreur -->
          <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <div>
              <strong>Lien invalide</strong>
              <p><?php echo htmlspecialchars($error); ?></p>
              <a href="<?php echo $base_url; ?>app/Views/forgotten_password.php" class="link">Demander un nouveau lien</a>
            </div>
          </div>
        <?php elseif ($success_message): ?>
          <!-- Succès -->
          <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <div>
              <strong>Succès!</strong>
              <p><?php echo htmlspecialchars($success_message); ?></p>
            </div>
          </div>
          <script>
            setTimeout(() => {
              window.location.href = '<?php echo $base_url; ?>app/Views/log_in.php';
            }, 3000);
          </script>
        <?php else: ?>
          <!-- Formulaire -->
          <form method="POST" class="reset-form">
            <div class="form-group">
              <label for="new_password">
                <i class="fas fa-lock"></i>
                Nouveau mot de passe
              </label>
              <div class="password-input">
                <input type="password" id="new_password" name="new_password"
                  placeholder="Entrez un nouveau mot de passe" required>
                <button type="button" class="toggle-password" onclick="togglePassword('new_password', this)">
                  <i class="fas fa-eye"></i>
                </button>
              </div>
              <div class="password-strength" id="strength-meter"></div>
              <small class="password-hint">Min. 6 caractères</small>
            </div>

            <div class="form-group">
              <label for="confirm_password">
                <i class="fas fa-lock"></i>
                Confirmer le mot de passe
              </label>
              <div class="password-input">
                <input type="password" id="confirm_password" name="confirm_password"
                  placeholder="Confirmez votre mot de passe" required>
                <button type="button" class="toggle-password" onclick="togglePassword('confirm_password', this)">
                  <i class="fas fa-eye"></i>
                </button>
              </div>
            </div>

            <button type="submit" class="btn-submit">
              <i class="fas fa-check"></i>
              Réinitialiser le mot de passe
            </button>

            <div class="form-footer">
              <p>Vous vous souvenez de votre mot de passe?</p>
              <a href="<?php echo $base_url; ?>app/Views/log_in.php">Se connecter</a>
            </div>
          </form>
        <?php endif; ?>
      </div>
    </div>

    <!-- Illustration -->
    <div class="reset-illustration">
      <div class="illustration-content">
        <i class="fas fa-lock-open"></i>
        <h2>Sécurité d'abord</h2>
        <p>Protégez votre compte KeepMyPet avec un mot de passe fort</p>
      </div>
    </div>
  </div>

  <script>
    // Toggle password visibility
    function togglePassword(inputId, button) {
      const input = document.getElementById(inputId);
      if (input.type === 'password') {
        input.type = 'text';
        button.innerHTML = '<i class="fas fa-eye-slash"></i>';
      } else {
        input.type = 'password';
        button.innerHTML = '<i class="fas fa-eye"></i>';
      }
    }

    // Password strength meter
    const passwordInput = document.getElementById('new_password');
    if (passwordInput) {
      passwordInput.addEventListener('input', function() {
        const strength = checkPasswordStrength(this.value);
        updateStrengthMeter(strength);
      });
    }

    function checkPasswordStrength(password) {
      let strength = 0;
      if (password.length >= 6) strength++;
      if (password.length >= 12) strength++;
      if (/[a-z]/.test(password)) strength++;
      if (/[A-Z]/.test(password)) strength++;
      if (/[0-9]/.test(password)) strength++;
      if (/[!@#$%^&*]/.test(password)) strength++;
      return strength;
    }

    function updateStrengthMeter(strength) {
      const meter = document.getElementById('strength-meter');
      const texts = ['', 'Faible', 'Faible', 'Moyen', 'Bon', 'Fort', 'Très fort'];
      const colors = ['', '#ef4444', '#ef4444', '#f59e0b', '#10b981', '#10b981', '#10b981'];

      meter.style.width = (strength * 16.67) + '%';
      meter.style.backgroundColor = colors[strength];
      meter.textContent = texts[strength];
    }

    // Validation on form submit
    document.querySelector('.reset-form')?.addEventListener('submit', function(e) {
      const password = document.getElementById('new_password').value;
      const confirm = document.getElementById('confirm_password').value;

      if (password !== confirm) {
        e.preventDefault();
        alert('Les mots de passe ne correspondent pas');
        return false;
      }

      if (password.length < 6) {
        e.preventDefault();
        alert('Le mot de passe doit contenir au moins 6 caractères');
        return false;
      }
    });
  </script>
</body>

</html>