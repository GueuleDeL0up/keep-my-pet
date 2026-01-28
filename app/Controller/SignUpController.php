<?php

class SignUpController
{
  /**
   * Handle signup form submission and user creation.
   *
   * @param PDO $db
   * @param string $base_dir Path to project root with trailing slash
   * @param string $base_url Base URL (for redirects)
   * @return array List of error messages (empty if none)
   */
  public static function handle(PDO $db, string $base_dir, string $base_url): array
  {
    $errors = [];

    // Load user model
    include_once $base_dir . 'app/Models/requests.users.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return $errors;
    }

    $email = trim($_POST['email'] ?? '');
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $phone_number = trim($_POST['phone_number'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');

    // Validation
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = 'Adresse email invalide.';
    }
    if (empty($first_name)) {
      $errors[] = 'Prénom requis.';
    }
    if (empty($last_name)) {
      $errors[] = 'Nom requis.';
    }
    if (strlen($password) < 6) {
      $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
    }
    if ($password !== $password_confirm) {
      $errors[] = 'Les mots de passe ne correspondent pas.';
    }

    // Phone validation (optional but if provided check format for FR)
    if (!empty($phone_number) && !preg_match('/^(\+33|0)[1-9](?:[ .-]?\d{2}){4}$/', $phone_number)) {
      $errors[] = 'Numéro de téléphone invalide (format FR). Ex: 06 12 34 56 78';
    }

    // Postal code validation (if provided)
    if (!empty($postal_code) && !preg_match('/^\d{5}$/', $postal_code)) {
      $errors[] = 'Code postal invalide (5 chiffres).';
    }

    // Check existing email
    if (empty($errors)) {
      try {
        $existing = trouveParEmail($db, $email);
        if (count($existing) > 0) {
          $errors[] = 'Cet email est déjà utilisé.';
        }
      } catch (Exception $e) {
        $errors[] = 'Impossible de vérifier l\'email pour le moment.';
      }
    }

    // Create user
    if (empty($errors)) {
      $hash = password_hash($password, PASSWORD_DEFAULT);

      $user = [
        'email' => $email,
        'password' => $hash,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'phone_number' => $phone_number,
        'address' => $address,
        'postal_code' => $postal_code,
        'gender' => 'other',
        'is_admin' => 0,
        'note' => 0.00,
        'theme' => 'light',
        'language' => 'fr'
      ];

      try {
        $ok = ajouteUtilisateur($db, $user);
        if ($ok) {
          header('Location: ' . $base_url . '/app/Views/log_in.php');
          exit;
        } else {
          $errors[] = 'Erreur lors de l\'inscription, veuillez réessayer.';
        }
      } catch (Exception $e) {
        $errors[] = 'Erreur serveur lors de l\'inscription: ' . $e->getMessage();
      }
    }

    return $errors;
  }
}
