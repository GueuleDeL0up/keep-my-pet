<?php

// Start session
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Include models
require_once __DIR__ . '/../Models/connection_db.php';
require_once __DIR__ . '/../Models/requests.bookings.php';

// Check booking status via AJAX
if (isset($_GET['check_booking'])) {
  header('Content-Type: application/json');

  $ad_id = (int)($_GET['ad_id'] ?? 0);
  $sitter_id = $_SESSION['user_id'] ?? null;

  if (!$sitter_id || !$ad_id) {
    echo json_encode(['has_booking' => false]);
    exit;
  }

  $has_booking = aDejaDemandeAnnonce($ad_id, $sitter_id);
  echo json_encode(['has_booking' => $has_booking]);
  exit;
}

// Default response
header('Content-Type: application/json');
echo json_encode(['error' => 'Invalid request']);
