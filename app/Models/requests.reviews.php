<?php

// Include database connection
require_once __DIR__ . '/connection_db.php';

/**
 * Create a review for an advertisement
 * @param int $ad_id
 * @param int $reviewer_id
 * @param int $reviewed_user_id
 * @param int $rating (1-5)
 * @param string|null $comment
 * @return bool
 */
function ensureReviewsTableExists(PDO $db): void
{
    try {
        // Create the table if missing (idempotent)
        $db->exec("CREATE TABLE IF NOT EXISTS `reviews` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `advertisement_id` int(11) NOT NULL,
            `reviewer_id` int(11) NOT NULL COMMENT 'User who gives the rating',
            `reviewed_user_id` int(11) DEFAULT NULL COMMENT 'User who receives the rating (pet sitter/owner)',
            `reviewed_animal_id` int(11) DEFAULT NULL COMMENT 'Animal who receives the rating',
            `rating` int(1) NOT NULL CHECK (rating >= 1 AND rating <= 5),
            `comment` text DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `reviewer_id` (`reviewer_id`),
            KEY `reviewed_user_id` (`reviewed_user_id`),
            KEY `reviewed_animal_id` (`reviewed_animal_id`),
            KEY `advertisement_id` (`advertisement_id`),
            CONSTRAINT `fk_reviews_advertisement` FOREIGN KEY (`advertisement_id`) REFERENCES `advertisements` (`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_reviews_reviewer` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_reviews_reviewed_user` FOREIGN KEY (`reviewed_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_reviews_animal` FOREIGN KEY (`reviewed_animal_id`) REFERENCES `animals` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
    } catch (PDOException $e) {
        error_log('Erreur lors de la création automatique de la table reviews: ' . $e->getMessage());
    }
}

function creerAvis(int $ad_id, int $reviewer_id, int $reviewed_user_id, int $rating, ?string $comment = null): bool
{
    try {
        global $db;

        // Ensure table exists
        ensureReviewsTableExists($db);
        
        // Check if this specific user has already been reviewed for this ad by this reviewer
        $check = $db->prepare("
            SELECT id FROM reviews 
            WHERE advertisement_id = ? 
            AND reviewer_id = ? 
            AND reviewed_user_id = ?
            AND reviewed_animal_id IS NULL
        ");
        $check->execute([$ad_id, $reviewer_id, $reviewed_user_id]);
        if ($check->fetch()) {
            error_log("DEBUG: Review already exists for ad=$ad_id, reviewer=$reviewer_id, reviewed_user=$reviewed_user_id");
            return false; // Already reviewed this specific user
        }
        
        $db->beginTransaction();
        
        // Insert review for user
        $stmt = $db->prepare("
            INSERT INTO reviews (advertisement_id, reviewer_id, reviewed_user_id, reviewed_animal_id, rating, comment) 
            VALUES (?, ?, ?, NULL, ?, ?)
        ");
        $stmt->execute([$ad_id, $reviewer_id, $reviewed_user_id, $rating, $comment]);
        
        // Update user's rating and review count
        $update = $db->prepare("
            UPDATE users u
            SET 
                u.note = (SELECT AVG(rating) FROM reviews WHERE reviewed_user_id = u.id AND reviewed_animal_id IS NULL)
            WHERE u.id = ?
        ");
        $update->execute([$reviewed_user_id]);
        
        error_log("DEBUG: Review created successfully for user $reviewed_user_id");
        $db->commit();
        return true;
    } catch (PDOException $e) {
        if (isset($db) && $db->inTransaction()) {
            $db->rollBack();
        }
        error_log("Erreur lors de la création de l'avis: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if user has already reviewed an advertisement
 * @param int $ad_id
 * @param int $reviewer_id
 * @return bool
 */
function aDejaNote(int $ad_id, int $reviewer_id): bool
{
    try {
        global $db;
        $stmt = $db->prepare("SELECT 1 FROM reviews WHERE advertisement_id = ? AND reviewer_id = ? LIMIT 1");
        $stmt->execute([$ad_id, $reviewer_id]);
        return (bool)$stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Erreur lors de la vérification de l'avis: " . $e->getMessage());
        return false;
    }
}

/**
 * Get reviews for a user
 * @param int $user_id
 * @return array
 */
function obtenirAvisUtilisateur(int $user_id): array
{
    try {
        global $db;
        $stmt = $db->prepare("
            SELECT r.*, 
                   u.first_name as reviewer_first_name, 
                   u.last_name as reviewer_last_name,
                   a.title as ad_title
            FROM reviews r
            JOIN users u ON r.reviewer_id = u.id
            JOIN advertisements a ON r.advertisement_id = a.id
            WHERE r.reviewed_user_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des avis: " . $e->getMessage());
        return [];
    }
}

/**
 * Get user's rating statistics
 * @param int $user_id
 * @return array ['average' => float, 'count' => int]
 */
function obtenirStatistiquesAvis(int $user_id): array
{
    try {
        global $db;
        $stmt = $db->prepare("
            SELECT 
                COALESCE(AVG(rating), 0) as average,
                COUNT(*) as count
            FROM reviews
            WHERE reviewed_user_id = ?
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des statistiques: " . $e->getMessage());
        return ['average' => 0, 'count' => 0];
    }
}
