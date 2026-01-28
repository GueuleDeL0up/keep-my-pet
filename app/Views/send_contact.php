<?php

/**
 * send_contact.php - Traite l'envoi du formulaire de contact
 * Envoie un email via Gmail SMTP
 */

$base_url = "/keep-my-pet/";
$base_dir = __DIR__ . "/../";

// Charger la configuration email
$email_config = require $base_dir . '/app/Config/email_config.php';

// Réponse JSON
header('Content-Type: application/json; charset=utf-8');

try {
  // Valider que c'est une requête POST
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    throw new Exception('Méthode non autorisée');
  }

  // Récupérer et valider les données
  $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
  $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
  $email = isset($_POST['email']) ? trim($_POST['email']) : '';
  $message = isset($_POST['message']) ? trim($_POST['message']) : '';

  // Validations
  if (empty($nom) || empty($prenom) || empty($email) || empty($message)) {
    throw new Exception('Tous les champs sont obligatoires');
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    throw new Exception('Adresse email invalide');
  }

  if (strlen($message) < 10) {
    throw new Exception('Le message doit contenir au moins 10 caractères');
  }

  // Préparer les données du mail
  $to = $email_config['from_email'];
  $subject = "Nouveau message de contact de " . htmlspecialchars($nom . ' ' . $prenom);

  // Body du mail
  $body = "
    <html>
        <head>
            <title>Nouveau message de contact</title>
            <style>
                body { font-family: Arial, sans-serif; background: #f5f7fa; }
                .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
                .header { border-bottom: 3px solid #5fbbb7; padding-bottom: 20px; margin-bottom: 20px; }
                .header h1 { color: #5fbbb7; margin: 0; font-size: 24px; }
                .content { margin: 20px 0; }
                .field { margin-bottom: 20px; }
                .label { color: #5fbbb7; font-weight: bold; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 5px; }
                .value { color: #333; padding: 10px; background: #f8f9fc; border-left: 3px solid #5fbbb7; }
                .message-box { background: #f8f9fc; padding: 15px; border-radius: 5px; border-left: 4px solid #57b1c8; }
                .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0efed; color: #999; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>📬 Nouveau message de contact</h1>
                </div>
                <div class='content'>
                    <div class='field'>
                        <span class='label'>Nom</span>
                        <div class='value'>" . htmlspecialchars($nom) . "</div>
                    </div>
                    <div class='field'>
                        <span class='label'>Prénom</span>
                        <div class='value'>" . htmlspecialchars($prenom) . "</div>
                    </div>
                    <div class='field'>
                        <span class='label'>Email de contact</span>
                        <div class='value'>" . htmlspecialchars($email) . "</div>
                    </div>
                    <div class='field'>
                        <span class='label'>Message</span>
                        <div class='message-box'>" . nl2br(htmlspecialchars($message)) . "</div>
                    </div>
                </div>
                <div class='footer'>
                    <p>Ce message a été envoyé via le formulaire de contact de KeepMyPet</p>
                    <p>Date: " . date('d/m/Y à H:i:s') . "</p>
                </div>
            </div>
        </body>
    </html>
    ";

  // Headers du mail
  $headers = "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
  $headers .= "From: " . $email_config['from_name'] . " <" . $email_config['from_email'] . ">\r\n";
  $headers .= "Reply-To: " . htmlspecialchars($email) . "\r\n";
  $headers .= "X-Mailer: PHP/" . phpversion();

  // Envoyer le mail avec la fonction native mail()
  // Note: Cette méthode fonctionne si le serveur est configuré pour envoyer des mails
  // Pour une meilleure compatibilité, utiliser PHPMailer avec SMTP

  if (mail($to, $subject, $body, $headers)) {
    // Envoyer aussi un mail de confirmation au visiteur
    $confirmation_subject = "Nous avons reçu votre message - KeepMyPet";
    $confirmation_body = "
        <html>
            <head>
                <title>Confirmation de message</title>
                <style>
                    body { font-family: Arial, sans-serif; background: #f5f7fa; }
                    .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
                    .header { border-bottom: 3px solid #5fbbb7; padding-bottom: 20px; margin-bottom: 20px; }
                    .header h1 { color: #5fbbb7; margin: 0; font-size: 24px; }
                    .content { margin: 20px 0; line-height: 1.6; color: #333; }
                    .cta { margin-top: 30px; text-align: center; }
                    .button { display: inline-block; padding: 12px 28px; background: linear-gradient(135deg, #5fbbb7 0%, #57b1c8 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: bold; }
                    .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0efed; color: #999; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>✅ Message reçu</h1>
                    </div>
                    <div class='content'>
                        <p>Bonjour " . htmlspecialchars($prenom) . ",</p>
                        <p>Merci de nous avoir contacté! Nous avons bien reçu votre message et nous vous répondrons dans les plus brefs délais.</p>
                        <p>Notre équipe KeepMyPet vous remercie de votre intérêt.</p>
                        <div class='cta'>
                            <a href='" . $base_url . "' class='button'>Retour à KeepMyPet</a>
                        </div>
                    </div>
                    <div class='footer'>
                        <p>© 2025 KeepMyPet - Tous droits réservés</p>
                    </div>
                </div>
            </body>
        </html>
        ";

    $confirmation_headers = "MIME-Version: 1.0\r\n";
    $confirmation_headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $confirmation_headers .= "From: " . $email_config['from_name'] . " <" . $email_config['from_email'] . ">\r\n";

    // Envoyer la confirmation (on ne l'affiche que dans les logs si elle échoue)
    mail($email, $confirmation_subject, $confirmation_body, $confirmation_headers);

    http_response_code(200);
    echo json_encode([
      'success' => true,
      'message' => 'Votre message a été envoyé avec succès! Nous vous répondrons bientôt.'
    ]);
  } else {
    throw new Exception('Erreur lors de l\'envoi du mail. Veuillez réessayer.');
  }
} catch (Exception $e) {
  http_response_code(400);
  echo json_encode([
    'success' => false,
    'message' => $e->getMessage()
  ]);
}
