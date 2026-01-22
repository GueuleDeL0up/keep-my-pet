<?php

// Include database connection
require_once __DIR__ . '/connection_db.php';

/**
 * Get all animals for a user
 * @param int $user_id User ID
 * @return array List of animals
 */
function obtenirAnimauxUtilisateur($user_id)
{
  try {
    global $db;
    $query = "
            SELECT 
                id,
                user_id,
                name,
                race,
                gender,
                birthdate,
                note
            FROM animals
            WHERE user_id = ?
            ORDER BY name ASC
        ";

    $stmt = $db->prepare($query);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    error_log("Erreur lors de la récupération des animaux: " . $e->getMessage());
    return [];
  }
}

/**
 * Get a specific animal by ID
 * @param int $id Animal ID
 * @return array|null Animal data or null if not found
 */
function obtenirAnimalPar($id)
{
  try {
    global $db;
    $query = "
            SELECT 
                id,
                user_id,
                name,
                race,
                gender,
                birthdate,
                note
            FROM animals
            WHERE id = ?
        ";

    $stmt = $db->prepare($query);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    error_log("Erreur lors de la récupération de l'animal: " . $e->getMessage());
    return null;
  }
}

/**
 * Create a new animal
 * @param array $data Animal data
 * @return bool|int Animal ID if successful, false otherwise
 */
function creerAnimal($data)
{
  try {
    global $db;
    $query = "
            INSERT INTO animals (user_id, name, race, gender, birthdate, note)
            VALUES (?, ?, ?, ?, ?, ?)
        ";

    $stmt = $db->prepare($query);
    $result = $stmt->execute([
      $data['user_id'],
      $data['name'],
      $data['race'],
      $data['gender'],
      $data['birthdate'],
      0 // note by default
    ]);

    if ($result) {
      return $db->lastInsertId();
    }
    return false;
  } catch (PDOException $e) {
    error_log("Erreur lors de la création de l'animal: " . $e->getMessage());
    return false;
  }
}

/**
 * Update an animal
 * @param int $id Animal ID
 * @param array $data Animal data to update
 * @return bool
 */
function mettreAJourAnimal($id, $data)
{
  try {
    global $db;
    $query = "
            UPDATE animals 
            SET name = ?, race = ?, gender = ?, birthdate = ?
            WHERE id = ?
        ";

    $stmt = $db->prepare($query);
    return $stmt->execute([
      $data['name'],
      $data['race'],
      $data['gender'],
      $data['birthdate'],
      $id
    ]);
  } catch (PDOException $e) {
    error_log("Erreur lors de la mise à jour de l'animal: " . $e->getMessage());
    return false;
  }
}

/**
 * Delete an animal
 * @param int $id Animal ID
 * @return bool
 */
function supprimerAnimal($id)
{
  try {
    global $db;
    $query = "DELETE FROM animals WHERE id = ?";

    $stmt = $db->prepare($query);
    return $stmt->execute([$id]);
  } catch (PDOException $e) {
    error_log("Erreur lors de la suppression de l'animal: " . $e->getMessage());
    return false;
  }
}

/**
 * Add a rating/review to an animal
 * @param int $animal_id Animal ID
 * @param float $rating Rating (1-5)
 * @return bool
 */
function noterAnimal(int $animal_id, float $rating): bool
{
  try {
    global $db;
    
    // Validate animal exists
    $check = $db->prepare("SELECT note FROM animals WHERE id = ?");
    $check->execute([$animal_id]);
    $animal = $check->fetch(PDO::FETCH_ASSOC);
    if (!$animal) {
      return false;
    }
    
    // Update animal's note (average rating)
    $stmt = $db->prepare("
        UPDATE animals
        SET note = (SELECT AVG(rating) FROM reviews WHERE reviewed_animal_id = ?)
        WHERE id = ?
    ");
    return $stmt->execute([$animal_id, $animal_id]);
  } catch (PDOException $e) {
    error_log("Erreur lors de la notation de l'animal: " . $e->getMessage());
    return false;
  }
}
