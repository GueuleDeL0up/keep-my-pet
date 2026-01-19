<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Define the base
$base_url = "/keep-my-pet/";
$base_dir = __DIR__ . "/../../";

// Include Controller
require_once $base_dir . "app/Controller/AdvertisementsController.php";

// Call controller to get advertisements
$data = AdvertisementsController::afficherAnnonces();
$annonces = $data['annonces'] ?? [];
$filters = $data['filters'] ?? [];
$count = $data['count'] ?? 0;

// Include Header
include $base_dir . "/app/Views/Components/header.php";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KeepMyPet - Annonces</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/advertisements.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>
  <main class="ads-container">
    <!-- Top Bar with Search and Button -->
    <div class="ads-top-bar">
      <div class="search-wrapper">
        <form method="GET" style="display: flex; width: 100%;">
          <input
            type="text"
            name="search"
            placeholder="Rechercher sur KeepMyPet"
            value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>" />
          <i class="fas fa-search search-icon"></i>
        </form>
      </div>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="<?php echo $base_url; ?>app/Views/create_advertisement.php" class="btn-deposit">
          <i class="fas fa-plus-circle"></i> Déposer une annonce
        </a>
      <?php else: ?>
        <a href="<?php echo $base_url; ?>app/Views/log_in.php" class="btn-deposit">
          <i class="fas fa-plus-circle"></i> Déposer une annonce
        </a>
      <?php endif; ?>
    </div>

    <!-- Filters Row -->
    <div class="filters-row">
      <form method="GET" style="display: flex; gap: 10px; flex-wrap: wrap; width: 100%;">
        <select name="type" class="filter-pill">
          <option value="">Type d'annonce</option>
          <option value="gardiennage" <?php echo $filters['type'] === 'gardiennage' ? 'selected' : ''; ?>>
            Gardiennage
          </option>
          <option value="promenade" <?php echo $filters['type'] === 'promenade' ? 'selected' : ''; ?>>
            Promenade
          </option>
        </select>

        <input
          type="text"
          name="city"
          class="filter-pill"
          placeholder="Ville"
          value="<?php echo htmlspecialchars($filters['city'] ?? ''); ?>"
          style="padding: 10px 15px; border-radius: 50px; border: 1px solid #ddd;" />

        <input
          type="number"
          name="min_price"
          class="filter-pill"
          placeholder="Prix min"
          value="<?php echo htmlspecialchars($filters['min_price'] ?? ''); ?>"
          style="padding: 10px 15px; border-radius: 50px; border: 1px solid #ddd; width: 120px;" />

        <input
          type="number"
          name="max_price"
          class="filter-pill"
          placeholder="Prix max"
          value="<?php echo htmlspecialchars($filters['max_price'] ?? ''); ?>"
          style="padding: 10px 15px; border-radius: 50px; border: 1px solid #ddd; width: 120px;" />

        <button type="submit" style="padding: 10px 20px; border-radius: 50px; border: 1px solid #ddd; background: #5fbbb7; color: white; cursor: pointer;">
          Rechercher
        </button>
      </form>
    </div>

    <!-- Advertisements Grid -->
    <div class="ads-grid">
      <!-- Left Column: List of Advertisements -->
      <div class="ads-list-col">
        <?php if (count($annonces) === 0): ?>
          <div class="no-results">
            <p><i class="fas fa-inbox"></i></p>
            <p>Aucune annonce trouvée. Soyez patient, des gardiens ajoutent leurs annonces chaque jour !</p>
          </div>
        <?php else: ?>
          <?php foreach ($annonces as $annonce): ?>
            <div class="ad-card" onclick="selectAd(<?php echo htmlspecialchars(json_encode($annonce)); ?>)">
              <div class="ad-icon">
                <?php if ($annonce['type'] === 'gardiennage'): ?>
                  <i class="fas fa-home"></i>
                <?php else: ?>
                  <i class="fas fa-paw"></i>
                <?php endif; ?>
              </div>
              <div class="ad-card-info">
                <div class="ad-card-title"><?php echo htmlspecialchars($annonce['title']); ?></div>
                <div class="ad-card-meta">
                  <i class="fas fa-map-marker-alt"></i>
                  <?php echo htmlspecialchars($annonce['city']); ?>
                </div>
                <div class="ad-card-meta">
                  <i class="fas fa-calendar-alt"></i>
                  <?php echo date('d/m/Y', strtotime($annonce['start_date'])); ?>
                </div>
              </div>
              <div class="ad-card-price"><?php echo number_format($annonce['price'], 2); ?>€</div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- Right Column: Advertisement Detail -->
      <div class="ads-detail-col">
        <div id="ad-detail" class="ad-detail" style="display: none;">
          <div class="ad-detail-header">
            <h2 id="detail-title">Sélectionnez une annonce</h2>
            <p id="detail-type"></p>
          </div>
          <div class="ad-detail-body">
            <!-- Description Section -->
            <div class="detail-section">
              <h3>Description</h3>
              <p id="detail-description"></p>
            </div>

            <!-- Details Grid -->
            <div class="detail-grid">
              <div class="detail-item">
                <div class="detail-item-label">Localisation</div>
                <div class="detail-item-value" id="detail-city"></div>
              </div>
              <div class="detail-item">
                <div class="detail-item-label">Prix</div>
                <div class="detail-item-value" id="detail-price"></div>
              </div>
              <div class="detail-item">
                <div class="detail-item-label">Début</div>
                <div class="detail-item-value" id="detail-start"></div>
              </div>
              <div class="detail-item">
                <div class="detail-item-label">Fin</div>
                <div class="detail-item-value" id="detail-end"></div>
              </div>
              <div class="detail-item">
                <div class="detail-item-label">Animal</div>
                <div class="detail-item-value" id="detail-animal"></div>
              </div>
              <div class="detail-item">
                <div class="detail-item-label">Race</div>
                <div class="detail-item-value" id="detail-race"></div>
              </div>
            </div>

            <!-- User Contact Section -->
            <div class="detail-section" style="margin-top: 30px;">
              <h3>Contacter le propriétaire</h3>
              <div class="user-info">
                <div class="user-avatar" id="user-avatar">?</div>
                <div class="user-details">
                  <h4><a href="javascript:void(0)" id="user-name-link" style="color: #333; text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Utilisateur</a></h4>
                  <p id="user-phone"></p>
                  <div class="user-contact">
                    <a href="#" id="btn-message" class="btn-contact btn-contact-primary">
                      <i class="fas fa-envelope"></i> Envoyer un message
                    </a>
                    <a href="#" id="btn-phone" class="btn-contact btn-contact-secondary">
                      <i class="fas fa-phone"></i> Appeler
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div id="ad-empty" class="empty-state">
          <h3><i class="fas fa-arrow-left"></i> Sélectionnez une annonce</h3>
          <p>Cliquez sur une annonce pour voir les détails</p>
        </div>
      </div>
    </div>
  </main>

  <?php include $base_dir . "/app/Views/Components/footer.php"; ?>

  <script src="<?php echo $base_url; ?>/public/assets/js/advertisements.js"></script>
</body>

</html>