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

// DB and models
include $base_dir . 'app/Models/connection_db.php';
include $base_dir . 'app/Models/requests.animals.php';

// Get animal ID from URL
$animal_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($animal_id === 0) {
  header('Location: ' . $base_url . '/app/Views/my_animals.php');
  exit;
}

// Get animal data
$animal = obtenirAnimalPar($animal_id);

// Verify animal belongs to user
if (!$animal || $animal['user_id'] != $_SESSION['user_id']) {
  header('Location: ' . $base_url . '/app/Views/my_animals.php');
  exit;
}

$error = null;
$success = false;

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Validate required fields
  $required_fields = ['name', 'race', 'gender', 'birthdate'];
  foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
      $error = 'Le champ ' . htmlspecialchars($field) . ' est obligatoire.';
      break;
    }
  }

  if (!$error) {
    // Sanitize data
    $data = [
      'name' => htmlspecialchars(trim($_POST['name'])),
      'race' => htmlspecialchars(trim($_POST['race'])),
      'gender' => htmlspecialchars($_POST['gender']),
      'birthdate' => htmlspecialchars($_POST['birthdate'])
    ];

    // Validate
    if (!strtotime($data['birthdate'])) {
      $error = 'La date de naissance est invalide.';
    } elseif (!in_array($data['gender'], ['male', 'female'])) {
      $error = 'Le genre doit être "mâle" ou "femelle".';
    } elseif (strlen($data['name']) > 50) {
      $error = 'Le nom de l\'animal ne doit pas dépasser 50 caractères.';
    } elseif (strlen($data['race']) > 50) {
      $error = 'La race ne doit pas dépasser 50 caractères.';
    } else {
      if (mettreAJourAnimal($animal_id, $data)) {
        $success = true;
        $animal = array_merge($animal, $data);
        echo "<script>
                    setTimeout(function() {
                        window.location.href = '" . $base_url . "/app/Views/my_animals.php';
                    }, 2000);
                </script>";
      } else {
        $error = 'Erreur lors de la mise à jour de l\'animal.';
      }
    }
  }
}

// HEADER
include $base_dir . "/app/Views/Components/header.php";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KeepMyPet - Modifier un animal</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/add_animal.css">
</head>

<body>

  <div class="container">
    <h1>Modifier l'animal</h1>
    <p>Mettez à jour les informations de <?php echo htmlspecialchars($animal['name']); ?></p>

    <?php if ($success): ?>
      <div class="alert alert-success">
        ✓ Animal modifié avec succès ! Redirection en cours...
      </div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="alert alert-error">
        ✗ <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="form-row">
        <div class="form-group">
          <label for="name">Nom de l'animal</label>
          <input
            type="text"
            id="name"
            name="name"
            value="<?php echo htmlspecialchars($animal['name']); ?>"
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
            value="<?php echo htmlspecialchars($animal['race']); ?>"
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
            <option value="male" <?php echo ($animal['gender'] === 'male' ? 'selected' : ''); ?>>Mâle</option>
            <option value="female" <?php echo ($animal['gender'] === 'female' ? 'selected' : ''); ?>>Femelle</option>
          </select>
        </div>

        <div class="form-group">
          <label for="birthdate">Date de naissance</label>
          <input
            type="date"
            id="birthdate"
            name="birthdate"
            value="<?php echo htmlspecialchars($animal['birthdate']); ?>"
            required>
        </div>
      </div>

      <div class="form-buttons">
        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        <a href="<?php echo $base_url; ?>/app/Views/my_animals.php" class="btn btn-secondary">Annuler</a>
      </div>
    </form>
  </div>

  <?php
  // FOOTER
  include $base_dir . '/app/Views/Components/footer.php';
  ?>

</body>

</html>