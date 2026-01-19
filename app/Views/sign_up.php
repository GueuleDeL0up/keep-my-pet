<?php
// Define the base
$base_url = "/keep-my-pet/";
$base_dir = __DIR__ . "/../../";

session_start();

$errors = [];
$success = false;

// Handle signup form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $first_name = trim($_POST['first_name'] ?? '');
  $last_name = trim($_POST['last_name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $address = trim($_POST['address'] ?? '');
  $password = $_POST['password'] ?? '';
  $password_confirm = $_POST['password_confirm'] ?? '';

  // Validation
  if (empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($address) || empty($password)) {
    $errors[] = 'Tous les champs sont requis.';
  }
  
  if (strlen($password) < 6) {
    $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
  }
  
  if ($password !== $password_confirm) {
    $errors[] = 'Les mots de passe ne correspondent pas.';
  }

  if (empty($errors)) {
    try {
      // Connect to database
      include $base_dir . 'app/Models/connection_db.php';

      // Check if email already exists - requête directe
      $check_query = "SELECT id FROM users WHERE email = ?";
      $check_stmt = $db->prepare($check_query);
      $check_stmt->execute([$email]);
      $existing = $check_stmt->fetch(PDO::FETCH_ASSOC);
      
      if ($existing) {
        $errors[] = 'Cet email est déjà utilisé.';
      } else {
        // Create user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO users (first_name, last_name, email, phone_number, address, password, note) VALUES (?, ?, ?, ?, ?, ?, 0)";
        $stmt = $db->prepare($query);
        $stmt->execute([$first_name, $last_name, $email, $phone, $address, $hashed_password]);
        
        $success = true;
        
        // Redirect to login after 2 seconds
        header('refresh:2; url=' . $base_url . 'app/Views/log_in.php');
      }
    } catch (Exception $e) {
      $errors[] = 'Erreur lors de l\'inscription: ' . $e->getMessage();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KeepMyPet - S'inscrire</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/log_in.css">
  <link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/Components/log_in.css">
</head>

<body>
  <div class="login-page">
    
    <!-- Logo -->
    <div class="logo-container">
      <img src="<?php echo $base_url; ?>public/assets/images/KeepMyPet_Logo.png" alt="Logo KeepMyPet" class="logo">
    </div>

    <!-- Form Container -->
    <div class="form-container">
      <h1>S'inscrire</h1>
      <p class="subtitle">Créez votre compte KeepMyPet</p>

      <?php if ($success): ?>
        <div class="success-box" style="background-color: #f0fdf4; border: 2px solid #86efac; border-radius: 10px; padding: 15px; margin-bottom: 25px;">
          <p style="color: #15803d; font-size: 14px;">Inscription réussie ! Redirection vers la connexion...</p>
        </div>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <div class="errors-box">
          <?php foreach ($errors as $err): ?>
            <p class="error-msg"><?php echo htmlspecialchars($err); ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if (!$success): ?>
        <form method="POST" action="" class="login-form">
          <div class="form-group">
            <label for="first_name">Prénom</label>
            <input 
              type="text" 
              id="first_name"
              name="first_name" 
              placeholder="Votre prénom"
              value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
              required
            >
          </div>

          <div class="form-group">
            <label for="last_name">Nom</label>
            <input 
              type="text" 
              id="last_name"
              name="last_name" 
              placeholder="Votre nom"
              value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
              required
            >
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <input 
              type="email" 
              id="email"
              name="email" 
              placeholder="votre@email.com"
              value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
              required
            >
          </div>

          <div class="form-group">
            <label for="phone">Numéro de téléphone</label>
            <input 
              type="tel" 
              id="phone"
              name="phone" 
              placeholder="+33 6 12 34 56 78"
              value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
              required
            >
          </div>

          <div class="form-group">
            <label for="address">Adresse</label>
            <input 
              type="text" 
              id="address"
              name="address" 
              placeholder="123 Rue de la Paix, 75000 Paris"
              value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>"
              required
            >
          </div>

          <div class="form-group">
            <label for="password">Mot de passe</label>
            <input 
              type="password" 
              id="password"
              name="password" 
              placeholder="Minimum 6 caractères"
              required
            >
          </div>

          <div class="form-group">
            <label for="password_confirm">Confirmer le mot de passe</label>
            <input 
              type="password" 
              id="password_confirm"
              name="password_confirm" 
              placeholder="Confirmez votre mot de passe"
              required
            >
          </div>

          <button type="submit" class="btn-login">S'inscrire</button>
        </form>
      <?php endif; ?>

      <div class="form-links">
        <p>
          Vous avez déjà un compte ? 
          <a href="<?php echo $base_url; ?>app/Views/log_in.php">Se connecter</a>
        </p>
      </div>
    </div>

    <!-- Background shapes -->
    <div class="shapes-container">
      <div class="shape circle" id="c1"></div>
      <div class="shape circle" id="c2"></div>
      <div class="shape circle" id="c3"></div>
      <div class="shape circle" id="c4"></div>
      <div class="shape circle" id="c5"></div>
      <div class="shape circle" id="c6"></div>
      <div class="shape circle" id="c7"></div>
      <div class="shape circle" id="c8"></div>
      <div class="shape circle" id="c9"></div>
      <div class="shape circle" id="c10"></div>
      <div class="shape triangle" id="t1"></div>
      <div class="shape triangle" id="t2"></div>
      <div class="shape triangle" id="t3"></div>
      <div class="shape square" id="s1"></div>
      <div class="shape square" id="s2"></div>
    </div>
  </div>
</body>

</html>
