<?php

// Include database connection
require_once __DIR__ . '/connection_db.php';

/**
 * Create a booking request (when a sitter accepts an advertisement)
 * @param int $ad_id Advertisement ID
 * @param int $sitter_id User ID of the sitter
 * @param int $owner_id User ID of the owner
 * @return bool
 */
function creerDemande(int $ad_id, int $sitter_id, int $owner_id): bool
{
    try {
        global $db;
        
        // Check if booking already exists
        $check = $db->prepare("SELECT id FROM bookings WHERE advertisement_id = ? AND sitter_id = ?");
        $check->execute([$ad_id, $sitter_id]);
        if ($check->fetch()) {
            return false; // Already exists
        }
        
        $stmt = $db->prepare("
            INSERT INTO bookings (advertisement_id, sitter_id, owner_id, status) 
            VALUES (?, ?, ?, 'pending')
        ");
        return $stmt->execute([$ad_id, $sitter_id, $owner_id]);
    } catch (PDOException $e) {
        error_log("Erreur lors de la création de la demande: " . $e->getMessage());
        return false;
    }
}

/**
 * Get booking requests received by owner
 * @param int $owner_id Owner ID
 * @return array List of bookings
 */
function obtenirDemandesRecues(int $owner_id): array
{
    try {
        global $db;
        $stmt = $db->prepare("
            SELECT 
                b.*,
                a.title as ad_title,
                a.start_date,
                a.start_hour,
                a.end_date,
                a.end_hour,
                a.animal_id,
                an.name as animal_name,
                u.first_name as sitter_first_name,
                u.last_name as sitter_last_name,
                u.email as sitter_email,
                u.phone_number as sitter_phone
            FROM bookings b
            JOIN advertisements a ON b.advertisement_id = a.id
            JOIN animals an ON a.animal_id = an.id
            JOIN users u ON b.sitter_id = u.id
            WHERE b.owner_id = ?
            ORDER BY b.created_at DESC
        ");
        $stmt->execute([$owner_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des demandes: " . $e->getMessage());
        return [];
    }
}

/**
 * Get bookings created by sitter (to show on their profile)
 * @param int $sitter_id Sitter ID
 * @return array List of bookings
 */
function obtenirGardesParGardien(int $sitter_id): array
{
    try {
        global $db;
        $stmt = $db->prepare("
            SELECT 
                b.*,
                a.title as ad_title,
                a.start_date,
                a.start_hour,
                a.end_date,
                a.end_hour,
                a.animal_id,
                an.name as animal_name,
                u.first_name as owner_first_name,
                u.last_name as owner_last_name
            FROM bookings b
            JOIN advertisements a ON b.advertisement_id = a.id
            JOIN animals an ON a.animal_id = an.id
            JOIN users u ON b.owner_id = u.id
            WHERE b.sitter_id = ? AND b.status = 'accepted'
            ORDER BY a.start_date DESC
        ");
        $stmt->execute([$sitter_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des gardes: " . $e->getMessage());
        return [];
    }
}

/**
 * Get finished bookings (as owner or sitter) for history
 * @param int $user_id User ID
 * @return array
 */
function obtenirGardesTermineesUtilisateur(int $user_id): array
{
    try {
        global $db;
        $stmt = $db->prepare("
            SELECT 
                b.id AS booking_id,
                b.advertisement_id,
                b.status,
                b.sitter_id,
                b.owner_id,
                a.title AS ad_title,
                a.start_date,
                a.start_hour,
                a.end_date,
                a.end_hour,
                a.animal_id,
                an.name AS animal_name,
                owner.first_name AS owner_first_name,
                owner.last_name AS owner_last_name,
                sitter.first_name AS sitter_first_name,
                sitter.last_name AS sitter_last_name,
                CASE WHEN b.sitter_id = :user_id THEN 'sitter' ELSE 'owner' END AS role
            FROM bookings b
            JOIN advertisements a ON b.advertisement_id = a.id
            JOIN animals an ON a.animal_id = an.id
            JOIN users owner ON b.owner_id = owner.id
            JOIN users sitter ON b.sitter_id = sitter.id
            WHERE (b.sitter_id = :user_id OR b.owner_id = :user_id)
              AND b.status = 'accepted'
              AND (
                a.end_date < CURDATE()
                OR (a.end_date = CURDATE() AND COALESCE(a.end_hour, '23:59:59') <= CURTIME())
              )
            ORDER BY a.end_date DESC, a.end_hour DESC
        ");
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération de l'historique des gardes: " . $e->getMessage());
        return [];
    }
}

/**
 * Accept a booking request
 * @param int $booking_id Booking ID
 * @return bool
 */
function accepterDemande(int $booking_id): bool
{
    try {
        global $db;
        $stmt = $db->prepare("
            UPDATE bookings 
            SET status = 'accepted', responded_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$booking_id]);
    } catch (PDOException $e) {
        error_log("Erreur lors de l'acceptation de la demande: " . $e->getMessage());
        return false;
    }
}

/**
 * Reject a booking request
 * @param int $booking_id Booking ID
 * @return bool
 */
function refuserDemande(int $booking_id): bool
{
    try {
        global $db;
        $stmt = $db->prepare("
            UPDATE bookings 
            SET status = 'rejected', responded_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$booking_id]);
    } catch (PDOException $e) {
        error_log("Erreur lors du refus de la demande: " . $e->getMessage());
        return false;
    }
}

/**
 * Get a specific booking
 * @param int $booking_id Booking ID
 * @return array|null Booking data
 */
function obtenirDemande(int $booking_id): ?array
{
    try {
        global $db;
        $stmt = $db->prepare("
            SELECT b.*, a.id as ad_id
            FROM bookings b
            JOIN advertisements a ON b.advertisement_id = a.id
            WHERE b.id = ?
        ");
        $stmt->execute([$booking_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération de la demande: " . $e->getMessage());
        return null;
    }
}

/**
 * Get the accepted sitter for an advertisement
 * @param int $ad_id Advertisement ID
 * @return array|null Sitter info or null if no accepted booking
 */
function obtenirGardienAccepte(int $ad_id): ?array
{
    try {
        global $db;
        $stmt = $db->prepare("
            SELECT 
                b.id as booking_id,
                b.sitter_id,
                u.first_name,
                u.last_name,
                u.email
            FROM bookings b
            JOIN users u ON b.sitter_id = u.id
            WHERE b.advertisement_id = ? AND b.status = 'accepted'
            LIMIT 1
        ");
        $stmt->execute([$ad_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération du gardien accepté: " . $e->getMessage());
        return null;
    }
}

/**
 */
function aDejaDemandeAnnonce(int $ad_id, int $sitter_id): bool
{
    try {
        global $db;
        $stmt = $db->prepare("
            SELECT 1 FROM bookings 
            WHERE advertisement_id = ? AND sitter_id = ? LIMIT 1
        ");
        $stmt->execute([$ad_id, $sitter_id]);
        return (bool)$stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Erreur lors de la vérification de la demande: " . $e->getMessage());
        return false;
    }
}
