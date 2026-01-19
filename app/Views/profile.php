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

// DB and user model
include $base_dir . 'app/Models/connection_db.php';
include $base_dir . 'app/Models/requests.users.php';
include $base_dir . 'app/Models/requests.animals.php';

// Determine which profile to show (optional ?id=)
$profile_user_id = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['user_id'];
$profile_user = trouveParId($db, $profile_user_id);
if (count($profile_user) === 0) {
  // user not found
  header('Location: ' . $base_url . '/app/Views/home.php');
  exit;
}
$profile_user = $profile_user[0];

// Viewer info
$viewer_id = $_SESSION['user_id'];
$is_own_profile = ($viewer_id === $profile_user_id);

// Get animals if own profile
$animals = [];
$delete_message = null;
$add_animal_message = null;

if ($is_own_profile) {
  $animals = obtenirAnimauxUtilisateur($viewer_id);

  // Handle add animal if requested
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && isset($_POST['race'])) {
    // Validate required fields
    $required_fields = ['name', 'race', 'gender', 'birthdate'];
    $has_error = false;

    foreach ($required_fields as $field) {
      if (empty($_POST[$field])) {
        $add_animal_message = ['type' => 'error', 'text' => 'Tous les champs sont obligatoires'];
        $has_error = true;
        break;
      }
    }

    if (!$has_error) {
      // Sanitize data
      $animal_data = [
        'user_id' => $viewer_id,
        'name' => htmlspecialchars(trim($_POST['name'])),
        'race' => htmlspecialchars(trim($_POST['race'])),
        'gender' => htmlspecialchars($_POST['gender']),
        'birthdate' => htmlspecialchars($_POST['birthdate'])
      ];

      // Validate birthdate
      if (!strtotime($animal_data['birthdate'])) {
        $add_animal_message = ['type' => 'error', 'text' => 'La date de naissance est invalide'];
        $has_error = true;
      }

      // Validate gender
      if (!$has_error && !in_array($animal_data['gender'], ['male', 'female'])) {
        $add_animal_message = ['type' => 'error', 'text' => 'Le genre doit être mâle ou femelle'];
        $has_error = true;
      }

      // Validate lengths
      if (!$has_error && strlen($animal_data['name']) > 50) {
        $add_animal_message = ['type' => 'error', 'text' => 'Le nom ne doit pas dépasser 50 caractères'];
        $has_error = true;
      }

      if (!$has_error && strlen($animal_data['race']) > 50) {
        $add_animal_message = ['type' => 'error', 'text' => 'La race ne doit pas dépasser 50 caractères'];
        $has_error = true;
      }

      if (!$has_error) {
        // Create animal
        $animal_id = creerAnimal($animal_data);
        if ($animal_id) {
          $add_animal_message = ['type' => 'success', 'text' => 'Animal ajouté avec succès !'];
          // Refresh animals list
          $animals = obtenirAnimauxUtilisateur($viewer_id);
        } else {
          $add_animal_message = ['type' => 'error', 'text' => 'Erreur lors de l\'ajout de l\'animal'];
        }
      }
    }
  }

  // Handle delete if requested
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_animal_id'])) {
    $animal_id = (int)$_POST['delete_animal_id'];
    $animal = obtenirAnimalPar($animal_id);
    if ($animal && $animal['user_id'] == $viewer_id) {
      if (supprimerAnimal($animal_id)) {
        $delete_message = ['type' => 'success', 'text' => 'Animal supprimé avec succès'];
        $animals = obtenirAnimauxUtilisateur($viewer_id);
      } else {
        $delete_message = ['type' => 'error', 'text' => 'Erreur lors de la suppression'];
      }
    }
  }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KeepMyPet - Mon profil</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/profile_modern.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
  <!-- HEADER -->
  <?php include $base_dir . "/app/Views/Components/header.php"; ?>

  <div class="profile-container">

    <!-- Profile Header Card -->
    <div class="profile-header">
      <div class="profile-header-content">
        <div class="profile-avatar">
          <?php if (!empty($profile_user['first_name'])): ?>
            <span><?php echo strtoupper(substr($profile_user['first_name'], 0, 1)) . strtoupper(substr($profile_user['last_name'] ?? '', 0, 1)); ?></span>
          <?php else: ?>
            <span>JD</span>
          <?php endif; ?>
        </div>

        <div class="profile-info">
          <h1><?php echo htmlspecialchars($profile_user['first_name'] . ' ' . $profile_user['last_name']); ?></h1>
          <div class="profile-meta">
            <span><i class="fas fa-star"></i> 4.8/5 (23 avis)</span>
            <span><i class="fas fa-map-marker-alt"></i> Paris</span>
            <span><i class="fas fa-calendar"></i> Membre depuis 2024</span>
          </div>
        </div>

        <?php if ($is_own_profile): ?>
          <a href="<?php echo $base_url; ?>/app/Views/profile_settings.php" class="btn-edit-profile">
            <i class="fas fa-cog"></i> Paramètres
          </a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Content Grid -->
    <div class="profile-grid">

      <!-- Left Column -->
      <div class="profile-main">

        <!-- Animals Section (own profile only) -->
        <?php if ($is_own_profile): ?>
          <section class="section">
            <div class="section-header">
              <h2><i class="fas fa-paw"></i> Mes animaux</h2>
              <button class="btn btn-primary" onclick="openAddAnimalModal()">
                <i class="fas fa-plus"></i> Ajouter
              </button>
            </div>

            <?php if ($delete_message): ?>
              <div class="alert alert-<?php echo $delete_message['type']; ?>">
                <i class="fas fa-<?php echo $delete_message['type'] === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo htmlspecialchars($delete_message['text']); ?>
              </div>
            <?php endif; ?>

            <?php if (empty($animals)): ?>
              <div class="empty-state">
                <div class="empty-icon">🐾</div>
                <h3>Aucun animal</h3>
                <p>Ajoutez votre premier animal pour commencer</p>
                <button class="btn btn-primary" onclick="openAddAnimalModal()">
                  <i class="fas fa-plus"></i> Ajouter un animal
                </button>
              </div>
            <?php else: ?>
              <div class="animals-grid">
                <?php foreach ($animals as $animal): ?>
                  <div class="animal-card">
                    <div class="animal-card-top">
                      <h3><?php echo htmlspecialchars($animal['name']); ?></h3>
                      <div class="animal-actions">
                        <a href="<?php echo $base_url; ?>/app/Views/edit_animal.php?id=<?php echo (int)$animal['id']; ?>" class="btn-icon" title="Modifier">
                          <i class="fas fa-pencil"></i>
                        </a>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Supprimer cet animal ?');">
                          <input type="hidden" name="delete_animal_id" value="<?php echo (int)$animal['id']; ?>">
                          <button type="submit" class="btn-icon btn-delete" title="Supprimer">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
                      </div>
                    </div>
                    <div class="animal-details">
                      <p><strong><?php echo htmlspecialchars($animal['race']); ?></strong></p>
                      <p><?php echo $animal['gender'] === 'male' ? '♂ Mâle' : '♀ Femelle'; ?></p>
                      <p>Âge: <?php
                              $birthdate = new DateTime($animal['birthdate']);
                              $today = new DateTime('today');
                              $age = $birthdate->diff($today)->y;
                              echo $age . ' an' . ($age > 1 ? 's' : '');
                              ?></p>
                    </div>
                    <a href="<?php echo $base_url; ?>/app/Views/create_advertisement.php?animal_id=<?php echo (int)$animal['id']; ?>" class="btn btn-secondary btn-block">
                      <i class="fas fa-bullhorn"></i> Créer annonce
                    </a>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </section>
        <?php endif; ?>

        <!-- Advertisements Section -->
        <section class="section">
          <h2><i class="fas fa-newspaper"></i> Annonces récentes</h2>
          <div class="ads-container">
            <p style="text-align: center; color: #999; padding: 40px 20px;">Aucune annonce pour le moment</p>
          </div>
        </section>

      </div>

      <!-- Sidebar -->
      <aside class="profile-sidebar">
        <div class="sidebar-card">
          <h3>À propos</h3>
          <p><?php echo htmlspecialchars($profile_user['first_name']); ?> est un gardien fiable et bienveillant, passionné par les animaux.</p>
        </div>

        <div class="sidebar-card">
          <h3>Services</h3>
          <ul>
            <li><i class="fas fa-home"></i> Garde à domicile</li>
            <li><i class="fas fa-walking"></i> Promenade</li>
            <li><i class="fas fa-utensils"></i> Alimentation</li>
          </ul>
        </div>

        <?php if (!$is_own_profile): ?>
          <div class="sidebar-card">
            <button class="btn btn-primary btn-block">
              <i class="fas fa-envelope"></i> Contacter
            </button>
          </div>
        <?php endif; ?>
      </aside>

    </div>

  </div>

  <!-- Modal Add Animal (hidden, opens via JavaScript) -->
  <div class="modal-overlay" id="addAnimalModal" style="display: none;">
    <div class="modal-content">
      <div class="modal-header">
        <h1>Ajouter un animal</h1>
        <button class="modal-close" onclick="closeAddAnimalModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="modal-body">
        <p class="modal-subtitle">Inscrivez votre compagnon à fourrure</p>

        <?php if ($add_animal_message): ?>
          <div class="alert alert-<?php echo $add_animal_message['type']; ?>">
            <i class="fas fa-<?php echo $add_animal_message['type'] === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <?php echo htmlspecialchars($add_animal_message['text']); ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="" class="modal-form" id="addAnimalForm">
          <div class="form-row">
            <div class="form-group">
              <label for="modal_name">Nom de l'animal</label>
              <input type="text" id="modal_name" name="name" placeholder="Ex: Rocky" required maxlength="50">
              <div class="help-text">Max 50 caractères</div>
            </div>
            <div class="form-group">
              <label for="modal_race">Race/Type</label>
              <input type="text" id="modal_race" name="race" placeholder="Ex: Golden Retriever" required maxlength="50">
              <div class="help-text">Max 50 caractères</div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="modal_gender">Genre</label>
              <select id="modal_gender" name="gender" required>
                <option value="">-- Sélectionnez --</option>
                <option value="male">Mâle</option>
                <option value="female">Femelle</option>
              </select>
            </div>
            <div class="form-group">
              <label for="modal_birthdate">Date de naissance</label>
              <input type="date" id="modal_birthdate" name="birthdate" required>
            </div>
          </div>

          <div class="form-buttons">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-plus"></i> Ajouter l'animal
            </button>
            <button type="button" class="btn btn-secondary" onclick="closeAddAnimalModal()">Annuler</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    function openAddAnimalModal() {
      document.getElementById('addAnimalModal').style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }

    function closeAddAnimalModal() {
      document.getElementById('addAnimalModal').style.display = 'none';
      document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    document.getElementById('addAnimalModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeAddAnimalModal();
      }
    });

    // Handle form submission
    document.getElementById('addAnimalForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const formData = new FormData(this);
      fetch(window.location.href, {
        method: 'POST',
        body: formData
      }).then(response => {
        if (response.ok) {
          setTimeout(() => {
            location.reload();
          }, 1000);
        }
      }).catch(error => {
        console.error('Erreur:', error);
      });
    });
  </script>

  </div><!-- /.profile-container -->

  <!-- FOOTER -->
  <?php include $base_dir . '/app/Views/Components/footer.php'; ?>

</body>

</html>