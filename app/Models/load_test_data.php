<?php
/**
 * Script to load test data into the database
 * Exécutez: http://localhost:8888/keep-my-pet/app/Models/load_test_data.php
 */

require_once __DIR__ . '/connection_db.php';

try {
    // Lire le fichier SQL
    $sqlFile = __DIR__ . '/../../test_data.sql';
    $sql = file_get_contents($sqlFile);

    // Exécuter les requêtes SQL
    $db->exec($sql);

    echo "<h1 style='color: green; font-family: Arial;'>✓ Données de test importées avec succès !</h1>";
    echo "<p style='font-family: Arial;'>Les annonces, utilisateurs et animaux sont maintenant dans la base de données.</p>";
    echo "<p style='font-family: Arial;'><a href='/keep-my-pet/app/Views/advertisements.php'>Voir les annonces</a></p>";
    echo "<h3 style='font-family: Arial;'>Comptes de test :</h3>";
    echo "<ul style='font-family: Arial;'>";
    echo "<li><strong>jean@example.com</strong> - Mot de passe: password123</li>";
    echo "<li><strong>marie@example.com</strong> - Mot de passe: password123</li>";
    echo "<li><strong>pierre@example.com</strong> - Mot de passe: password123</li>";
    echo "<li><strong>sophie@example.com</strong> - Mot de passe: password123</li>";
    echo "</ul>";

} catch (PDOException $e) {
    echo "<h1 style='color: red; font-family: Arial;'>✗ Erreur lors de l'import !</h1>";
    echo "<p style='font-family: Arial; color: red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p style='font-family: Arial;'>Conseil : Si vous avez une erreur de clé primaire, videz la table et réessayez.</p>";
}
?>
