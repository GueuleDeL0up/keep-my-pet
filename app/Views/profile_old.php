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
include $base_dir . 'app/Controller/AnimalsController.php';

// Determine which profile to show (optional ?id=)
$profile_user_id = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['user_id'];
$profile_user = trouveParId($db, $profile_user_id);
if (count($profile_user) === 0) {
  // user not found
  header('Location: ' . $base_url . '/app/Views/home.php');
  exit;
}
$profile_user = $profile_user[0];

// Check follow/favorite status for current logged-in user
$viewer_id = $_SESSION['user_id'];
$is_following = false;
$is_favorited = false;
if ($viewer_id !== $profile_user_id) {
  $is_following = estSuivi($db, $viewer_id, $profile_user_id);
  $is_favorited = estFavori($db, $viewer_id, $profile_user_id);
}

// Get animals if viewing own profile
$animals = [];
$delete_message = null;
if ($viewer_id === $profile_user_id) {
  $animals = obtenirAnimauxUtilisateur($viewer_id);
  
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

// HEADER
include $base_dir . "/app/Views/Components/header.php";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>KeepMyPet - Mon profil</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/profile.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
  <div class="profile-page">

    <div class="profile-hero">
      <div class="profile-hero-inner">
        <div class="avatar">
          <?php if (!empty($_SESSION['user_first_name'])): ?>
            <span class="avatar-initial"><?php echo strtoupper(substr($_SESSION['user_first_name'], 0, 1)) . strtoupper(substr($_SESSION['user_last_name'] ?? '', 0, 1)); ?></span>
          <?php else: ?>
            <span class="avatar-initial">JD</span>
          <?php endif; ?>
        </div>

        <div class="profile-info">
          <h1 class="name"><?php echo htmlspecialchars($profile_user['first_name'] . ' ' . $profile_user['last_name']); ?></h1>
          <div class="meta">
            <span class="rating">⭐ <strong>5</strong>(18 avis)</span>
            <span class="joined">📅 02/08/2024</span>
            <span class="location">📍 Paris</span>
          </div>
        </div>

        <div class="profile-actions">
          <?php if ($viewer_id !== $profile_user_id): ?>
            <button id="followBtn" class="btn follow <?php echo $is_following ? 'following' : ''; ?>" data-target-user-id="<?php echo $profile_user_id; ?>"><?php echo $is_following ? 'Suivi' : 'Suivre'; ?></button>
            <button id="moreBtn" class="btn ghost">⋮</button>
            <button id="starBtn" class="icon-btn <?php echo $is_favorited ? 'starred' : ''; ?>" data-target-user-id="<?php echo $profile_user_id; ?>"><?php echo $is_favorited ? '★' : '☆'; ?></button>
          <?php else: ?>
            <a href="<?php echo $base_url; ?>/app/Views/profile_settings.php" id="editProfileBtn" class="btn">Modifier profil</a>
            <button id="moreBtn" class="btn ghost">⋮</button>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="profile-content">
      <div class="left-col">
        
        <!-- Animals Section (if viewing own profile) -->
        <?php if ($viewer_id === $profile_user_id): ?>
          <div class="animals-section">
            <div class="section-header">
              <h2>Mes animaux</h2>
              <a href="<?php echo $base_url; ?>/app/Views/add_animal.php" class="btn-add-animal">
                <i class="fas fa-plus"></i> Ajouter
              </a>
            </div>

            <?php if ($delete_message): ?>
              <div class="alert alert-<?php echo $delete_message['type']; ?>">
                <?php echo htmlspecialchars($delete_message['text']); ?>
              </div>
            <?php endif; ?>

            <?php if (empty($animals)): ?>
              <div class="empty-animals">
                <div class="empty-icon">🐾</div>
                <p>Aucun animal pour le moment</p>
                <p class="empty-hint">Ajoutez votre premier animal pour créer des annonces</p>
                <a href="<?php echo $base_url; ?>/app/Views/add_animal.php" class="btn btn-primary-small">
                  <i class="fas fa-plus"></i> Ajouter un animal
                </a>
              </div>
            <?php else: ?>
              <div class="animals-grid">
                <?php foreach ($animals as $animal): ?>
                  <div class="animal-card-profile">
                    <div class="animal-card-header">
                      <h3><?php echo htmlspecialchars($animal['name']); ?></h3>
                      <div class="animal-card-actions">
                        <a href="<?php echo $base_url; ?>/app/Views/edit_animal.php?id=<?php echo (int)$animal['id']; ?>" class="btn-icon" title="Modifier">
                          <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr ?');">
                          <input type="hidden" name="delete_animal_id" value="<?php echo (int)$animal['id']; ?>">
                          <button type="submit" class="btn-icon delete" title="Supprimer">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
                      </div>
                    </div>
                    <div class="animal-card-info">
                      <p><strong><?php echo htmlspecialchars($animal['race']); ?></strong></p>
                      <p><?php echo $animal['gender'] === 'male' ? '♂ Mâle' : '♀ Femelle'; ?></p>
                      <p>🎂 <?php 
                        $birthdate = new DateTime($animal['birthdate']);
                        $today = new DateTime('today');
                        $age = $birthdate->diff($today)->y;
                        echo $age . ' an' . ($age > 1 ? 's' : '');
                      ?></p>
                    </div>
                    <a href="<?php echo $base_url; ?>/app/Views/create_advertisement.php?animal_id=<?php echo (int)$animal['id']; ?>" class="btn-create-ad">
                      Créer annonce
                    </a>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <h2>Annonces</h2>
          <div class="ad-left">
            <div class="ad-avatar">🐶</div>
          </div>
          <div class="ad-body">
            <h3>Rocky</h3>
            <p>Gardiennage à Paris 18e du 15 au 20 novembre 2025</p>
          </div>
          <div class="ad-right">
            <div class="price">⭐ 4.8<br><span>25 € / jour</span></div>
          </div>
        </div>

        <div class="ad-card">
          <div class="ad-left">
            <div class="ad-avatar">🐱</div>
          </div>
          <div class="ad-body">
            <h3>Milo</h3>
            <p>Gardiennage à Lille pour le 15 décembre 2025</p>
          </div>
          <div class="ad-right">
            <div class="price">⭐ 4.5<br><span>27 € / jour</span></div>
          </div>
        </div>

      </div>

      <aside class="right-col">
        <div class="detail-box">
          <h4>Gardiennage à domicile</h4>
          <p><strong>Titre :</strong> Garde d'un chien labrador pendant les vacances</p>
          <p><strong>Type :</strong> Gardiennage</p>
          <p><strong>Animal :</strong> Chien • Labrador • Rocky</p>
          <p><strong>Lieu :</strong> Paris 18e</p>
          <p><strong>Dates :</strong> Du 15 au 20 novembre 2025</p>
          <p><strong>Tarif :</strong> 25 € / jour</p>
        </div>
      </aside>
    </div>

  </div>

  <script src="<?php echo $base_url; ?>/public/assets/js/profile.js"></script>
</body>

</html>

<?php
// FOOTER
include $base_dir . '/app/Views/Components/footer.php';
?>