<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Define the base
$base_url = "/keep-my-pet/";
$base_dir = __DIR__ . "/../../";

// Load language system
require_once $base_dir . 'app/Config/language.php';

// Include database connection and models
require_once $base_dir . "app/Models/connection_db.php";
require_once $base_dir . "app/Models/requests.reviews.php";

// Get user ID from URL
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

error_log("=== USER PROFILE PAGE ===");
error_log("user_id from URL: " . $user_id);

// Initialize variables
$user = null;
$animals = [];
$advertisements = [];
$reviews = [];

if ($user_id > 0) {
  // Fetch user information
  try {
    $stmt = $db->prepare("SELECT id, first_name, last_name, email, phone_number, address, postal_code, note, created_at FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    error_log("User found: " . ($user ? "YES" : "NO"));

    if ($user) {
      // Fetch user's animals
      $stmt = $db->prepare("SELECT id, name, race, gender, birthdate FROM animals WHERE user_id = ? ORDER BY name");
      $stmt->execute([$user_id]);
      $animals = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // Fetch user's advertisements
      $stmt = $db->prepare("SELECT id, title, description, type, city, price, start_date FROM advertisements WHERE user_id = ? ORDER BY start_date DESC LIMIT 5");
      $stmt->execute([$user_id]);
      $advertisements = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // Fetch reviews about this user
      $reviews = obtenirAvisUtilisateur($user_id);

      error_log("Animals: " . count($animals) . ", Ads: " . count($advertisements));
    }
  } catch (PDOException $e) {
    error_log("Erreur lors de la récupération du profil utilisateur: " . $e->getMessage());
  }
}

// Include Header
include $base_dir . "/app/Views/Components/header.php";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?> - KeepMyPet</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/user_profile.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>
  <main class="profile-container">

    <?php if (!$user): ?>
      <div style="text-align: center; padding: 40px; background: white; border-radius: 10px; margin: 40px auto; max-width: 600px;">
        <h2>Utilisateur non trouvé</h2>
        <p>L'utilisateur avec l'ID <?php echo htmlspecialchars($_GET['id'] ?? '0'); ?> n'existe pas.</p>
        <a href="<?php echo $base_url; ?>app/Views/advertisements.php" style="color: #5fbbb7; text-decoration: none;">
          ← Retour aux annonces
        </a>
      </div>
    <?php else: ?>

      <!-- Profile Header -->
      <div class="profile-header">
        <div class="profile-avatar">
          <div class="avatar-circle">
            <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
          </div>
        </div>

        <div class="profile-info">
          <h1><?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?></h1>

          <div class="profile-rating">
            <div class="stars">
              <?php
              $rating = round($user['note']);
              for ($i = 1; $i <= 5; $i++) {
                if ($i <= $rating) {
                  echo '<i class="fas fa-star filled"></i>';
                } else {
                  echo '<i class="fas fa-star empty"></i>';
                }
              }
              ?>
            </div>
            <span class="rating-text"><?php echo number_format($user['note'], 2); ?>/5</span>
            <span class="rating-count">(<?php echo count($reviews); ?> avis)</span>
          </div>

          <p class="member-since">
            <i class="fas fa-calendar"></i>
            Membre depuis <?php echo date('d/m/Y', strtotime($user['created_at'])); ?>
          </p>
        </div>

        <div class="profile-actions">
          <a href="mailto:<?php echo htmlspecialchars($user['email']); ?>" class="btn-action btn-email">
            <i class="fas fa-envelope"></i> Envoyer un email
          </a>
          <?php if (!empty($user['phone_number'])): ?>
            <a href="tel:<?php echo htmlspecialchars($user['phone_number']); ?>" class="btn-action btn-phone">
              <i class="fas fa-phone"></i> Appeler
            </a>
          <?php endif; ?>
        </div>
      </div>

      <!-- Profile Details -->
      <div class="profile-section">
        <h2><i class="fas fa-star"></i> Avis reçus</h2>
        <?php if (empty($reviews)): ?>
          <p>Aucun avis pour le moment.</p>
        <?php else: ?>
          <div class="reviews-list">
            <?php foreach ($reviews as $review): ?>
              <details class="review-item">
                <summary>
                  <div class="review-summary">
                    <div class="review-stars">
                      <?php
                        $r = (int)$review['rating'];
                        for ($i = 1; $i <= 5; $i++) {
                          echo $i <= $r ? '<i class="fas fa-star filled"></i>' : '<i class="fas fa-star empty"></i>';
                        }
                      ?>
                    </div>
                    <div class="review-meta">
                      <strong><?php echo htmlspecialchars($review['reviewer_first_name'] . ' ' . $review['reviewer_last_name']); ?></strong>
                      <span class="review-ad">Sur: <?php echo htmlspecialchars($review['ad_title']); ?></span>
                      <span class="review-date"><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></span>
                    </div>
                  </div>
                </summary>
                <div class="review-body">
                  <?php if (!empty($review['comment'])): ?>
                    <p class="review-comment"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                  <?php else: ?>
                    <p class="review-comment muted">Pas de commentaire</p>
                  <?php endif; ?>
                </div>
              </details>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="profile-details">
        <div class="detail-card">
          <h3><i class="fas fa-map-marker-alt"></i> Localisation</h3>
          <p>
            <?php
            echo htmlspecialchars($user['address'] ?? 'Non spécifiée');
            if (!empty($user['postal_code'])) {
              echo ', ' . htmlspecialchars($user['postal_code']);
            }
            ?>
          </p>
        </div>

        <div class="detail-card">
          <h3><i class="fas fa-envelope"></i> Email</h3>
          <p><?php echo htmlspecialchars($user['email']); ?></p>
        </div>

        <?php if (!empty($user['phone_number'])): ?>
          <div class="detail-card">
            <h3><i class="fas fa-phone"></i> Téléphone</h3>
            <p><?php echo htmlspecialchars($user['phone_number']); ?></p>
          </div>
        <?php endif; ?>
      </div>

      <!-- Animals Section -->
      <?php if (!empty($animals)): ?>
        <section class="profile-section">
          <h2><i class="fas fa-paw"></i> Animaux de <?php echo htmlspecialchars($user['first_name']); ?></h2>
          <div class="animals-grid">
            <?php foreach ($animals as $animal): ?>
              <div class="animal-card">
                <div class="animal-icon">
                  <i class="fas fa-<?php echo $animal['gender'] === 'male' ? 'mars' : 'venus'; ?>"></i>
                </div>
                <h3><?php echo htmlspecialchars($animal['name']); ?></h3>
                <p class="animal-race"><?php echo htmlspecialchars($animal['race']); ?></p>
                <p class="animal-gender">
                  <?php echo $animal['gender'] === 'male' ? 'Mâle' : 'Femelle'; ?>
                </p>
              </div>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endif; ?>

      <!-- Advertisements Section -->
      <?php if (!empty($advertisements)): ?>
        <section class="profile-section">
          <h2><i class="fas fa-clipboard-list"></i> Annonces Récentes</h2>
          <div class="ads-list">
            <?php foreach ($advertisements as $ad): ?>
              <div class="ad-item">
                <div class="ad-item-header">
                  <h3><?php echo htmlspecialchars($ad['title']); ?></h3>
                  <span class="ad-type <?php echo $ad['type']; ?>">
                    <?php echo $ad['type'] === 'gardiennage' ? t('home_sitting') : t('walking'); ?>
                  </span>
                </div>
                <p class="ad-description"><?php echo htmlspecialchars(substr($ad['description'], 0, 100)); ?>...</p>
                <div class="ad-info">
                  <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($ad['city']); ?></span>
                  <span><i class="fas fa-euro-sign"></i> <?php echo number_format($ad['price'], 2); ?>€</span>
                  <span><i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($ad['start_date'])); ?></span>
                </div>
                <a href="<?php echo $base_url; ?>app/Views/advertisements.php" class="link-to-ads">
                  Voir cette annonce →
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endif; ?>

    <?php endif; ?>

    <div class="profile-footer">
      <a href="<?php echo $base_url; ?>app/Views/advertisements.php" class="btn-back">
        <i class="fas fa-arrow-left"></i> Retour aux annonces
      </a>
    </div>
  </main>

  <?php include $base_dir . "/app/Views/Components/footer.php"; ?>
</body>

</html>