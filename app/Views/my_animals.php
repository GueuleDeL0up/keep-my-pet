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
include $base_dir . 'app/Controller/AnimalsController.php';
include $base_dir . 'app/Models/requests.animals.php';

// Get user's animals
$animals = AnimalsController::obtenirMesAnimaux($db);

// Handle delete if requested
$delete_message = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_animal_id'])) {
  $animal_id = (int)$_POST['delete_animal_id'];

  // Verify the animal belongs to the user
  $animal = obtenirAnimalPar($animal_id);
  if ($animal && $animal['user_id'] == $_SESSION['user_id']) {
    if (supprimerAnimal($animal_id)) {
      $delete_message = ['type' => 'success', 'text' => 'Animal supprimé avec succès'];
      // Refresh animals list
      $animals = AnimalsController::obtenirMesAnimaux($db);
    } else {
      $delete_message = ['type' => 'error', 'text' => 'Erreur lors de la suppression'];
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
  <title>KeepMyPet - Mes animaux</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/my_animals.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

  <div class="container">
    <div class="header-section">
      <h1>Mes animaux</h1>
      <a href="<?php echo $base_url; ?>/app/Views/add_animal.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Ajouter un animal
      </a>
    </div>

    <?php if ($delete_message): ?>
      <div class="alert alert-<?php echo $delete_message['type']; ?>">
        <?php echo htmlspecialchars($delete_message['text']); ?>
      </div>
    <?php endif; ?>

    <?php if (empty($animals)): ?>
      <div class="empty-state">
        <div class="empty-icon">🐾</div>
        <h2>Aucun animal pour le moment</h2>
        <p>Commencez par ajouter votre premier animal pour pouvoir créer des annonces.</p>
        <a href="<?php echo $base_url; ?>/app/Views/add_animal.php" class="btn btn-primary">
          Ajouter mon premier animal
        </a>
      </div>
    <?php else: ?>
      <div class="animals-grid">
        <?php foreach ($animals as $animal): ?>
          <div class="animal-card">
            <div class="animal-header">
              <h3><?php echo htmlspecialchars($animal['name']); ?></h3>
              <div class="animal-actions">
                <a href="<?php echo $base_url; ?>/app/Views/edit_animal.php?id=<?php echo (int)$animal['id']; ?>" class="btn-action edit" title="Modifier">
                  <i class="fas fa-edit"></i>
                </a>
                <form method="POST" action="" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet animal ?');">
                  <input type="hidden" name="delete_animal_id" value="<?php echo (int)$animal['id']; ?>">
                  <button type="submit" class="btn-action delete" title="Supprimer">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
              </div>
            </div>

            <div class="animal-info">
              <div class="info-row">
                <span class="info-label">Race :</span>
                <span class="info-value"><?php echo htmlspecialchars($animal['race']); ?></span>
              </div>
              <div class="info-row">
                <span class="info-label">Genre :</span>
                <span class="info-value">
                  <?php echo $animal['gender'] === 'male' ? '♂ Mâle' : '♀ Femelle'; ?>
                </span>
              </div>
              <div class="info-row">
                <span class="info-label">Date de naissance :</span>
                <span class="info-value">
                  <?php
                  $date = new DateTime($animal['birthdate']);
                  echo $date->format('d/m/Y');
                  ?>
                </span>
              </div>
              <div class="info-row">
                <span class="info-label">Âge :</span>
                <span class="info-value">
                  <?php
                  $birthdate = new DateTime($animal['birthdate']);
                  $today = new DateTime('today');
                  $age = $birthdate->diff($today)->y;
                  echo $age . ' an' . ($age > 1 ? 's' : '');
                  ?>
                </span>
              </div>
            </div>

            <div class="animal-footer">
              <a href="<?php echo $base_url; ?>/app/Views/create_advertisement.php?animal_id=<?php echo (int)$animal['id']; ?>" class="btn btn-secondary">
                Créer une annonce
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <?php
  // FOOTER
  include $base_dir . '/app/Views/Components/footer.php';
  ?>

</body>

</html>