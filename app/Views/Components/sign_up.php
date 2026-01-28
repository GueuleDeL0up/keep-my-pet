<?php
// Define the base
$base_url = "/keep-my-pet/";  // For HTML links
$base_dir = __DIR__ . "/../../../";  // For PHP includes
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/Components/sign_up.css">
</head>

<body>

  <div class="right">

    <?php if (!empty($errors) && is_array($errors)) : ?>
      <div class="errors">
        <?php foreach ($errors as $err) : ?>
          <p class="error"><?php echo htmlspecialchars($err); ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <label>MAIL</label>
      <input type="email" name="email" class="input" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>

      <div class="row">
        <div class="col">
          <label>NOM</label>
          <input type="text" name="last_name" class="input" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
        </div>

        <div class="col">
          <label>PRÉNOM</label>
          <input type="text" name="first_name" class="input" value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
        </div>
      </div>

      <label>TÉLÉPHONE</label>
      <input type="tel" name="phone_number" class="input" pattern="^(\+33|0)[1-9](?:[ .-]?\d{2}){4}$" placeholder="Ex: 06 12 34 56 78" value="<?php echo htmlspecialchars($_POST['phone_number'] ?? ''); ?>">

      <label>ADRESSE</label>
      <input type="text" name="address" class="input" placeholder="Numéro, rue" value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>">

      <label>CODE POSTAL</label>
      <input type="text" name="postal_code" class="input" pattern="^\d{5}$" placeholder="75001" value="<?php echo htmlspecialchars($_POST['postal_code'] ?? ''); ?>">

      <label>MOT DE PASSE</label>
      <input type="password" name="password" class="input" required>

      <label>CONFIRMER LE MOT DE PASSE</label>
      <input type="password" name="password_confirm" class="input" required>

      <button type="submit" class="btn">S'INSCRIRE</button>
    </form>

    <div class="links">
      <a href="<?php echo $base_url; ?>/app/Views/log_in.php">SE CONNECTER</a>
    </div>
  </div>
</body>

</html>