<?php
// Define the base
$base_url = "/keep-my-pet/";  // For HTML links
$base_dir = __DIR__ . "/../../";  // For PHP includes

// on récupère les requêtes génériques
require_once($base_dir . "app/Models/requests.basics.php");

//on définit le nom de la table
$table = "users";

/**
 * Recherche un utilisateur en fonction du nom passé en paramètre
 * @param PDO $bdd
 * @param string $nom
 * @return array
 */
function rechercheParNom(PDO $bdd, string $nom): array
{
    $statement = $bdd->prepare('SELECT * FROM users WHERE username = :username');
    $statement->bindParam(":username", $nom, PDO::PARAM_STR);
    $statement->execute();

    return $statement->fetchAll();
}

/**
 * Recherche un utilisateur par email
 * @param PDO $bdd
 * @param string $email
 * @return array
 */
function trouveParEmail(PDO $bdd, string $email): array
{
    $statement = $bdd->prepare('SELECT * FROM users WHERE email = :email');
    $statement->bindParam(":email", $email, PDO::PARAM_STR);
    $statement->execute();

    return $statement->fetchAll();
}

/**
 * Récupère tous les enregistrements de la table users
 * @param PDO $bdd
 * @return array
 */
function recupereTousUtilisateurs(PDO $bdd): array
{
    $query = 'SELECT * FROM users';
    return $bdd->query($query)->fetchAll();
}

/**
 * Recherche un utilisateur par ID
 * @param PDO $bdd
 * @param int $id
 * @return array
 */
