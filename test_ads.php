<?php
require_once __DIR__ . "/app/Models/connection_db.php";
require_once __DIR__ . "/app/Models/requests.advertisements.php";

// Test 1: Get all advertisements
echo "=== TEST 1: obtenirToutesAnnonces() ===\n";
$annonces = obtenirToutesAnnonces();
echo "Nombre d'annonces trouvées: " . count($annonces) . "\n";
if (!empty($annonces)) {
  echo "Première annonce:\n";
  print_r($annonces[0]);
} else {
  echo "Aucune annonce trouvée!\n";
}

// Test 2: Direct query to advertisements table
echo "\n=== TEST 2: Requête directe ===\n";
try {
  $stmt = $db->query("SELECT id, user_id, title, type, city, price FROM advertisements LIMIT 5");
  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo "Résultats: " . count($results) . " annonces\n";
  if (!empty($results)) {
    print_r($results);
  }
} catch (Exception $e) {
  echo "Erreur: " . $e->getMessage() . "\n";
}

// Test 3: Check utilisateurs and animals tables
echo "\n=== TEST 3: Vérifier les tables ===\n";
$stmt = $db->query("SELECT COUNT(*) as count FROM advertisements");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Total annonces: " . $row['count'] . "\n";

$stmt = $db->query("SELECT COUNT(*) as count FROM utilisateurs");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Total utilisateurs: " . $row['count'] . "\n";

$stmt = $db->query("SELECT COUNT(*) as count FROM animals");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Total animaux: " . $row['count'] . "\n";
