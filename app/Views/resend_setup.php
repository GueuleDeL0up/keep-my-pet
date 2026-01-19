<?php
session_start();

// Récupérer les infos existantes
$api_key = $_SESSION['resend_api_key'] ?? '';
$saved_message = '';

// Traiter le formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $api_key = trim($_POST['resend_api_key'] ?? '');

  if (empty($api_key)) {
    $saved_message = '<div class="alert alert-warning">Veuillez entrer votre clé API Resend</div>';
  } else {
    // Sauvegarder en session
    $_SESSION['resend_api_key'] = $api_key;
    $saved_message = '<div class="alert alert-success">✅ Configuration Resend sauvegardée!</div>';
  }
}

// Traiter l'envoi de test
$test_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'test') {
  $test_email = trim($_POST['test_email'] ?? '');

  if (empty($test_email)) {
    $test_message = '<div class="alert alert-warning">Veuillez entrer une adresse email</div>';
  } elseif (empty($_SESSION['resend_api_key'])) {
    $test_message = '<div class="alert alert-danger">Clé API Resend non configurée</div>';
  } else {
    // Charger et utiliser ResendMailer
    require_once __DIR__ . '/../Classes/ResendMailer.php';

    $mailer = new ResendMailer($_SESSION['resend_api_key']);
    $result = $mailer->send(
      $test_email,
      'Test Email from Resend',
      '<html><body><h1>Test Email</h1><p>Si tu vois ce message, Resend fonctionne!</p></body></html>',
      'noreply@keepmypet.com'
    );

    if ($result['success']) {
      $test_message = '<div class="alert alert-success">✅ Email de test envoyé à ' . htmlspecialchars($test_email) . '!</div>';
    } else {
      $test_message = '<div class="alert alert-danger">❌ Erreur: ' . htmlspecialchars($result['error'] ?? 'Unknown error') . '</div>';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Configuration Resend - KeepMyPet</title>
  <link rel="stylesheet" href="/keep-my-pet/public/assets/css/main.css">
  <style>
    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .container {
      max-width: 600px;
      background: white;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    }

    h1 {
      color: #333;
      margin-bottom: 10px;
      font-size: 28px;
    }

    .subtitle {
      color: #666;
      margin-bottom: 30px;
      font-size: 14px;
    }

    .form-group {
      margin-bottom: 25px;
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #333;
    }

    input[type="text"],
    input[type="email"] {
      width: 100%;
      padding: 12px 15px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      font-size: 14px;
      box-sizing: border-box;
      transition: border-color 0.3s;
    }

    input[type="text"]:focus,
    input[type="email"]:focus {
      outline: none;
      border-color: #667eea;
    }

    button {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 12px 30px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.2s;
    }

    button:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .alert {
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
    }

    .alert-success {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .alert-warning {
      background: #fff3cd;
      color: #856404;
      border: 1px solid #ffeaa7;
    }

    .alert-danger {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    .divider {
      margin: 30px 0;
      border: none;
      border-top: 2px solid #e0e0e0;
    }

    .form-group-row {
      display: flex;
      gap: 10px;
    }

    .form-group-row input {
      flex: 1;
    }

    .form-group-row button {
      flex: 0 0 auto;
      padding: 12px 20px;
    }

    .info-box {
      background: #f0f7ff;
      border-left: 4px solid #667eea;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      color: #333;
      font-size: 13px;
      line-height: 1.6;
    }

    .info-box strong {
      color: #667eea;
    }

    .success-check {
      color: #28a745;
      font-weight: bold;
    }
  </style>
</head>

<body>
  <div class="container">
    <h1>⚙️ Configuration Resend</h1>
    <p class="subtitle">Configure ta clé API Resend pour activer l'envoi d'emails</p>

    <?php if ($saved_message) echo $saved_message; ?>
    <?php if ($test_message) echo $test_message; ?>

    <div class="info-box">
      <strong>Comment obtenir ta clé API Resend?</strong><br>
      1. Va sur <a href="https://resend.com" target="_blank">https://resend.com</a><br>
      2. Crée un compte (2 minutes)<br>
      3. Va dans Settings → API Keys<br>
      4. Clique "Create API Key"<br>
      5. Copie ta clé (commence par <code>re_</code>)<br>
      6. Colle-la ci-dessous
    </div>

    <form method="POST">
      <div class="form-group">
        <label for="resend_api_key">Clé API Resend:</label>
        <input type="text" id="resend_api_key" name="resend_api_key" placeholder="re_..." value="<?php echo htmlspecialchars($api_key); ?>" required>
        <small style="color: #999; margin-top: 5px; display: block;">Ta clé API Resend commence par "re_"</small>
      </div>
      <button type="submit">💾 Sauvegarder</button>
    </form>

    <hr class="divider">

    <h3 style="margin-top: 0; color: #333;">Tester l'envoi d'email</h3>
    <p style="color: #666; margin-bottom: 20px;">Envoie un email de test pour vérifier que tout fonctionne:</p>

    <form method="POST">
      <div class="form-group-row">
        <input type="email" name="test_email" placeholder="exemple@test.com" required>
        <button type="submit" name="action" value="test">📧 Envoyer test</button>
      </div>
    </form>

    <?php if ($_SESSION['resend_api_key'] ?? false): ?>
      <div style="margin-top: 30px; padding: 15px; background: #f0f7ff; border-radius: 8px; color: #333;">
        <span class="success-check">✅ Clé API configurée</span>
        <p style="margin: 10px 0 0; color: #666; font-size: 13px;">La réinitialisation de mot de passe enverra les emails via Resend.</p>
      </div>
    <?php endif; ?>

    <hr class="divider">

    <div style="text-align: center;">
      <a href="/keep-my-pet/" style="color: #667eea; text-decoration: none; font-weight: 600;">← Retour à l'accueil</a>
    </div>
  </div>
</body>

</html>