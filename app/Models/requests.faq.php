<?php
// FAQ queries
require_once __DIR__ . '/connection_db.php';

/**
 * Retourne toutes les FAQ publiées triées par position
 */
function obtenirToutesFAQ()
{
    global $db;
    $current_language = isset($_SESSION['language']) ? $_SESSION['language'] : 'fr';
    $sql = "SELECT id, question, reponse, position FROM faq
            WHERE language = :language AND (published = 1 OR published IS NULL)
            ORDER BY position ASC, id ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute([':language' => $current_language]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}
