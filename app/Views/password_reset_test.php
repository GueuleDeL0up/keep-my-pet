<?php
session_start();
require_once __DIR__ . '/../Models/connection_db.php';
require_once __DIR__ . '/../Models/requests.password_reset.php';

$base_url = "/keep-my-pet/";
$test_results = [];

// Vérifier les infos de session
$resend_configured = isset($_SESSION['resend_api_key']) && !empty($_SESSION['resend_api_key']);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Test Réinitialisation Mot de Passe</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      padding: 20px;
    }

    .container {
      max-width: 1000px;
      margin: 0 auto;
    }

    .header {
      text-align: center;
      color: white;
      margin-bottom: 40px;
    }

    .header h1 {
      font-size: 32px;
      margin-bottom: 10px;
    }

    .header p {
      font-size: 16px;
      opacity: 0.9;
    }

    .grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 20px;
    }

    @media (max-width: 768px) {
      .grid {
        grid-template-columns: 1fr;
      }
    }

    .card {
      background: white;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    .card h2 {
      color: #333;
      margin-bottom: 15px;
      font-size: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .card h2 .icon {
      font-size: 24px;
    }

    .card p {
      color: #666;
      margin-bottom: 15px;
      font-size: 14px;
      line-height: 1.6;
    }

    .form-group {
      margin-bottom: 15px;
    }

    label {
      display: block;
      margin-bottom: 5px;
      font-weight: 600;
      color: #333;
      font-size: 13px;
    }

    input {
      width: 100%;
      padding: 10px 12px;
      border: 2px solid #e0e0e0;
      border-radius: 6px;
      font-size: 13px;
      transition: border-color 0.3s;
    }

    input:focus {
      outline: none;
      border-color: #667eea;
    }

    button {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 6px;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.2s;
      font-size: 13px;
      width: 100%;
    }

    button:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    button:active {
      transform: translateY(0);
    }

    .status {
      padding: 12px;
      border-radius: 6px;
      margin-bottom: 15px;
      font-size: 13px;
      font-weight: 600;
    }

    .status.success {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .status.error {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    .status.warning {
      background: #fff3cd;
      color: #856404;
      border: 1px solid #ffeaa7;
    }

    .info-box {
      background: #f0f7ff;
      border-left: 4px solid #667eea;
      padding: 12px;
      border-radius: 6px;
      margin-bottom: 15px;
      font-size: 13px;
      color: #333;
      line-height: 1.5;
    }

    .link-list {
      background: #f5f5f5;
      border-radius: 6px;
      padding: 15px;
      margin-bottom: 15px;
    }

    .link-list h3 {
      color: #333;
      font-size: 13px;
      margin-bottom: 10px;
      font-weight: 600;
    }

    .link-list ul {
      list-style: none;
    }

    .link-list li {
      margin-bottom: 8px;
    }

    .link-list a {
      color: #667eea;
      text-decoration: none;
      font-size: 13px;
      font-weight: 500;
      word-break: break-all;
    }

    .link-list a:hover {
      text-decoration: underline;
    }

    .divider {
      border: none;
      border-top: 2px solid #e0e0e0;
      margin: 20px 0;
    }

    .warning-box {
      background: #fff3cd;
      border: 2px solid #ffc107;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 20px;
      color: #856404;
    }

    .warning-box strong {
      display: block;
      margin-bottom: 8px;
    }

    .success-box {
      background: #d4edda;
      border: 2px solid #28a745;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 20px;
      color: #155724;
    }

    .success-box strong {
      display: block;
      margin-bottom: 8px;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="header">
      <h1>🔐 Test Réinitialisation Mot de Passe</h1>
      <p>Testez le système complet de réinitialisation en développement</p>
    </div>

    <?php if (!$resend_configured): ?>
      <div class="warning-box">
        <strong>⚠️ Resend pas configuré</strong>
        Les emails ne seront pas envoyés par Resend, mais seront sauvegardés en base et visibles dans debug_emails.php
      </div>
    <?php else: ?>
      <div class="success-box">
        <strong>✅ Resend est configuré!</strong>
        Les emails seront envoyés via Resend et aussi sauvegardés en base.
      </div>
    <?php endif; ?>

    <div class="grid">
      <!-- Étape 1: Tester la demande -->
      <div class="card">
        <h2><span class="icon">1️⃣</span> Demander Réinitialisation</h2>
        <p>Entrez une email d'utilisateur existant pour recevoir un lien de réinitialisation.</p>

        <form id="testForm" method="POST">
          <div class="form-group">
            <label for="test_email">Email de l'utilisateur:</label>
            <input type="email" id="test_email" name="test_email" placeholder="user@example.com" required>
            <small style="color: #999; display: block; margin-top: 5px;">Ex: john@example.com</small>
          </div>
          <button type="submit">📧 Demander Réinitialisation</button>
        </form>
        <div id="testResult"></div>
      </div>

      <!-- Étape 2: Voir les emails -->
      <div class="card">
        <h2><span class="icon">2️⃣</span> Voir les Emails</h2>
        <p>Consultez tous les emails de réinitialisation générés pour copier les liens de test.</p>

        <div class="info-box">
          Après avoir cliqué "Demander Réinitialisation", rendez-vous sur debug_emails.php pour voir l'email avec le lien.
        </div>

        <a href="/keep-my-pet/app/Views/debug_emails.php" style="display: inline-block; width: 100%;">
          <button type="button">🔍 Voir debug_emails.php</button>
        </a>
      </div>

      <!-- Étape 3: Tester la réinitialisation -->
      <div class="card">
        <h2><span class="icon">3️⃣</span> Tester Réinitialisation</h2>
        <p>Une fois que vous avez un token, testez la page de réinitialisation directement.</p>

        <div class="form-group">
          <label for="test_token">Token (copié depuis debug_emails.php):</label>
          <input type="text" id="test_token" name="test_token" placeholder="Collez le token ici" style="font-size: 11px;">
          <small style="color: #999; display: block; margin-top: 5px;">Trouvez le token dans l'email sur debug_emails.php</small>
        </div>
        <button onclick="testResetPage()" type="button">🔗 Ouvrir la page de réinitialisation</button>
      </div>

      <!-- Infos utiles -->
      <div class="card">
        <h2><span class="icon">📋</span> Infos Utiles</h2>
        <p><strong>Workflow complet:</strong></p>
        <ol style="margin-left: 20px; font-size: 13px; color: #666; line-height: 1.8;">
          <li>Utilisateur oublie son mot de passe</li>
          <li>Clic sur "Mot de passe oublié"</li>
          <li>Rentre son email</li>
          <li>Email reçu avec lien</li>
          <li>Clic sur le lien → page reset_password.php</li>
          <li>Entre nouveau mot de passe</li>
          <li>Nouveau mot de passe enregistré en base</li>
          <li>Peut se connecter avec nouveau mot de passe</li>
        </ol>
      </div>

      <!-- Config Resend -->
      <div class="card">
        <h2><span class="icon">⚙️</span> Configuration</h2>
        <?php if ($resend_configured): ?>
          <div class="status success">
            ✅ Resend est configuré
          </div>
          <p style="font-size: 13px; color: #666;">Clé API: <?php echo substr($_SESSION['resend_api_key'], 0, 10); ?>...</p>
        <?php else: ?>
          <div class="status warning">
            ⚠️ Resend n'est pas configuré
          </div>
          <p style="font-size: 13px; margin-bottom: 12px;">Les emails seront sauvegardés en base mais pas envoyés. Configurez Resend pour les envoyer.</p>
        <?php endif; ?>
        <a href="/keep-my-pet/app/Views/resend_setup.php" style="display: block;">
          <button type="button">⚙️ Configurer Resend</button>
        </a>
      </div>
    </div>

    <div class="divider"></div>

    <!-- Liens directs -->
    <div class="card">
      <h2>🔗 Liens Directs</h2>
      <div class="link-list">
        <h3>Pages du système:</h3>
        <ul>
          <li><a href="/keep-my-pet/app/Views/forgotten_password.php">forgotten_password.php</a> - Page de demande</li>
          <li><a href="/keep-my-pet/app/Views/debug_emails.php">debug_emails.php</a> - Voir les emails générés</li>
          <li><a href="/keep-my-pet/app/Views/resend_setup.php">resend_setup.php</a> - Configuration Resend</li>
        </ul>
      </div>
    </div>
  </div>

  <script>
    document.getElementById('testForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const email = document.getElementById('test_email').value;
      const resultDiv = document.getElementById('testResult');

      resultDiv.innerHTML = '<div class="status warning">⏳ En cours...</div>';

      try {
        const formData = new FormData();
        formData.append('email', email);

        const response = await fetch('/keep-my-pet/app/Views/request_password_reset.php', {
          method: 'POST',
          body: formData
        });

        const data = await response.json();

        if (data.success) {
          resultDiv.innerHTML = `
                        <div class="status success">
                            ✅ ${data.message}
                        </div>
                        <div class="info-box">
                            <strong>Prochaine étape:</strong><br>
                            1. Allez sur <a href="/keep-my-pet/app/Views/debug_emails.php" style="color: #667eea; font-weight: 600;">debug_emails.php</a><br>
                            2. Trouvez l'email que vous venez d'envoyer<br>
                            3. Copiez le lien de réinitialisation<br>
                            4. Cliquez sur le lien pour tester
                        </div>
                    `;
        } else {
          resultDiv.innerHTML = `<div class="status error">❌ ${data.message}</div>`;
        }
      } catch (error) {
        resultDiv.innerHTML = `<div class="status error">❌ Erreur: ${error.message}</div>`;
      }
    });

    function testResetPage() {
      const token = document.getElementById('test_token').value;
      if (!token) {
        alert('Veuillez coller le token d\'abord');
        return;
      }
      window.open(`/keep-my-pet/app/Views/reset_password.php?token=${encodeURIComponent(token)}`, '_blank');
    }
  </script>
</body>

</html>