<?php
// Define the base
$base_url = "/keep-my-pet/";  // For HTML links
$base_dir = __DIR__ . "/../../";  // For PHP includes

// Start session and require authentication
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (empty($_SESSION['user_id'])) {
  header('Location: ' . $base_url . '/app/Views/log_in.php');
  exit;
}

// DB and model
include $base_dir . 'app/Models/connection_db.php';
include $base_dir . 'app/Controller/AnimalsController.php';

$success = false;
$error = null;

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $result = AnimalsController::creerAnimal($db, $_POST);

  if ($result['success']) {
    $success = true;
    // Clear form
    $_POST = [];
    echo "<script>
            setTimeout(function() {
                window.location.href = '" . $base_url . "/app/Views/my_animals.php';
            }, 1500);
        </script>";
  } else {
    $error = $result['error'];
  }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KeepMyPet - Ajouter un animal</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/add_animal_modal.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
  <?php
  // HEADER
  include $base_dir . "/app/Views/Components/header.php";
  ?>

  <!-- Modal Overlay -->
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="modal-header">
        <h1>Ajouter un animal</h1>
        <a href="<?php echo $base_url; ?>/app/Views/my_animals.php" class="modal-close">
          <i class="fas fa-times"></i>
        </a>
      </div>

      <div class="modal-body">
        <?php if ($success): ?>
          <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Animal ajouté avec succès ! Redirection en cours...
          </div>
        <?php endif; ?>

        <?php if ($error): ?>
          <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
          </div>
        <?php endif; ?>

        <?php if (!$success): ?>
          <p class="modal-subtitle">Inscrivez votre compagnon à fourrure</p>

          <form method="POST" action="" class="modal-form">
            <div class="form-row">
              <div class="form-group">
                <label for="name">Nom de l'animal</label>
                <input
                  type="text"
                  id="name"
                  name="name"
                  value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                  placeholder="Ex: Rocky"
                  required
                  maxlength="50">
                <div class="help-text">Max 50 caractères</div>
              </div>

              <div class="form-group">
                <label for="race">Race/Type</label>
                <input
                  type="text"
                  id="race"
                  name="race"
                  value="<?php echo htmlspecialchars($_POST['race'] ?? ''); ?>"
                  placeholder="Ex: Golden Retriever"
                  required
                  maxlength="50">
                <div class="help-text">Max 50 caractères</div>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="gender">Genre</label>
                <select id="gender" name="gender" required>
                  <option value="">-- Sélectionnez --</option>
                  <option value="male" <?php echo (($_POST['gender'] ?? '') === 'male' ? 'selected' : ''); ?>>Mâle</option>
                  <option value="female" <?php echo (($_POST['gender'] ?? '') === 'female' ? 'selected' : ''); ?>>Femelle</option>
                </select>
              </div>

              <div class="form-group">
                <label for="birthdate">Date de naissance</label>
                <input
                  type="date"
                  id="birthdate"
                  name="birthdate"
                  value="<?php echo htmlspecialchars($_POST['birthdate'] ?? ''); ?>"
                  required>
              </div>
            </div>

            <div class="form-buttons">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter l'animal
              </button>
              <a href="<?php echo $base_url; ?>/app/Views/my_animals.php" class="btn btn-secondary">Annuler</a>
            </div>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <?php
  // FOOTER
  include $base_dir . '/app/Views/Components/footer.php';
  ?>

</body>

</html>