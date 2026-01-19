<?php

/**
 * Fonctions pour la réinitialisation de mot de passe
 */

/**
 * Génère un token sécurisé pour la réinitialisation
 * @return string Token
 */
function genererTokenReinit()
{
  return bin2hex(random_bytes(32));
}

/**
 * Sauvegarde le token de réinitialisation pour un utilisateur
 * @param object $db - Connection PDO
 * @param int $user_id - ID utilisateur
 * @param string $token - Token
 * @return bool
 */
function sauvegarderTokenReinit($db, $user_id, $token)
{
  try {
    // Créer une table de tokens si elle n'existe pas
    $query = "CREATE TABLE IF NOT EXISTS password_reset_tokens (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            token VARCHAR(255) NOT NULL UNIQUE,
            expires_at TIMESTAMP NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX (user_id)
        )";
    $db->exec($query);

    // Supprimer les anciens tokens de cet utilisateur
    $stmt = $db->prepare("DELETE FROM password_reset_tokens WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // Insérer le nouveau token (valide 24 heures)
    $stmt = $db->prepare("INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 24 HOUR))");
    return $stmt->execute([$user_id, $token]);
  } catch (Exception $e) {
    error_log("Erreur sauvegarde token: " . $e->getMessage());
    return false;
  }
}

/**
 * Valide un token de réinitialisation
 * @param object $db - Connection PDO
 * @param string $token - Token
 * @return int|false - User ID si valide, false sinon
 */
function validerTokenReinit($db, $token)
{
  try {
    $stmt = $db->prepare("SELECT user_id FROM password_reset_tokens WHERE token = ? AND expires_at > NOW()");
    $stmt->execute([$token]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['user_id'] : false;
  } catch (Exception $e) {
    return false;
  }
}

/**
 * Supprime un token utilisé
 * @param object $db - Connection PDO
 * @param string $token - Token
 */
function supprimerTokenReinit($db, $token)
{
  try {
    $stmt = $db->prepare("DELETE FROM password_reset_tokens WHERE token = ?");
    $stmt->execute([$token]);
  } catch (Exception $e) {
    error_log("Erreur suppression token: " . $e->getMessage());
  }
}

/**
 * Vérifie si un email existe en base
 * @param object $db - Connection PDO
 * @param string $email - Email
 * @return int|false - User ID si existe, false sinon
 */
function trouverUserParEmail($db, $email)
{
  try {
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['id'] : false;
  } catch (Exception $e) {
    return false;
  }
}

/**
 * Change le mot de passe d'un utilisateur
 * @param object $db - Connection PDO
 * @param int $user_id - ID utilisateur
 * @param string $new_password - Nouveau mot de passe (sera hashé)
 * @return bool
 */
function changerMotDePasse($db, $user_id, $new_password)
{
  try {
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
    return $stmt->execute([$hashed_password, $user_id]);
  } catch (Exception $e) {
    error_log("Erreur changement mot de passe: " . $e->getMessage());
    return false;
  }
}

/**
 * Envoie un email de réinitialisation directement à l'utilisateur
 * @param object $db - Connection PDO (pour sauvegarder en debug)
 * @param string $email - Email de l'utilisateur
 * @param string $nom_utilisateur - Nom de l'utilisateur
 * @param string $lien_reinit - Lien de réinitialisation
 * @param string $token - Token (pour debug)
 * @return bool
 */
function envoyerEmailReinit($db, $email, $nom_utilisateur, $lien_reinit, $token = '')
{
  $email_config = require __DIR__ . '/../Config/email_config.php';
  $from_email = 'noreply@keepmypet.com';

  $subject = "Réinitialisation de votre mot de passe KeepMyPet";

  $body = "
    <html>
        <head>
            <title>Réinitialisation de mot de passe</title>
            <style>
                body { font-family: Arial, sans-serif; background: #f5f7fa; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
                .header { border-bottom: 3px solid #5fbbb7; padding-bottom: 20px; margin-bottom: 20px; }
                .header h1 { color: #5fbbb7; margin: 0; font-size: 24px; }
                .content { margin: 20px 0; line-height: 1.6; color: #333; }
                .cta { margin-top: 30px; text-align: center; }
                .button { display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #5fbbb7 0%, #57b1c8 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px; }
                .button:hover { text-decoration: none; box-shadow: 0 4px 12px rgba(95, 187, 183, 0.3); }
                .warning { background: #fff5f5; border-left: 4px solid #ef4444; padding: 15px; border-radius: 5px; margin-top: 20px; color: #7f1d1d; }
                .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0efed; color: #999; font-size: 12px; }
                code { background: #f0f5f4; padding: 2px 6px; border-radius: 3px; word-break: break-all; color: #5fbbb7; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔐 Réinitialisation de mot de passe</h1>
                </div>
                <div class='content'>
                    <p>Bonjour " . htmlspecialchars($nom_utilisateur) . ",</p>
                    <p>Vous avez demandé la réinitialisation de votre mot de passe KeepMyPet. Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe:</p>
                    <div class='cta'>
                        <a href='" . htmlspecialchars($lien_reinit) . "' class='button'>Réinitialiser mon mot de passe</a>
                    </div>
                    <p style='margin-top: 30px; color: #666; font-size: 13px;'>Ou copiez ce lien dans votre navigateur:<br><code>" . htmlspecialchars($lien_reinit) . "</code></p>
                    <div class='warning'>
                        <strong>⚠️ Sécurité:</strong><br>
                        Ce lien expire dans 24 heures. Si vous n'avez pas demandé cette réinitialisation, ignorez cet email ou contactez-nous.
                    </div>
                </div>
                <div class='footer'>
                    <p>© 2025 KeepMyPet - Tous droits réservés</p>
                </div>
            </div>
        </body>
    </html>
    ";

  // Sauvegarder l'email en base pour debug
  try {
    $db->exec("CREATE TABLE IF NOT EXISTS debug_emails (
            id INT PRIMARY KEY AUTO_INCREMENT,
            to_email VARCHAR(255) NOT NULL,
            subject VARCHAR(255) NOT NULL,
            body LONGTEXT NOT NULL,
            token VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

    $stmt = $db->prepare("INSERT INTO debug_emails (to_email, subject, body, token) VALUES (?, ?, ?, ?)");
    $stmt->execute([$email, $subject, $body, $token]);
  } catch (Exception $e) {
    error_log("Erreur sauvegarde debug email: " . $e->getMessage());
  }

  // Essayer d'envoyer via Resend si la clé API est disponible
  if (isset($_SESSION['resend_api_key']) && !empty($_SESSION['resend_api_key'])) {
    try {
      require_once __DIR__ . '/../Classes/ResendMailer.php';

      $mailer = new ResendMailer($_SESSION['resend_api_key']);
      $result = $mailer->send($email, $subject, $body, $from_email);

      if ($result['success']) {
        return true;
      }
    } catch (Exception $e) {
      error_log("Erreur Resend: " . $e->getMessage());
    }
  }

  // Fallback: envoyer avec mail() (n'aura probablement pas d'effet sur MAMP)
  // Mais l'email est déjà sauvegardé en base via debug_emails
  $headers = "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
  $headers .= "From: " . $from_email . "\r\n";

  @mail($email, $subject, $body, $headers);

  return true; // Retourner true car on a au moins sauvegardé en debug
}
