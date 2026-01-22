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

// Load language system
require_once $base_dir . 'app/Config/language.php';

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
<html lang="<?php echo getCurrentLanguage(); ?>">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KeepMyPet - <?php echo t('post_ad'); ?></title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/create_advertisement.css">
</head>

<body>
  <main class="create-ad-container">
    <div class="create-ad-wrapper">
      <h1><?php echo t('post_ad'); ?></h1>
      <p class="subtitle"><?php echo t('propose_services'); ?></p>

      <?php if ($error): ?>
        <div class="alert alert-error">
          <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="alert alert-success">
          <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
          <p><?php echo t('redirecting'); ?></p>
        </div>
      <?php else: ?>

        <form method="POST" class="create-ad-form">
          <!-- Title -->
          <div class="form-group">
            <label for="title"><?php echo t('ad_title'); ?> *</label>
            <input type="text" id="title" name="title" required placeholder="<?php echo t('ad_title_placeholder'); ?>">
          </div>

          <!-- Description -->
          <div class="form-group">
            <label for="description"><?php echo t('ad_description'); ?> *</label>
            <textarea id="description" name="description" required rows="6" placeholder="<?php echo t('ad_description_placeholder'); ?>"></textarea>
          </div>

          <!-- Type -->
          <div class="form-group">
            <label for="type"><?php echo t('ad_service_type'); ?> *</label>
            <select id="type" name="type" required>
              <option value="">-- <?php echo t('select'); ?> --</option>
              <option value="gardiennage"><?php echo t('home_sitting'); ?></option>
              <option value="promenade"><?php echo t('walking'); ?></option>
            </select>
          </div>

          <!-- City -->
          <div class="form-group">
            <label for="city"><?php echo t('city'); ?> *</label>
            <input type="text" id="city" name="city" required placeholder="<?php echo t('city_placeholder'); ?>">
          </div>

          <!-- Animal Selection -->
          <div class="form-group">
            <label for="animal_id"><?php echo t('ad_animal_concerned'); ?> *</label>
            <?php if (empty($animals)): ?>
              <div class="alert alert-error">
                <p><?php echo t('ad_must_add_animal_first'); ?></p>
                <a href="<?php echo $base_url; ?>/app/Views/add_animal.php" class="btn btn-primary"><?php echo t('add_animal'); ?></a>
              </div>
            <?php else: ?>
              <div class="animal-selection">
                <select id="animal_id" name="animal_id" required>
                  <option value="">-- <?php echo t('select_animal'); ?> --</option>
                  <?php foreach ($animals as $animal): ?>
                    <option value="<?php echo $animal['id']; ?>" <?php echo ($pre_selected_animal_id === $animal['id'] ? 'selected' : ''); ?>>
                      <?php echo htmlspecialchars($animal['name']); ?> (<?php echo htmlspecialchars($animal['race']); ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
                <a href="<?php echo $base_url; ?>/app/Views/add_animal.php" class="btn-add-animal">
                  <i class="fas fa-plus"></i> <?php echo t('add_animal'); ?>
                </a>
              </div>
            <?php endif; ?>
          </div>

          <!-- Dates and Times -->
          <div class="form-row">
            <div class="form-group">
              <label for="start_date"><?php echo t('ad_start_date'); ?> *</label>
              <input type="date" id="start_date" name="start_date" required>
            </div>
            <div class="form-group">
              <label for="start_hour"><?php echo t('ad_start_time'); ?> *</label>
              <input type="time" id="start_hour" name="start_hour" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="end_date"><?php echo t('ad_end_date'); ?> *</label>
              <input type="date" id="end_date" name="end_date" required>
            </div>
            <div class="form-group">
              <label for="end_hour"><?php echo t('ad_end_time'); ?> *</label>
              <input type="time" id="end_hour" name="end_hour" required>
            </div>
          </div>

          <!-- Price -->
          <div class="form-group">
            <label for="price"><?php echo t('ad_price_per_day'); ?> *</label>
            <input type="number" id="price" name="price" required min="0" step="0.01" placeholder="<?php echo t('ad_price_placeholder'); ?>">
          </div>

          <!-- Buttons -->
          <div class="form-actions">
            <button type="submit" class="btn-submit">
              <i class="fas fa-check"></i> <?php echo t('ad_post_button'); ?>
            </button>
            <a href="<?php echo $base_url; ?>app/Views/advertisements.php" class="btn-cancel">
              <i class="fas fa-times"></i> <?php echo t('cancel'); ?>
            </a>
          </div>
        </form>

      <?php endif; ?>
    </div>
  </main>

  <?php include $base_dir . "/app/Views/Components/footer.php"; ?>
</body>

</html>