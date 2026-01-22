<?php

// Include the model
require_once __DIR__ . '/../Models/requests.advertisements.php';

class AdvertisementsController
{

  /**
   * Display all advertisements
   * Handles search filters and pagination
   */
  public static function afficherAnnonces()
  {
    try {
      // Start session if not started
      if (session_status() === PHP_SESSION_NONE) {
        session_start();
      }

      // Get search filters from GET parameters
      $filters = [
        'search' => $_GET['search'] ?? '',
        'type' => $_GET['type'] ?? '',
        'city' => $_GET['city'] ?? '',
        'min_price' => $_GET['min_price'] ?? '',
        'max_price' => $_GET['max_price'] ?? ''
      ];

      // Check if filters are applied
      $hasFilters = !empty($filters['search']) ||
        !empty($filters['type']) ||
        !empty($filters['city']) ||
        !empty($filters['min_price']) ||
        !empty($filters['max_price']);

      // Get current user ID if logged in
      $current_user_id = $_SESSION['user_id'] ?? null;

      // Get advertisements
      if ($hasFilters) {
        $annonces = rechercherAnnonces($filters);
      } else {
        $annonces = obtenirToutesAnnonces();
      }

      // Filter out user's own advertisements
      if ($current_user_id) {
        $annonces = array_filter($annonces, function($ad) use ($current_user_id) {
          return $ad['user_id'] != $current_user_id;
        });
      }

      return [
        'annonces' => $annonces,
        'filters' => $filters,
        'count' => count($annonces)
      ];
    } catch (Exception $e) {
      error_log("Erreur dans AdvertisementsController::afficherAnnonces: " . $e->getMessage());
      return [
        'annonces' => [],
        'filters' => [],
        'count' => 0,
        'error' => 'Erreur lors du chargement des annonces'
      ];
    }
  }

  /**
   * Display a single advertisement detail
   * @param int $id Advertisement ID
   */
  public static function afficherDetailAnnonce($id)
  {
    try {
      $id = intval($id);
      $annonce = obtenirAnnoncePar($id);

      if (!$annonce) {
        return [
          'annonce' => null,
          'error' => 'Annonce non trouvée'
        ];
      }

      return [
        'annonce' => $annonce
      ];
    } catch (Exception $e) {
      error_log("Erreur dans AdvertisementsController::afficherDetailAnnonce: " . $e->getMessage());
      return [
        'annonce' => null,
        'error' => 'Erreur lors du chargement de l\'annonce'
      ];
    }
  }

  /**
   * Get advertisements by current user (requires session)
   */
  public static function afficherMesAnnonces()
  {
    try {
      if (!isset($_SESSION['user_id'])) {
        return [
          'annonces' => [],
          'error' => 'Veuillez vous connecter'
        ];
      }

      $annonces = obtenirAnnoncesParUtilisateur($_SESSION['user_id']);

      return [
        'annonces' => $annonces,
        'count' => count($annonces)
      ];
    } catch (Exception $e) {
      error_log("Erreur dans AdvertisementsController::afficherMesAnnonces: " . $e->getMessage());
      return [
        'annonces' => [],
        'error' => 'Erreur lors du chargement de vos annonces'
      ];
    }
  }