function trouveParId(PDO $bdd, int $id): array
{
    $stmt = $bdd->prepare('SELECT * FROM users WHERE id = :id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Récupère un utilisateur par son ID (retourne un seul utilisateur ou false)
 * @param int $id
 * @return array|false
 */
function obtenirUtilisateurParId(int $id)
{
    global $db;
    $stmt = $db->prepare('SELECT u.*, COUNT(DISTINCT r.id) as review_count FROM users u LEFT JOIN reviews r ON u.id = r.reviewed_user_id WHERE u.id = :id GROUP BY u.id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Vérifie si $follower_id suit $followed_id
 * @param PDO $bdd
 * @param int $follower_id
 * @param int $followed_id
 * @return bool
 */
function estSuivi(PDO $bdd, int $follower_id, int $followed_id): bool
{
    $stmt = $bdd->prepare('SELECT 1 FROM follows WHERE follower_id = :follower AND followed_id = :followed');
    $stmt->bindParam(':follower', $follower_id, PDO::PARAM_INT);
    $stmt->bindParam(':followed', $followed_id, PDO::PARAM_INT);
    $stmt->execute();
    return (bool)$stmt->fetchColumn();
}

/**
 * Vérifie si $user_id a ajouté $target_user_id en favori
 * @param PDO $bdd
 * @param int $user_id
 * @param int $target_user_id
 * @return bool
 */
function estFavori(PDO $bdd, int $user_id, int $target_user_id): bool
{
    $stmt = $bdd->prepare('SELECT 1 FROM favorites WHERE user_id = :user AND target_user_id = :target');
    $stmt->bindParam(':user', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':target', $target_user_id, PDO::PARAM_INT);
    $stmt->execute();
    return (bool)$stmt->fetchColumn();
}

/**
 * Ajoute un nouvel utilisateur dans la base de données
 * @param PDO $bdd
 * @param array $utilisateur
 * @return bool
 */
function ensureUsersHasAddressColumns(PDO $bdd): void
{
    // Add address and postal_code columns if they don't exist (MySQL 8+ supports IF NOT EXISTS)
    try {
        $bdd->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS address VARCHAR(255) NOT NULL DEFAULT ''");
        $bdd->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS postal_code VARCHAR(10) NOT NULL DEFAULT ''");
    } catch (Exception $e) {
        // ignore failures on older MySQL versions; insertion will fail if column missing
    }
}

function ajouteUtilisateur(PDO $bdd, array $utilisateur): bool
{
    // Ensure address columns exist
    ensureUsersHasAddressColumns($bdd);

    // On fournit des valeurs par défaut pour les champs non fournis
    $query = 'INSERT INTO users (email, password, first_name, last_name, phone_number, address, postal_code, gender, is_admin, note, theme, language) 
              VALUES (:email, :password, :first_name, :last_name, :phone_number, :address, :postal_code, :gender, :is_admin, :note, :theme, :language)';

    $donnees = $bdd->prepare($query);

    $donnees->bindParam(':email', $utilisateur['email'], PDO::PARAM_STR);
    $donnees->bindParam(':password', $utilisateur['password'], PDO::PARAM_STR);
    $donnees->bindParam(':first_name', $utilisateur['first_name'], PDO::PARAM_STR);
    $donnees->bindParam(':last_name', $utilisateur['last_name'], PDO::PARAM_STR);
    $donnees->bindParam(':phone_number', $utilisateur['phone_number'], PDO::PARAM_STR);
    $donnees->bindParam(':address', $utilisateur['address'], PDO::PARAM_STR);
    $donnees->bindParam(':postal_code', $utilisateur['postal_code'], PDO::PARAM_STR);
    $donnees->bindParam(':gender', $utilisateur['gender'], PDO::PARAM_STR);
    $donnees->bindValue(':is_admin', $utilisateur['is_admin'], PDO::PARAM_INT);
    $donnees->bindValue(':note', $utilisateur['note']);
    $donnees->bindParam(':theme', $utilisateur['theme'], PDO::PARAM_STR);
    $donnees->bindParam(':language', $utilisateur['language'], PDO::PARAM_STR);

    return $donnees->execute();
}

/**
 * Met à jour un utilisateur avec les champs fournis
 * @param PDO $bdd
 * @param int $id
 * @param array $fields (email, first_name, last_name, phone_number, address, postal_code)
 * @return bool
 */
function updateUtilisateur(PDO $bdd, int $id, array $fields): bool
{
    // Only allow these fields to be updated
    $allowed = ['email', 'first_name', 'last_name', 'phone_number', 'address', 'postal_code'];
    $sets = [];
    $params = [];

    foreach ($allowed as $f) {
        if (array_key_exists($f, $fields)) {
            $sets[] = "$f = :$f";
            $params[":$f"] = $fields[$f];
        }
    }

    if (empty($sets)) {
        return false; // nothing to do
    }

    // Ensure address columns exist if we are updating address/postal_code
    ensureUsersHasAddressColumns($bdd);

    $sql = 'UPDATE users SET ' . implode(', ', $sets) . ' WHERE id = :id';
    $stmt = $bdd->prepare($sql);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v, PDO::PARAM_STR);
    }
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    return $stmt->execute();
}

/**
 * Met à jour le mot de passe d'un utilisateur
 * @param PDO $bdd
 * @param int $id
 * @param string $hash
 * @return bool
 */
function updatePassword(PDO $bdd, int $id, string $hash): bool
{
    $stmt = $bdd->prepare('UPDATE users SET password = :password WHERE id = :id');
    $stmt->bindParam(':password', $hash, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    return $stmt->execute();
}

/**
 * Recherche des utilisateurs par email, prénom ou nom
 */
function rechercherUtilisateurs(PDO $bdd, string $term): array
{
    $like = '%' . $term . '%';
    $sql = 'SELECT * FROM users WHERE email LIKE :like OR first_name LIKE :like OR last_name LIKE :like ORDER BY id DESC';
    $stmt = $bdd->prepare($sql);
    $stmt->bindParam(':like', $like, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Supprime un utilisateur par ID
 */
function supprimerUtilisateur(PDO $bdd, int $id): bool
{
    $stmt = $bdd->prepare('DELETE FROM users WHERE id = :id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}
