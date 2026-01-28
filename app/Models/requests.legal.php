<?php
// Legal content queries (CGU and Mentions Légales)
require_once __DIR__ . '/connection_db.php';

/**
 * Retourne la dernière entrée publiée par type (cgu | mentions).
 * Attendu : table `cgu` avec colonnes id, type (ENUM('cgu','mentions')), titre, contenu (TEXT), updated_at (DATETIME), published (TINYINT).
 */
function obtenirDernierLegalParType(string $type)
{
  global $db;
  $current_language = isset($_SESSION['language']) ? $_SESSION['language'] : 'fr';
  $sql = "SELECT id, type, titre, contenu, updated_at FROM cgu
            WHERE type = :type AND language = :language AND (published = 1 OR published IS NULL)
            ORDER BY updated_at DESC, id DESC
            LIMIT 1";
  $stmt = $db->prepare($sql);
  $stmt->execute([':type' => $type, ':language' => $current_language]);
  return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

function obtenirDerniereCGU()
{
  return obtenirDernierLegalParType('cgu');
}

function obtenirDernieresMentions()
{
  return obtenirDernierLegalParType('mentions');
}