  /**
   * Create a new advertisement (POST request)
   */
  public static function creerAnnonce()
  {
    try {
      if (!isset($_SESSION['user_id'])) {
        return [
          'success' => false,
          'error' => 'Veuillez vous connecter'
        ];
      }

      if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return [
          'success' => false,
          'error' => 'Méthode non autorisée'
        ];
      }

      // Validate required fields
      $required = ['title', 'description', 'city', 'animal_id', 'start_date', 'start_hour', 'end_date', 'end_hour', 'price', 'type'];
      foreach ($required as $field) {
        if (empty($_POST[$field])) {
          return [
            'success' => false,
            'error' => "Le champ {$field} est requis"
          ];
        }
      }

      // Prepare data
      $data = [
        'user_id' => $_SESSION['user_id'],
        'title' => htmlspecialchars($_POST['title']),
        'description' => htmlspecialchars($_POST['description']),
        'city' => htmlspecialchars($_POST['city']),
        'animal_id' => intval($_POST['animal_id']),
        'start_date' => $_POST['start_date'],
        'start_hour' => $_POST['start_hour'],
        'end_date' => $_POST['end_date'],
        'end_hour' => $_POST['end_hour'],
        'price' => floatval($_POST['price']),
        'type' => $_POST['type']
      ];

      // Create advertisement
      $annonce_id = creerAnnonce($data);

      if ($annonce_id) {
        return [
          'success' => true,
          'message' => 'Annonce créée avec succès',
          'annonce_id' => $annonce_id
        ];
      } else {
        return [
          'success' => false,
          'error' => 'Erreur lors de la création de l\'annonce'
        ];
      }
    } catch (Exception $e) {
      error_log("Erreur dans AdvertisementsController::creerAnnonce: " . $e->getMessage());
      return [
        'success' => false,
        'error' => 'Erreur serveur: ' . $e->getMessage()
      ];
    }
  }

  /**
   * Update an advertisement (POST request)
   */
  public static function mettreAJourAnnonce($id)
  {
    try {
      if (!isset($_SESSION['user_id'])) {
        return [
          'success' => false,
          'error' => 'Veuillez vous connecter'
        ];
      }

      if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return [
          'success' => false,
          'error' => 'Méthode non autorisée'
        ];
      }

      // Validate required fields
      $required = ['title', 'description', 'city', 'start_date', 'start_hour', 'end_date', 'end_hour', 'price', 'type'];
      foreach ($required as $field) {
        if (empty($_POST[$field])) {
          return [
            'success' => false,
            'error' => "Le champ {$field} est requis"
          ];
        }
      }

      // Prepare data
      $data = [
        'user_id' => $_SESSION['user_id'],
        'title' => htmlspecialchars($_POST['title']),
        'description' => htmlspecialchars($_POST['description']),
        'city' => htmlspecialchars($_POST['city']),
        'start_date' => $_POST['start_date'],
        'start_hour' => $_POST['start_hour'],
        'end_date' => $_POST['end_date'],
        'end_hour' => $_POST['end_hour'],
        'price' => floatval($_POST['price']),
        'type' => $_POST['type']
      ];

      // Update advertisement
      $result = mettreAJourAnnonce($id, $data);

      if ($result) {
        return [
          'success' => true,
          'message' => 'Annonce mise à jour avec succès'
        ];
      } else {
        return [
          'success' => false,
          'error' => 'Erreur lors de la mise à jour de l\'annonce'
        ];
      }
    } catch (Exception $e) {
      error_log("Erreur dans AdvertisementsController::mettreAJourAnnonce: " . $e->getMessage());
      return [
        'success' => false,
        'error' => 'Erreur serveur: ' . $e->getMessage()
      ];
    }
  }

  /**
   * Delete an advertisement (POST request)
   */
  public static function supprimerAnnonce($id)
  {
    try {
      if (!isset($_SESSION['user_id'])) {
        return [
          'success' => false,
          'error' => 'Veuillez vous connecter'
        ];
      }

      if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return [
          'success' => false,
          'error' => 'Méthode non autorisée'
        ];
      }

      // Delete advertisement
      $result = supprimerAnnonce($id, $_SESSION['user_id']);

      if ($result) {
        return [
          'success' => true,
          'message' => 'Annonce supprimée avec succès'
        ];
      } else {
        return [
          'success' => false,
          'error' => 'Erreur lors de la suppression de l\'annonce'
        ];
      }
    } catch (Exception $e) {
      error_log("Erreur dans AdvertisementsController::supprimerAnnonce: " . $e->getMessage());
      return [
        'success' => false,
        'error' => 'Erreur serveur: ' . $e->getMessage()
      ];
    }
  }
}
