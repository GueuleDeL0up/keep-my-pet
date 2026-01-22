<?php

// Include database connection
require_once __DIR__ . '/connection_db.php';

/**
 * Get all advertisements with user and animal information
 * @return array List of advertisements
 */
function obtenirToutesAnnonces()
{
    try {
        global $db;
        $query = "
            SELECT 
                a.id,
                a.user_id,
                a.title,
                a.description,
                a.city,
                a.start_date,
                a.start_hour,
                a.end_date,
                a.end_hour,
                a.price,
                a.type,
                u.first_name,
                u.last_name,
                u.email,
                u.phone_number,
                an.name as animal_name,
                an.race as animal_race
            FROM advertisements a
            LEFT JOIN users u ON a.user_id = u.id
            LEFT JOIN animals an ON a.animal_id = an.id
            WHERE a.end_date >= CURDATE()
            ORDER BY a.start_date DESC
        ";

        $stmt = $db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des annonces: " . $e->getMessage());
        return [];
    }
}

/**
 * Get a specific advertisement by ID
 * @param int $id Advertisement ID
 * @return array|null Advertisement data or null if not found
 */
function obtenirAnnoncePar($id)
{
    try {
        global $db;
        $query = "
            SELECT 
                a.id,
                a.user_id,
                a.title,
                a.description,
                a.city,
                a.start_date,
                a.start_hour,
                a.end_date,
                a.end_hour,
                a.price,
                a.type,
                u.first_name,
                u.last_name,
                u.phone_number,
                u.email,
                an.name as animal_name,
                an.race as animal_race
            FROM advertisements a
            LEFT JOIN users u ON a.user_id = u.id
            LEFT JOIN animals an ON a.animal_id = an.id
            WHERE a.id = ?
        ";

        $stmt = $db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération de l'annonce: " . $e->getMessage());
        return null;
    }
}

/**
 * Get a specific advertisement by ID (alias)
 * @param int $id Advertisement ID
 * @return array|null Advertisement data or null if not found
 */
function obtenirAnnonceParId($id)
{
    return obtenirAnnoncePar($id);
}

/**
 * Get advertisements by user ID
 * @param int $user_id User ID
 * @return array List of advertisements
 */
function obtenirAnnoncesParUtilisateur($user_id)
{
    try {
        global $db;
        $query = "
            SELECT 
                a.id,
                a.user_id,
                a.animal_id,
                a.title,
                a.description,
                a.city,
                a.start_date,
                a.start_hour,
                a.end_date,
                a.end_hour,
                a.price,
                a.type,
                u.first_name,
                u.last_name,
                an.name as animal_name,
                an.race as animal_race
            FROM advertisements a
            LEFT JOIN users u ON a.user_id = u.id
            LEFT JOIN animals an ON a.animal_id = an.id
            WHERE a.user_id = ?
            ORDER BY a.start_date DESC
        ";

        $stmt = $db->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des annonces de l'utilisateur: " . $e->getMessage());
        return [];
    }
}

/**
 * Create a new advertisement
 * @param array $data Advertisement data
 * @return bool|int Advertisement ID if successful, false otherwise
 */
function creerAnnonce($data)
{
    try {
        global $db;
        $query = "
            INSERT INTO advertisements (user_id, animal_id, title, description, city, start_date, start_hour, end_date, end_hour, price, type)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $db->prepare($query);
        $result = $stmt->execute([
            $data['user_id'],
            $data['animal_id'],
            $data['title'],
            $data['description'],
            $data['city'],
            $data['start_date'],
            $data['start_hour'],
            $data['end_date'],
            $data['end_hour'],
            $data['price'],
            $data['type']
        ]);

        return $result ? $db->lastInsertId() : false;
    } catch (PDOException $e) {
        error_log("Erreur lors de la création de l'annonce: " . $e->getMessage());
        return false;
    }
}

/**
 * Update an advertisement
 * @param int $id Advertisement ID
 * @param array $data Updated advertisement data
 * @return bool Success status
 */
function mettreAJourAnnonce($id, $data)
{
    try {
        global $db;
        $query = "
            UPDATE advertisements 
            SET title = ?, description = ?, city = ?, start_date = ?, start_hour = ?, end_date = ?, end_hour = ?, price = ?, type = ?
            WHERE id = ? AND user_id = ?
        ";

        $stmt = $db->prepare($query);
        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['city'],
            $data['start_date'],
            $data['start_hour'],
            $data['end_date'],
            $data['end_hour'],
            $data['price'],
            $data['type'],
            $id,
            $data['user_id']
        ]);
    } catch (PDOException $e) {
        error_log("Erreur lors de la mise à jour de l'annonce: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete an advertisement
 * @param int $id Advertisement ID
 * @param int $user_id User ID (for authorization check)
 * @return bool Success status
 */
function supprimerAnnonce($id, $user_id)
{
    try {
        global $db;
        $query = "DELETE FROM advertisements WHERE id = ? AND user_id = ?";

        $stmt = $db->prepare($query);
        return $stmt->execute([$id, $user_id]);
    } catch (PDOException $e) {
        error_log("Erreur lors de la suppression de l'annonce: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete an advertisement (admin, no user check)
 */
function supprimerAnnonceAdmin(int $id): bool
{
    try {
        global $db;
        $query = "DELETE FROM advertisements WHERE id = ?";
        $stmt = $db->prepare($query);
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        error_log("Erreur lors de la suppression admin de l'annonce: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete all advertisements belonging to a user (admin cleanup)
 */
function supprimerAnnoncesParUtilisateur(int $user_id): bool
{
    try {
        global $db;
        $query = "DELETE FROM advertisements WHERE user_id = ?";
        $stmt = $db->prepare($query);
        return $stmt->execute([$user_id]);
    } catch (PDOException $e) {
        error_log("Erreur lors de la suppression des annonces de l'utilisateur: " . $e->getMessage());
        return false;
    }
}

/**
 * Search advertisements by filters
 * @param array $filters Search filters (type, city, animal_type, etc.)
 * @return array Filtered advertisements
 */
function rechercherAnnonces($filters)
{
    try {
        global $db;
        $query = "
            SELECT 
                a.id,
                a.user_id,
                a.title,
                a.description,
                a.city,
                a.start_date,
                a.start_hour,
                a.end_date,
                a.end_hour,
                a.price,
                a.type,
                u.first_name,
                u.last_name,
                an.name as animal_name,
                an.race as animal_race
            FROM advertisements a
            LEFT JOIN users u ON a.user_id = u.id
            LEFT JOIN animals an ON a.animal_id = an.id
            WHERE a.end_date >= CURDATE()
        ";

        $params = [];

        if (!empty($filters['type'])) {
            $query .= " AND a.type = ?";
            $params[] = $filters['type'];
        }

        if (!empty($filters['city'])) {
            $query .= " AND a.city LIKE ?";
            $params[] = "%" . $filters['city'] . "%";
        }

        if (!empty($filters['min_price'])) {
            $query .= " AND a.price >= ?";
            $params[] = $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $query .= " AND a.price <= ?";
            $params[] = $filters['max_price'];
        }

        if (!empty($filters['search'])) {
            $query .= " AND (a.title LIKE ? OR a.description LIKE ?)";
            $search_term = "%" . $filters['search'] . "%";
            $params[] = $search_term;
            $params[] = $search_term;
        }

        $query .= " ORDER BY a.start_date DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur lors de la recherche d'annonces: " . $e->getMessage());
        return [];
    }
}
