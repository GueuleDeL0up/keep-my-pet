<?php
// Define the base
$base_url = "/keep-my-pet/";  // For HTML links
$base_dir = __DIR__ . "/../../";  // For PHP includes

// on récupère les requêtes génériques
include($base_dir . "app/Models/requests.basics.php");

//on définit le nom de la table
$table = "advertisements";

/**
 * Recherche les annonces en fonction du type passé en paramètre
 * @param PDO $bdd
 * @param string $table
 * @param string $type
 * @return array
 */
function rechercheParType(PDO $bdd, string $table, string $type): array
{

    return recherche($bdd, $table, ['type' => $type]);
}
