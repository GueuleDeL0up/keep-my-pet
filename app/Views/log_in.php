<?php
// Define the base
$base_url = "/keep-my-pet/";
$base_dir = __DIR__ . "/../../";

session_start();

$errors = [];

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if (empty($email) || empty($password)) {
    $errors[] = 'Email et mot de passe requis.';
  } else {
    try {
      // Connect to database
      include $base_dir . 'app/Models/connection_db.php';
      include $base_dir . 'app/Models/requests.users.php';

      // Find user by email
      $users = trouveParEmail($db, $email);
      
      if (empty($users)) {
        $errors[] = 'Identifiants invalides.';
      } else {
        $user = $users[0];
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
          $errors[] = 'Identifiants invalides.';
        } else {
          // Login success
          $_SESSION['user_id'] = $user['id'];
          $_SESSION['user_email'] = $user['email'];
          $_SESSION['user_first_name'] = $user['first_name'];
          $_SESSION['user_last_name'] = $user['last_name'];
          
          // Redirect to home
          header('Location: ' . $base_url . 'app/Views/home.php');
          exit;
        }
      }
    } catch (Exception $e) {
      $errors[] = 'Erreur serveur lors de la connexion.';
    }
  }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KeepMyPet - Se connecter</title>
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
      <h1>Se connecter</h1>
      <p class="subtitle">Bienvenue sur KeepMyPet</p>

      <?php if (!empty($errors)): ?>
        <div class="errors-box">
          <?php foreach ($errors as $err): ?>
            <p class="error-msg"><?php echo htmlspecialchars($err); ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="" class="login-form">
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
          <label for="password">Mot de passe</label>
          <input 
            type="password" 
            id="password"
            name="password" 
            placeholder="••••••••"
            required
          >
        </div>

        <button type="submit" class="btn-login">Se connecter</button>
      </form>

      <div class="form-links">
        <p>
          <a href="<?php echo $base_url; ?>app/Views/forgotten_password.php">Mot de passe oublié ?</a>
        </p>
        <p>
          Pas encore inscrit ? 
          <a href="<?php echo $base_url; ?>app/Views/sign_up.php">S'inscrire</a>
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
