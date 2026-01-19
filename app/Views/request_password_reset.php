<?php
session_start();

/**
 * request_password_reset.php
 * Traite la demande de réinitialisation de mot de passe
 * Envoie un email avec un lien de réinitialisation
 */

$base_url = "/keep-my-pet/";
$base_dir = __DIR__ . "/../";

// Charger les dépendances
require $base_dir . 'Models/connection_db.php';
require $base_dir . 'Models/requests.password_reset.php';

// Réponse JSON
header('Content-Type: application/json; charset=utf-8');

try {
  // Valider que c'est une requête POST
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    throw new Exception('Méthode non autorisée');
  }

  // Récupérer et valider l'email
  $email = isset($_POST['email']) ? trim($_POST['email']) : '';

  if (empty($email)) {
    throw new Exception('Veuillez entrer votre adresse email');
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    throw new Exception('Adresse email invalide');
  }

  // Chercher l'utilisateur
  $user_id = trouverUserParEmail($db, $email);

  if (!$user_id) {
    // Pour des raisons de sécurité, on ne dit pas si l'email existe ou pas
    // On affiche le même message
    http_response_code(200);
    echo json_encode([
      'success' => true,
      'message' => 'Si cette adresse email existe dans notre base, un lien de réinitialisation a été envoyé.'
    ]);
    exit;
  }

  // Générer un token
  $token = genererTokenReinit();

  // Sauvegarder le token en base
  if (!sauvegarderTokenReinit($db, $user_id, $token)) {
    throw new Exception('Erreur lors de la création du lien de réinitialisation');
  }

  // Récupérer les infos de l'utilisateur
  $stmt = $db->prepare("SELECT first_name FROM users WHERE id = ?");
  $stmt->execute([$user_id]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  // Construire le lien de réinitialisation
  $lien_reinit = $base_url . "app/Views/reset_password.php?token=" . urlencode($token);
  if (!preg_match('/^https?:\/\//', $lien_reinit)) {
    // Si pas de protocole, ajouter selon le contexte
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $lien_reinit = $protocol . "://" . $host . "/" . ltrim($lien_reinit, '/');
  }

  // Envoyer l'email
  if (envoyerEmailReinit($db, $email, $user['first_name'], $lien_reinit, $token)) {
    http_response_code(200);
    echo json_encode([
      'success' => true,
      'message' => 'Si cette adresse email existe dans notre base, un lien de réinitialisation a été envoyé.',
      'debug_link' => '/keep-my-pet/app/Views/debug_emails.php'
    ]);
  } else {
    throw new Exception('Erreur lors de l\'envoi de l\'email. Veuillez réessayer.');
  }
} catch (Exception $e) {
  http_response_code(400);
  echo json_encode([
    'success' => false,
    'message' => $e->getMessage()
  ]);
}
