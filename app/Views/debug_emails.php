<?php

/**
 * debug_emails.php
 * Page pour voir les emails "envoyés" sur MAMP
 * Utile pour tester le système sans serveur SMTP
 */

$base_dir = __DIR__ . "/../../";
require $base_dir . 'app/Models/connection_db.php';

// Créer la table si elle n'existe pas
try {
  $db->exec("CREATE TABLE IF NOT EXISTS debug_emails (
        id INT PRIMARY KEY AUTO_INCREMENT,
        to_email VARCHAR(255) NOT NULL,
        subject VARCHAR(255) NOT NULL,
        body LONGTEXT NOT NULL,
        token VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch (Exception $e) {
  error_log("Erreur création table debug_emails: " . $e->getMessage());
}

// Récupérer les derniers emails
try {
  $stmt = $db->query("SELECT * FROM debug_emails ORDER BY created_at DESC LIMIT 20");
  $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  $emails = [];
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Debug - Emails Envoyés</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f5f7fa;
      padding: 20px;
    }

    .container {
      max-width: 1000px;
      margin: 0 auto;
    }

    .header {
      background: linear-gradient(135deg, #5fbbb7 0%, #57b1c8 100%);
      color: white;
      padding: 30px;
      border-radius: 8px;
      margin-bottom: 30px;
    }

    .header h1 {
      font-size: 28px;
      margin-bottom: 10px;
    }

    .header p {
      opacity: 0.95;
    }

    .email-card {
      background: white;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      border-left: 4px solid #5fbbb7;
    }

    .email-to {
      color: #5fbbb7;
      font-weight: 700;
      font-size: 14px;
      margin-bottom: 8px;
    }

    .email-subject {
      font-size: 16px;
      font-weight: 700;
      color: #333;
      margin-bottom: 12px;
    }

    .email-body {
      background: #f8f9fc;
      padding: 15px;
      border-radius: 4px;
      max-height: 300px;
      overflow-y: auto;
      font-size: 13px;
      color: #666;
      line-height: 1.5;
    }

    .email-body iframe {
      width: 100%;
      border: none;
    }

    .email-footer {
      margin-top: 12px;
      font-size: 12px;
      color: #999;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .email-time {
      color: #5fbbb7;
      font-weight: 700;
    }

    .reset-link {
      background: #ecfdf5;
      border: 1px solid #86efac;
      padding: 10px;
      border-radius: 4px;
      margin: 10px 0;
      word-break: break-all;
      font-family: monospace;
      font-size: 12px;
      color: #065f46;
    }

    .no-emails {
      background: white;
      padding: 40px;
      text-align: center;
      border-radius: 8px;
      color: #999;
    }

    .back-link {
      display: inline-block;
      margin-bottom: 20px;
      padding: 10px 20px;
      background: white;
      color: #5fbbb7;
      text-decoration: none;
      border-radius: 4px;
      font-weight: 700;
      transition: all 0.3s ease;
    }

    .back-link:hover {
      background: #5fbbb7;
      color: white;
    }
  </style>
</head>

<body>
  <div class="container">
    <a href="/keep-my-pet/" class="back-link">← Retour</a>

    <div class="header">
      <h1>📧 Debug - Emails Envoyés</h1>
      <p>Voici les emails générés par le système (pour les tests sur MAMP)</p>
    </div>

    <?php if (empty($emails)): ?>
      <div class="no-emails">
        <i class="fas fa-inbox" style="font-size: 48px; color: #ddd; display: block; margin-bottom: 20px;"></i>
        <p>Aucun email envoyé pour le moment.</p>
      </div>
    <?php else: ?>
      <?php foreach ($emails as $email): ?>
        <div class="email-card">
          <div class="email-to">
            <i class="fas fa-envelope"></i>
            À: <?php echo htmlspecialchars($email['to_email']); ?>
          </div>
          <div class="email-subject">
            <?php echo htmlspecialchars($email['subject']); ?>
          </div>

          <?php if ($email['token']): ?>
            <div class="reset-link">
              <strong>Token de réinitialisation:</strong><br>
              /keep-my-pet/app/Views/reset_password.php?token=<?php echo htmlspecialchars($email['token']); ?>
            </div>
          <?php endif; ?>

          <div class="email-body">
            <?php echo $email['body']; ?>
          </div>

          <div class="email-footer">
            <span class="email-time">
              <i class="fas fa-clock"></i>
              <?php echo date('d/m/Y à H:i:s', strtotime($email['created_at'])); ?>
            </span>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</body>

</html>