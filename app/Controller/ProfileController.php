<?php

class ProfileController
{
  /**
   * Handle profile settings page: show and process updates
   */
  public static function handle(PDO $db, string $base_dir, string $base_url): array
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    $result = ['errors' => [], 'success' => false, 'user' => null];

    if (empty($_SESSION['user_id'])) {
      header('Location: ' . $base_url . '/app/Views/log_in.php');
      exit;
    }

    $userId = (int)$_SESSION['user_id'];

    // Load current user data
    include_once $base_dir . 'app/Models/requests.users.php';
    include_once $base_dir . 'app/Models/requests.bookings.php';
    $existing = trouveParId($db, $userId);
    if (count($existing) === 0) {
      // user not found
      $result['errors'][] = 'Utilisateur introuvable.';
      return $result;
    }
    $user = $existing[0];
    $result['user'] = $user;

    // History of finished bookings (owner or sitter)
    $result['history'] = obtenirGardesTermineesUtilisateur($userId);

    // POST handling
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Simple CSRF check if present
      if (!empty($_POST['csrf_token']) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        // ok
      } else {
        // Not fatal, but we require token
        $result['errors'][] = 'Jeton CSRF invalide. Veuillez réessayer.';
        return $result;
      }

      // If this is a password change request, handle separately
      if (!empty($_POST['action']) && $_POST['action'] === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['new_password_confirm'] ?? '';

        if (empty($current) || empty($new) || empty($confirm)) {
          $result['errors'][] = 'Tous les champs du mot de passe sont requis.';
          return $result;
        }

        if ($new !== $confirm) {
          $result['errors'][] = 'La confirmation du mot de passe ne correspond pas.';
          return $result;
        }

        if (strlen($new) < 6) {
          $result['errors'][] = 'Le nouveau mot de passe doit contenir au moins 6 caractères.';
          return $result;
        }

        // Verify current password
        if (!password_verify($current, $user['password'])) {
          $result['errors'][] = 'Mot de passe actuel incorrect.';
          return $result;
        }

        // Hash and update
        $hash = password_hash($new, PASSWORD_DEFAULT);
        include_once $base_dir . 'app/Models/requests.users.php';
        $ok = updatePassword($db, $userId, $hash);
        if ($ok) {
          // Redirect back with saved flag
          header('Location: ' . $base_url . '/app/Views/profile_settings.php?saved=1');
          exit;
        } else {
          $result['errors'][] = 'Impossible de mettre à jour le mot de passe.';
          return $result;
        }
      }

      // Collect and sanitize (account update)
      $email = trim($_POST['email'] ?? '');
      $first_name = trim($_POST['first_name'] ?? '');
      $last_name = trim($_POST['last_name'] ?? '');
      $phone_number = trim($_POST['phone_number'] ?? '');
      $address = trim($_POST['address'] ?? '');
      $postal_code = trim($_POST['postal_code'] ?? '');

      // Validation
      if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $result['errors'][] = 'Email invalide.';
      }

      // If email changed, ensure uniqueness
      if (count(trouveParEmail($db, $email)) > 0) {
        $found = trouveParEmail($db, $email);
        $found = $found[0];
        if ((int)$found['id'] !== $userId) {
          $result['errors'][] = 'Cet email est déjà utilisé.';
        }
      }

      // postal code FR check, allow empty
      if ($postal_code !== '' && !preg_match('/^\d{5}$/', $postal_code)) {
        $result['errors'][] = 'Le code postal doit contenir 5 chiffres.';
      }

      // phone validation (FR-ish)
      if ($phone_number !== '' && !preg_match('/^(\+33|0)[1-9](?:[ .-]?\d{2}){4}$/', $phone_number)) {
        $result['errors'][] = 'Numéro de téléphone invalide.';
      }

      if (empty($result['errors'])) {
        // Prepare fields to update
        $fields = [
          'email' => $email,
          'first_name' => $first_name,
          'last_name' => $last_name,
          'phone_number' => $phone_number,
          'address' => $address,
          'postal_code' => $postal_code,
        ];

        $ok = updateUtilisateur($db, $userId, $fields);
        if ($ok) {
          // update session (if showing name/email elsewhere)
          $_SESSION['user_first_name'] = $first_name;
          $_SESSION['user_last_name'] = $last_name;
          $_SESSION['user_email'] = $email;

          // Redirect back with saved flag
          header('Location: ' . $base_url . '/app/Views/profile_settings.php?saved=1');
          exit;
        } else {
          $result['errors'][] = 'Impossible de sauvegarder les modifications.';
        }
      }
    }

    return $result;
  }
}
