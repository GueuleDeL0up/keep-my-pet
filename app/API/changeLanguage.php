<?php
// Handle language change
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once __DIR__ . '/../Config/language.php';
require_once __DIR__ . '/../Models/connection_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lang'])) {
  $lang = $_POST['lang'];
  $result = setLanguage($lang);

  // Rediriger vers la page précédente ou l'accueil
  $referrer = $_SERVER['HTTP_REFERER'] ?? '/keep-my-pet/app/Views/home.php';
  header('Location: ' . $referrer);
  exit;
}

// Sinon rediriger vers l'accueil
header('Location: /keep-my-pet/app/Views/home.php');
exit;
