<?php
// Set timezone to Europe/Paris (France)
date_default_timezone_set('Europe/Paris');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Define the base
$base_url = "/keep-my-pet/";
$base_dir = __DIR__ . "/../../";

// Load language system
require_once $base_dir . 'app/Config/language.php';

// Include Controller
require_once $base_dir . "app/Controller/AdvertisementsController.php";
require_once $base_dir . "app/Models/requests.bookings.php";
require_once $base_dir . "app/Models/requests.advertisements.php";

// Call controller to get advertisements
$data = AdvertisementsController::afficherAnnonces();
$annonces = $data['annonces'] ?? [];
$filters = $data['filters'] ?? [];
$count = $data['count'] ?? 0;

// Handle booking request submission
$booking_message = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_ad'])) {
  if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $base_url . 'app/Views/log_in.php');
    exit;
  }

  $ad_id = (int)($_POST['ad_id'] ?? 0);
  $sitter_id = $_SESSION['user_id'];

  error_log("DEBUG: Booking attempt - ad_id=$ad_id, sitter_id=$sitter_id");

  // Get the advertisement to find owner
  $ad = obtenirAnnoncePar($ad_id);

  if ($ad) {
    error_log("DEBUG: Ad found - owner_id=" . $ad['user_id'] . ", sitter_id=$sitter_id");
  } else {
    error_log("DEBUG: Ad not found for id=$ad_id");
  }

  if ($ad && $ad['user_id'] != $sitter_id) {
    $owner_id = $ad['user_id'];
    if (creerDemande($ad_id, $sitter_id, $owner_id)) {
      $booking_message = ['type' => 'success', 'text' => t('booking_sent')];
      error_log("DEBUG: Booking created successfully");
    } else {
      $booking_message = ['type' => 'error', 'text' => t('booking_already_sent')];
      error_log("DEBUG: Booking already exists or error");
    }
  } else {
    $booking_message = ['type' => 'error', 'text' => t('cannot_book_own')];
    error_log("DEBUG: Owner trying to book own ad or ad not found");
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
  <title>KeepMyPet - Annonces</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/advertisements.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>
  <main class="ads-container">
    <!-- Booking Message -->
    <?php if ($booking_message): ?>
      <div class="alert alert-<?php echo $booking_message['type']; ?>" style="margin: 20px; border-radius: 8px;">
        <i class="fas fa-<?php echo $booking_message['type'] === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
        <?php echo htmlspecialchars($booking_message['text']); ?>
      </div>
    <?php endif; ?>

    <!-- Top Bar with Search and Button -->
    <div class="ads-top-bar">
      <div class="search-wrapper">
        <form method="GET" style="display: flex; width: 100%;">
          <input
            type="text"
            name="search"
            placeholder="<?php echo t('search_keepmypet'); ?>"
            value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>" />
          <i class="fas fa-search search-icon"></i>
        </form>
      </div>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="<?php echo $base_url; ?>app/Views/create_advertisement.php" class="btn-deposit">
          <i class="fas fa-plus-circle"></i> <?php echo t('post_ad'); ?>
        </a>
      <?php else: ?>
        <a href="<?php echo $base_url; ?>app/Views/log_in.php" class="btn-deposit">
          <i class="fas fa-plus-circle"></i> <?php echo t('post_ad'); ?>
        </a>
      <?php endif; ?>
    </div>

    <!-- Filters Row -->
    <div class="filters-row">
      <form method="GET" style="display: flex; gap: 10px; flex-wrap: wrap; width: 100%;">
        <select name="type" class="filter-pill">
          <option value=""><?php echo t('ad_type'); ?></option>
          <option value="gardiennage" <?php echo $filters['type'] === 'gardiennage' ? 'selected' : ''; ?>>
            <?php echo t('home_sitting'); ?>
          </option>
          <option value="promenade" <?php echo $filters['type'] === 'promenade' ? 'selected' : ''; ?>>
            <?php echo t('walking'); ?>
          </option>
        </select>

        <input
          type="text"
          name="city"
          class="filter-pill"
          placeholder="<?php echo t('city'); ?>"
          value="<?php echo htmlspecialchars($filters['city'] ?? ''); ?>"
          style="padding: 10px 15px; border-radius: 50px; border: 1px solid #ddd;" />

        <input
          type="number"
          name="min_price"
          class="filter-pill"
          placeholder="<?php echo t('min_price'); ?>"
          value="<?php echo htmlspecialchars($filters['min_price'] ?? ''); ?>"
          style="padding: 10px 15px; border-radius: 50px; border: 1px solid #ddd; width: 120px;" />

        <input
          type="number"
          name="max_price"
          class="filter-pill"
          placeholder="<?php echo t('max_price'); ?>"
          value="<?php echo htmlspecialchars($filters['max_price'] ?? ''); ?>"
          style="padding: 10px 15px; border-radius: 50px; border: 1px solid #ddd; width: 120px;" />

        <button type="submit" style="padding: 10px 20px; border-radius: 50px; border: 1px solid #ddd; background: #5fbbb7; color: white; cursor: pointer;">
          <?php echo t('search'); ?>
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
            <p><?php echo t('no_ads_found'); ?></p>
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
                  <?php echo formatDate($annonce['start_date'], 'medium'); ?> <?php echo t('at'); ?> <?php echo $annonce['start_hour']; ?>
                </div>
                <div class="ad-card-meta">
                  <i class="fas fa-calendar-check"></i>
                  <?php echo formatDate($annonce['end_date'], 'medium'); ?> <?php echo t('at'); ?> <?php echo $annonce['end_hour']; ?>
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
            <h2 id="detail-title"><?php echo t('select_ad'); ?></h2>
            <p id="detail-type"></p>
          </div>
          <div class="ad-detail-body">
            <!-- Description Section -->
            <div class="detail-section">
              <h3><?php echo t('description'); ?></h3>
              <p id="detail-description"></p>
            </div>

            <!-- Details Grid -->
            <div class="detail-grid">
              <div class="detail-item">
                <div class="detail-item-label"><?php echo t('location'); ?></div>
                <div class="detail-item-value" id="detail-city"></div>
              </div>
              <div class="detail-item">
                <div class="detail-item-label"><?php echo t('price'); ?></div>
                <div class="detail-item-value" id="detail-price"></div>
              </div>
              <div class="detail-item">
                <div class="detail-item-label"><?php echo t('start'); ?></div>
                <div class="detail-item-value" id="detail-start"></div>
              </div>
              <div class="detail-item">
                <div class="detail-item-label"><?php echo t('end'); ?></div>
                <div class="detail-item-value" id="detail-end"></div>
              </div>
              <div class="detail-item">
                <div class="detail-item-label"><?php echo t('animal'); ?></div>
                <div class="detail-item-value" id="detail-animal"></div>
              </div>
              <div class="detail-item">
                <div class="detail-item-label"><?php echo t('breed'); ?></div>
                <div class="detail-item-value" id="detail-race"></div>
              </div>
            </div>

            <!-- User Contact Section -->
            <div class="detail-section" style="margin-top: 30px;">
              <h3><?php echo t('contact_owner'); ?></h3>
              <div class="user-info">
                <div class="user-avatar" id="user-avatar">?</div>
                <div class="user-details">
                  <h4><a href="javascript:void(0)" id="user-name-link" style="color: #333; text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'"><?php echo t('user'); ?></a></h4>
                  <p id="user-phone"></p>
                  <div class="user-contact">
                    <?php if (isset($_SESSION['user_id'])): ?>
                      <form method="POST" action="" style="display: inline;">
                        <input type="hidden" name="ad_id" id="acceptAdId" value="">
                        <button type="submit" name="accept_ad" class="btn-contact btn-contact-primary" id="acceptBtn">
                          <i class="fas fa-check"></i> <?php echo t('accept'); ?>
                        </button>
                      </form>
                    <?php else: ?>
                      <a href="<?php echo $base_url; ?>app/Views/log_in.php" class="btn-contact btn-contact-primary">
                        <i class="fas fa-sign-in-alt"></i> <?php echo t('sign_in'); ?>
                      </a>
                    <?php endif; ?>
                    <a href="#" id="btn-message" class="btn-contact btn-contact-secondary">
                      <i class="fas fa-envelope"></i> <?php echo t('send_message'); ?>
                    </a>
                    <a href="#" id="btn-phone" class="btn-contact btn-contact-secondary">
                      <i class="fas fa-phone"></i> <?php echo t('call'); ?>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div id="ad-empty" class="empty-state">
          <h3><i class="fas fa-arrow-left"></i> <?php echo t('select_ad_sidebar'); ?></h3>
          <p><?php echo t('click_ad_details'); ?></p>
        </div>
      </div>
    </div>
  </main>

  <?php include $base_dir . "/app/Views/Components/footer.php"; ?>

  <script>
    // Pass current user ID to JavaScript for booking checks
    const currentUserId = <?php echo isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 'null'; ?>;

    // Pass current language and locale for date formatting
    const currentLanguage = '<?php echo $current_language; ?>';
    const localeMap = {
      'fr': 'fr-FR',
      'en': 'en-US',
      'es': 'es-ES'
    };
    const currentLocale = localeMap[currentLanguage] || 'fr-FR';

    // Translations for JS
    const translations = {
      'home_sitting': '<?php echo t('home_sitting'); ?>',
      'walking': '<?php echo t('walking'); ?>',
      'not_specified': '<?php echo t('not_specified'); ?>',
      'user': '<?php echo t('user'); ?>',
      'not_provided': '<?php echo t('not_provided'); ?>',
      'at': '<?php echo t('at'); ?>'
    };
  </script>
  <script src="<?php echo $base_url; ?>/public/assets/js/advertisements.js"></script>
</body>

</html>