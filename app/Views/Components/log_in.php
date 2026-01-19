<?php
// Ce component contient JUSTE le formulaire de login
// Définir base_url si pas déjà défini
if (!isset($base_url)) {
  $base_url = "/keep-my-pet/";
}
?>

<link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/Components/log_in.css">

<div class="form-container">
  <h1>Se connecter</h1>
  <p class="subtitle">Bienvenue sur KeepMyPet</p>

  <?php if (!empty($errors) && is_array($errors)): ?>
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
        required>
    </div>

    <div class="form-group">
      <label for="password">Mot de passe</label>
      <input
        type="password"
        id="password"
        name="password"
        placeholder="••••••••"
        required>
    </div>

    <button type="submit" class="btn-login">Se connecter</button>
  </form>

  <div class="form-links">
    <p>
      <a href="<?php echo $base_url; ?>/app/Views/forgotten_password.php">Mot de passe oublié ?</a>
    </p>
    <p>
      Pas encore inscrit ?
      <a href="<?php echo $base_url; ?>/app/Views/sign_up.php">S'inscrire</a>
    </p>
  </div>
</div>