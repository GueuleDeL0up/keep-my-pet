<?php
require_once __DIR__ . "/app/Models/connection_db.php";

// Vérifier les annonces et leurs user_id
$stmt = $db->query("SELECT id, user_id, title FROM advertisements LIMIT 5");
$ads = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Annonces trouvées:\n";
print_r($ads);

// Vérifier les utilisateurs
if (!empty($ads)) {
  foreach ($ads as $ad) {
    $stmt = $db->prepare("SELECT id, first_name, last_name FROM users WHERE id = ?");
    $stmt->execute([$ad['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "\nUtilisateur " . $ad['user_id'] . ": ";
    if ($user) {
      echo $user['first_name'] . " " . $user['last_name'];
    } else {
      echo "NON TROUVÉ!";
    }
  }
}
