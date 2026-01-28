<?php
// Define the base
$base_url = "/keep-my-pet/";  // For HTML links
$base_dir = __DIR__ . "/../../../";  // For PHP includes
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    .forgot-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .card {
      background: white;
      border-radius: 16px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      overflow: hidden;
      max-width: 450px;
      width: 100%;
      animation: slideUp 0.5s ease;
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .card-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      padding: 40px 30px;
      text-align: center;
      color: white;
    }

    .card-header h1 {
      font-size: 28px;
      margin-bottom: 8px;
      font-weight: 700;
    }

    .card-header p {
      font-size: 14px;
      opacity: 0.95;
      line-height: 1.5;
    }

    .card-body {
      padding: 40px 30px;
    }

    .form-group {
      margin-bottom: 25px;
    }

    label {
      display: block;
      font-weight: 600;
      color: #333;
      margin-bottom: 8px;
      font-size: 14px;
    }

    label i {
      margin-right: 8px;
      color: #667eea;
    }

    input[type="email"] {
      width: 100%;
      padding: 12px 15px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      font-size: 14px;
      transition: all 0.3s;
      font-family: inherit;
    }

    input[type="email"]:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    input[type="email"]::placeholder {
      color: #999;
    }

    .form-helper {
      font-size: 12px;
      color: #999;
      margin-top: 6px;
    }

    .btn-submit {
      width: 100%;
      padding: 14px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      font-size: 15px;
      cursor: pointer;
      transition: all 0.3s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
    }

    .btn-submit:active {
      transform: translateY(0);
    }

    .btn-submit:disabled {
      opacity: 0.7;
      cursor: not-allowed;
      transform: none;
    }

    .card-footer {
      padding: 0 30px 30px;
      text-align: center;
      border-top: 1px solid #f0f0f0;
      margin-top: 30px;
    }

    .card-footer p {
      font-size: 14px;
      color: #666;
      margin-bottom: 8px;
    }

    .card-footer a {
      color: #667eea;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s;
    }

    .card-footer a:hover {
      color: #764ba2;
    }

    /* Message de succès */
    .success-box {
      text-align: center;
      display: none;
      animation: slideUp 0.5s ease;
    }

    .success-icon {
      width: 60px;
      height: 60px;
      margin: 0 auto 20px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 32px;
      color: white;
      animation: scaleIn 0.5s ease;
    }

    @keyframes scaleIn {
      from {
        transform: scale(0);
      }

      to {
        transform: scale(1);
      }
    }

    .success-box h2 {
      color: #333;
      font-size: 24px;
      margin-bottom: 12px;
      font-weight: 700;
    }

    .success-box p {
      color: #666;
      font-size: 14px;
      line-height: 1.6;
      margin-bottom: 12px;
    }

    .success-box .subtitle {
      color: #999;
      font-size: 13px;
    }

    .btn-back {
      display: inline-block;
      margin-top: 20px;
      padding: 12px 30px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 600;
      transition: all 0.3s;
    }

    .btn-back:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
    }

    .info-banner {
      background: #f0f7ff;
      border-left: 4px solid #667eea;
      padding: 12px 15px;
      border-radius: 6px;
      margin-bottom: 20px;
      font-size: 13px;
      color: #333;
      line-height: 1.5;
    }

    .info-banner strong {
      color: #667eea;
    }
  </style>
</head>

<body>

  <div class="forgot-container">
    <div class="card">
      <div class="card-header">
        <h1>🔐 Réinitialiser le mot de passe</h1>
        <p>Entrez votre adresse email pour recevoir un lien de réinitialisation</p>
      </div>

      <div class="card-body">
        <!-- INFO BANNER -->
        <div class="info-banner">
          <strong>💡 Conseil:</strong> Vérifiez votre dossier spam après l'envoi
        </div>

        <!-- FORMULAIRE -->
        <form id="forgot-form">
          <div class="form-group">
            <label for="email">
              <i class="fas fa-envelope"></i>
              Adresse email
            </label>
            <input
              type="email"
              id="email"
              name="email"
              placeholder="exemple@email.com"
              required
              autofocus>
            <div class="form-helper">
              Nous vous enverrons un lien de réinitialisation
            </div>
          </div>

          <button type="submit" class="btn-submit">
            <i class="fas fa-paper-plane"></i>
            Envoyer le lien
          </button>
        </form>

        <!-- MESSAGE DE SUCCÈS -->
        <div id="success-message" class="success-box">
          <div class="success-icon">
            <i class="fas fa-check-circle"></i>
          </div>
          <h2>Email envoyé! 📧</h2>
          <p>Si cette adresse email existe dans notre base, un lien de réinitialisation a été envoyé.</p>
          <p class="subtitle">Vérifiez votre boîte email (et le dossier spam) pour continuer.</p>
          <a href="<?php echo $base_url; ?>/app/Views/log_in.php" class="btn-back">
            <i class="fas fa-sign-in-alt"></i> Retour à la connexion
          </a>
        </div>

        <!-- PIED DE PAGE -->
        <div class="card-footer" id="form-footer">
          <p>Vous vous souvenez de votre mot de passe?</p>
          <a href="<?php echo $base_url; ?>/app/Views/log_in.php">Se connecter</a>
        </div>
      </div>
    </div>
  </div>

  <script>
    const form = document.getElementById("forgot-form");
    const successMessage = document.getElementById("success-message");
    const formFooter = document.getElementById("form-footer");

    form.addEventListener("submit", function(e) {
      e.preventDefault();

      const email = document.getElementById("email").value;
      const submitBtn = form.querySelector("button[type='submit']");
      const originalText = submitBtn.innerHTML;

      // Afficher le chargement
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';

      // Envoyer via fetch
      fetch('<?php echo $base_url; ?>app/Views/request_password_reset.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'email=' + encodeURIComponent(email)
        })
        .then(response => response.json())
        .then(data => {
          // Toujours afficher le message de succès (pour des raisons de sécurité)
          form.style.display = "none";
          formFooter.style.display = "none";
          successMessage.style.display = "block";
        })
        .catch(error => {
          console.error('Erreur:', error);
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalText;
          alert('Une erreur est survenue. Veuillez réessayer.');
        });
    });
  </script>

</body>

</html>