<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: /keep-my-pet/app/Views/log_in.php');
  exit;
}

// Define the base
$base_url = "/keep-my-pet/";
$base_dir = __DIR__ . "/../../";

// Include Controller
require_once $base_dir . "app/Controller/AdvertisementsController.php";
require_once $base_dir . "app/Models/requests.users.php";

// Handle form submission
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $result = AdvertisementsController::creerAnnonce();

  if ($result['success']) {
    $success = $result['message'];
    // Redirect after 2 seconds
    header('Refresh: 2; url=' . $base_url . 'app/Views/advertisements.php');
  } else {
    $error = $result['error'] ?? 'Erreur lors de la création de l\'annonce';
  }
}

// Get database connection
require_once $base_dir . "app/Models/connection_db.php";
require_once $base_dir . "app/Models/requests.animals.php";

// Get user's animals
$animals = obtenirAnimauxUtilisateur($_SESSION['user_id']);

// Check if animal_id is passed in URL (pre-select it)
$pre_selected_animal_id = isset($_GET['animal_id']) ? (int)$_GET['animal_id'] : null;

// Include Header
include $base_dir . "/app/Views/Components/header.php";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KeepMyPet - Déposer une annonce</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/create_advertisement.css">
</head>

<body>
  <main class="create-ad-container">
    <div class="create-ad-wrapper">
      <h1>Déposer une annonce</h1>
      <p class="subtitle">Proposez vos services de garde ou de promenade pour animaux</p>

      <?php if ($error): ?>
        <div class="alert alert-error">
          <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="alert alert-success">
          <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
          <p>Redirection vers les annonces...</p>
        </div>
      <?php else: ?>

        <form method="POST" class="create-ad-form">
          <!-- Title -->
          <div class="form-group">
            <label for="title">Titre de l'annonce *</label>
            <input type="text" id="title" name="title" required placeholder="Ex: Garde de mon labrador en décembre">
          </div>

          <!-- Description -->
          <div class="form-group">
            <label for="description">Description *</label>
            <textarea id="description" name="description" required rows="6" placeholder="Décrivez vos services, les besoins de l'animal, vos disponibilités..."></textarea>
          </div>

          <!-- Type -->
          <div class="form-group">
            <label for="type">Type de service *</label>
            <select id="type" name="type" required>
              <option value="">-- Sélectionner --</option>
              <option value="gardiennage">Gardiennage à domicile</option>
              <option value="promenade">Promenade</option>
            </select>
          </div>

          <!-- City -->
          <div class="form-group">
            <label for="city">Ville *</label>
            <input type="text" id="city" name="city" required placeholder="Ex: Paris, Lyon, Marseille...">
          </div>

          <!-- Animal Selection -->
          <div class="form-group">
            <label for="animal_id">Animal concerné *</label>
            <?php if (empty($animals)): ?>
              <div class="alert alert-error">
                <p>Vous devez d'abord ajouter un animal avant de créer une annonce.</p>
                <a href="<?php echo $base_url; ?>/app/Views/add_animal.php" class="btn btn-primary">Ajouter un animal</a>
              </div>
            <?php else: ?>
              <div class="animal-selection">
                <select id="animal_id" name="animal_id" required>
                  <option value="">-- Sélectionner un animal --</option>
                  <?php foreach ($animals as $animal): ?>
                    <option value="<?php echo $animal['id']; ?>" <?php echo ($pre_selected_animal_id === $animal['id'] ? 'selected' : ''); ?>>
                      <?php echo htmlspecialchars($animal['name']); ?> (<?php echo htmlspecialchars($animal['race']); ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
                <a href="<?php echo $base_url; ?>/app/Views/add_animal.php" class="btn-add-animal">
                  <i class="fas fa-plus"></i> Ajouter un animal
                </a>
              </div>
            <?php endif; ?>
          </div>

          <!-- Dates and Times -->
          <div class="form-row">
            <div class="form-group">
              <label for="start_date">Date de début *</label>
              <input type="date" id="start_date" name="start_date" required>
            </div>
            <div class="form-group">
              <label for="start_hour">Heure de début *</label>
              <input type="time" id="start_hour" name="start_hour" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="end_date">Date de fin *</label>
              <input type="date" id="end_date" name="end_date" required>
            </div>
            <div class="form-group">
              <label for="end_hour">Heure de fin *</label>
              <input type="time" id="end_hour" name="end_hour" required>
            </div>
          </div>

          <!-- Price -->
          <div class="form-group">
            <label for="price">Prix par jour (€) *</label>
            <input type="number" id="price" name="price" required min="0" step="0.01" placeholder="Ex: 25.00">
          </div>

          <!-- Buttons -->
          <div class="form-actions">
            <button type="submit" class="btn-submit">
              <i class="fas fa-check"></i> Déposer l'annonce
            </button>
            <a href="<?php echo $base_url; ?>app/Views/advertisements.php" class="btn-cancel">
              <i class="fas fa-times"></i> Annuler
            </a>
          </div>
        </form>

      <?php endif; ?>
    </div>
  </main>

  <?php include $base_dir . "/app/Views/Components/footer.php"; ?>
</body>

</html>