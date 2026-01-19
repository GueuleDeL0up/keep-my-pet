<?php

class AnimalsController {
    /**
     * Create a new animal
     * @param PDO $db Database connection
     * @param array $data Animal data from POST
     * @return array Result with success/error keys
     */
    public static function creerAnimal($db, $data) {
        $result = ['success' => false, 'error' => null, 'animal_id' => null];

        // Check session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user_id'])) {
            $result['error'] = 'Vous devez être connecté pour ajouter un animal.';
            return $result;
        }

        // Validate required fields
        $required_fields = ['name', 'race', 'gender', 'birthdate'];
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                $result['error'] = 'Le champ ' . htmlspecialchars($field) . ' est obligatoire.';
                return $result;
            }
        }

        // Sanitize data
        $animal_data = [
            'user_id' => (int)$_SESSION['user_id'],
            'name' => htmlspecialchars(trim($data['name'])),
            'race' => htmlspecialchars(trim($data['race'])),
            'gender' => htmlspecialchars($data['gender']),
            'birthdate' => htmlspecialchars($data['birthdate'])
        ];

        // Validate birthdate format
        if (!strtotime($animal_data['birthdate'])) {
            $result['error'] = 'La date de naissance est invalide.';
            return $result;
        }

        // Validate gender
        if (!in_array($animal_data['gender'], ['male', 'female'])) {
            $result['error'] = 'Le genre doit être "mâle" ou "femelle".';
            return $result;
        }

        // Check if name is too long
        if (strlen($animal_data['name']) > 50) {
            $result['error'] = 'Le nom de l\'animal ne doit pas dépasser 50 caractères.';
            return $result;
        }

        // Check if race is too long
        if (strlen($animal_data['race']) > 50) {
            $result['error'] = 'La race ne doit pas dépasser 50 caractères.';
            return $result;
        }

        // Call model function to create animal
        include_once __DIR__ . '/../Models/requests.animals.php';
        $animal_id = creerAnimal($animal_data);

        if ($animal_id) {
            $result['success'] = true;
            $result['animal_id'] = $animal_id;
        } else {
            $result['error'] = 'Erreur lors de la création de l\'animal. Veuillez réessayer.';
        }

        return $result;
    }

    /**
     * Get all animals for current user
     * @param PDO $db Database connection
     * @return array Array of animals
     */
    public static function obtenirMesAnimaux($db) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user_id'])) {
            return [];
        }

        include_once __DIR__ . '/../Models/requests.animals.php';
        return obtenirAnimauxUtilisateur($_SESSION['user_id']);
    }
}
