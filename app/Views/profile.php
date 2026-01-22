<?php
// Set timezone to Europe/Paris (France)
date_default_timezone_set('Europe/Paris');

// Define the base
$base_url = "/keep-my-pet/";  // For HTML links
$base_dir = __DIR__ . "/../../";  // For PHP includes

// Start session and require authentication
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Load language system
require_once $base_dir . 'app/Config/language.php';

// Require authentication
if (!isset($_SESSION['user_id'])) {
  header('Location: ' . $base_url . '/app/Views/log_in.php');
  exit;
}

// Include required files
require_once $base_dir . '/app/Models/connection_db.php';
require_once $base_dir . '/app/Models/requests.basics.php';
require_once $base_dir . '/app/Models/requests.users.php';
require_once $base_dir . '/app/Models/requests.animals.php';
require_once $base_dir . '/app/Models/requests.advertisements.php';
require_once $base_dir . '/app/Models/requests.bookings.php';
require_once $base_dir . '/app/Models/requests.reviews.php';

// Get viewer ID
$viewer_id = (int)$_SESSION['user_id'];

// Initialize messages
$add_animal_message = null;
$delete_message = null;
$ad_message = null;
$demande_message = null;

// Get profile user (own profile by default)
$profile_user_id = isset($_GET['id']) ? (int)$_GET['id'] : $viewer_id;
$profile_user = obtenirUtilisateurParId($profile_user_id);

if (!$profile_user) {
  header('Location: ' . $base_url . '/app/Views/home.php');
  exit;
}

$is_own_profile = ($profile_user_id === $viewer_id);

// Load data for own profile
if ($is_own_profile) {
  $animals = obtenirAnimauxUtilisateur($viewer_id);
  $user_ads = obtenirAnnoncesParUtilisateur($viewer_id);
  $demandes_recues = obtenirDemandesRecues($viewer_id);
  $sitter_bookings = obtenirGardesParGardien($viewer_id);
} else {
  $animals = [];
  $user_ads = [];
  $demandes_recues = [];
  $sitter_bookings = [];
}

// Handle accept booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_booking_id'])) {
  $booking_id = (int)$_POST['accept_booking_id'];
  $booking = obtenirDemande($booking_id);
  if ($booking && $booking['owner_id'] == $viewer_id) {
    if (accepterDemande($booking_id)) {
      $demande_message = ['type' => 'success', 'text' => t('booking_accepted')];
      $demandes_recues = obtenirDemandesRecues($viewer_id);
    } else {
      $demande_message = ['type' => 'error', 'text' => t('error_accepting_booking')];
    }
  }
}

// Handle reject booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_booking_id'])) {
  $booking_id = (int)$_POST['reject_booking_id'];
  $booking = obtenirDemande($booking_id);
  if ($booking && $booking['owner_id'] == $viewer_id) {
    if (refuserDemande($booking_id)) {
      $demande_message = ['type' => 'success', 'text' => t('booking_rejected')];
      $demandes_recues = obtenirDemandesRecues($viewer_id);
    } else {
      $demande_message = ['type' => 'error', 'text' => t('error_rejecting_booking')];
    }
  }
}

// Handle delete ad (including expired ones)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_ad_id'])) {
  $ad_id = (int)$_POST['delete_ad_id'];
  // Use the safer function that verifies ownership
  if (supprimerAnnonce($ad_id, $viewer_id)) {
    $ad_message = ['type' => 'success', 'text' => t('ad_deleted_success')];
    // Refresh the ads list after deletion
    $user_ads = obtenirAnnoncesParUtilisateur($viewer_id);
    error_log("Annonce #{$ad_id} supprimée par utilisateur #{$viewer_id}");
  } else {
    $ad_message = ['type' => 'error', 'text' => t('error_deleting_ad')];
    error_log("Échec de la suppression de l'annonce #{$ad_id} par utilisateur #{$viewer_id}");
  }
}

// Handle rating submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rate_ad_id'])) {
  $ad_id = (int)$_POST['rate_ad_id'];
  $rating = (int)$_POST['rating'];
  $comment = trim($_POST['comment'] ?? '');
  $sitter_id = (int)$_POST['sitter_id'];
  $animal_rating = isset($_POST['animal_rating']) ? (int)$_POST['animal_rating'] : null;
  $animal_id = isset($_POST['animal_id']) ? (int)$_POST['animal_id'] : null;

  error_log("DEBUG RATING: ad_id=$ad_id, viewer_id=$viewer_id, sitter_id=$sitter_id, rating=$rating");

  $success = false;

  if ($rating >= 1 && $rating <= 5) {
    // Note the sitter/owner
    $result = creerAvis($ad_id, $viewer_id, $sitter_id, $rating, $comment);
    error_log("DEBUG: creerAvis returned " . ($result ? 'TRUE' : 'FALSE'));

    if ($result) {
      $success = true;

      // If this is a sitter rating and they provided animal rating, also rate the animal
      if ($animal_rating && $animal_id && $animal_rating >= 1 && $animal_rating <= 5) {
        // Insert review for animal
        try {
          global $db;
          $stmt = $db->prepare("
              INSERT INTO reviews (advertisement_id, reviewer_id, reviewed_animal_id, rating, comment) 
              VALUES (?, ?, ?, ?, ?)
            ");
          $stmt->execute([$ad_id, $viewer_id, $animal_id, $animal_rating, $comment]);

          // Update animal's note
          noterAnimal($animal_id, $animal_rating);
        } catch (PDOException $e) {
          error_log("Erreur notation animal: " . $e->getMessage());
        }
      }
    }
  }

  if ($success) {
    $ad_message = ['type' => 'success', 'text' => t('review_added')];
    $user_ads = obtenirAnnoncesParUtilisateur($viewer_id);
  } else {
    $ad_message = ['type' => 'error', 'text' => t('error_adding_review')];
  }
}

// Handle add animal if requested
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && isset($_POST['race'])) {
  // Validate required fields
  $required_fields = ['name', 'race', 'gender', 'birthdate'];
  $has_error = false;

  foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
      $add_animal_message = ['type' => 'error', 'text' => t('all_fields_required')];
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
      $add_animal_message = ['type' => 'error', 'text' => t('invalid_birthdate')];
      $has_error = true;
    }

    // Validate gender
    if (!$has_error && !in_array($animal_data['gender'], ['male', 'female'])) {
      $add_animal_message = ['type' => 'error', 'text' => t('invalid_gender')];
      $has_error = true;
    }

    // Validate lengths
    if (!$has_error && strlen($animal_data['name']) > 50) {
      $add_animal_message = ['type' => 'error', 'text' => t('name_max_length')];
      $has_error = true;
    }

    if (!$has_error && strlen($animal_data['race']) > 50) {
      $add_animal_message = ['type' => 'error', 'text' => t('breed_max_length')];
      $has_error = true;
    }

    if (!$has_error) {
      // Create animal
      $animal_id = creerAnimal($animal_data);
      if ($animal_id) {
        $add_animal_message = ['type' => 'success', 'text' => t('animal_added_success')];
        // Refresh animals list
        $animals = obtenirAnimauxUtilisateur($viewer_id);
      } else {
        $add_animal_message = ['type' => 'error', 'text' => t('error_adding_animal')];
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
      $delete_message = ['type' => 'success', 'text' => t('animal_deleted_success')];
      $animals = obtenirAnimauxUtilisateur($viewer_id);
    } else {
      $delete_message = ['type' => 'error', 'text' => t('error_deleting_animal')];
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
            <span>⭐ <?php echo ($profile_user['note'] == (int)$profile_user['note']) ? (int)$profile_user['note'] : number_format($profile_user['note'], 1); ?>/5 (<?php echo (int)($profile_user['review_count'] ?? 0); ?> <?php echo t('reviews'); ?>)</span>
            <?php if (!empty($profile_user['address'])): ?>
              <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($profile_user['address']); ?></span>
            <?php endif; ?>
            <span>📅 <?php echo t('member_since'); ?> <?php echo date('Y', strtotime($profile_user['created_at'] ?? 'now')); ?></span>
          </div>
        </div>

        <?php if ($is_own_profile): ?>
          <a href="<?php echo $base_url; ?>/app/Views/profile_settings.php" class="btn-edit-profile">
            ⚙️ <?php echo t('settings'); ?>
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
              <h2>🐾 <?php echo t('my_animals'); ?></h2>
              <button class="btn btn-primary" onclick="openAddAnimalModal()">
                <i class="fas fa-plus"></i> <?php echo t('add'); ?>
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
                <h3><?php echo t('no_animals'); ?></h3>
                <p><?php echo t('add_first_animal'); ?></p>
                <button class="btn btn-primary" onclick="openAddAnimalModal()">
                  <i class="fas fa-plus"></i> <?php echo t('add_animal'); ?>
                </button>
              </div>
            <?php else: ?>
              <div class="animals-grid">
                <?php foreach ($animals as $animal): ?>
                  <div class="animal-card">
                    <div class="animal-card-top">
                      <h3><?php echo htmlspecialchars($animal['name']); ?></h3>
                      <div class="animal-actions">
                        <a href="<?php echo $base_url; ?>/app/Views/edit_animal.php?id=<?php echo (int)$animal['id']; ?>" class="btn-icon" title="<?php echo t('edit'); ?>">
                          <i class="fas fa-pencil"></i>
                        </a>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('<?php echo t('confirm_delete'); ?>');">
                          <input type="hidden" name="delete_animal_id" value="<?php echo (int)$animal['id']; ?>">
                          <button type="submit" class="btn-icon btn-delete" title="<?php echo t('delete'); ?>">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
                      </div>
                    </div>
                    <div class="animal-details">
                      <p><strong><?php echo htmlspecialchars($animal['race']); ?></strong></p>
                      <p><?php echo $animal['gender'] === 'male' ? '♂ ' . t('male') : '♀ ' . t('female'); ?></p>
                      <p><?php echo t('age'); ?>: <?php
                                                  $birthdate = new DateTime($animal['birthdate']);
                                                  $today = new DateTime('today');
                                                  $age = $birthdate->diff($today)->y;
                                                  echo $age . ' an' . ($age > 1 ? 's' : '');
                                                  ?></p>
                    </div>
                    <a href="<?php echo $base_url; ?>/app/Views/create_advertisement.php?animal_id=<?php echo (int)$animal['id']; ?>" class="btn btn-secondary btn-block">
                      <i class="fas fa-bullhorn"></i> <?php echo t('create_ad'); ?>
                    </a>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </section>
        <?php endif; ?>

        <!-- Booking Requests Section -->
        <?php if ($is_own_profile && !empty($demandes_recues)): ?>
          <section class="section">
            <div class="section-header">
              <h2><i class="fas fa-inbox"></i> <?php echo t('booking_requests'); ?></h2>
            </div>

            <?php if ($demande_message): ?>
              <div class="alert alert-<?php echo $demande_message['type']; ?>">
                <i class="fas fa-<?php echo $demande_message['type'] === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo htmlspecialchars($demande_message['text']); ?>
              </div>
            <?php endif; ?>

            <div class="bookings-list">
              <?php foreach ($demandes_recues as $demande):
                $status_color = $demande['status'] === 'pending' ? 'pending' : ($demande['status'] === 'accepted' ? 'accepted' : 'rejected');
              ?>
                <div class="booking-card booking-<?php echo $status_color; ?>">
                  <div class="booking-header">
                    <h3><?php echo htmlspecialchars($demande['ad_title']); ?></h3>
                    <span class="booking-status status-<?php echo $demande['status']; ?>">
                      <?php
                      $status_text = [
                        'pending' => '⛳ ' . t('pending'),
                        'accepted' => '✅ ' . t('accept'),
                        'rejected' => '❌ Refusée'
                      ];
                      echo $status_text[$demande['status']] ?? 'Inconnue';
                      ?>
                    </span>
                  </div>

                  <div class="booking-info">
                    <p><strong><?php echo t('sitter'); ?>:</strong> <?php echo htmlspecialchars($demande['sitter_first_name'] . ' ' . $demande['sitter_last_name']); ?></p>
                    <p><strong><?php echo t('animal'); ?>:</strong> <?php echo htmlspecialchars($demande['animal_name']); ?></p>
                    <p><strong><?php echo t('date'); ?>:</strong> <?php echo formatDate($demande['start_date'], 'short'); ?> à <?php echo $demande['start_hour']; ?> - <?php echo formatDate($demande['end_date'], 'short'); ?> à <?php echo $demande['end_hour']; ?></p>
                    <p><strong><?php echo t('contact_owner'); ?>:</strong> <?php echo htmlspecialchars($demande['sitter_email']); ?> | <?php echo htmlspecialchars($demande['sitter_phone']); ?></p>
                  </div>

                  <?php if ($demande['status'] === 'pending'): ?>
                    <div class="booking-actions">
                      <form method="POST" style="display: inline;">
                        <input type="hidden" name="accept_booking_id" value="<?php echo (int)$demande['id']; ?>">
                        <button type="submit" class="btn btn-success btn-sm">
                          <i class="fas fa-check"></i> <?php echo t('accept'); ?>
                        </button>
                      </form>
                      <form method="POST" style="display: inline;">
                        <input type="hidden" name="reject_booking_id" value="<?php echo (int)$demande['id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm">
                          <i class="fas fa-times"></i> <?php echo t('reject'); ?>
                        </button>
                      </form>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          </section>
        <?php endif; ?>

        <!-- Gardes acceptées (en tant que gardien) -->
        <?php if ($is_own_profile && !empty($sitter_bookings)): ?>
          <section class="section">
            <div class="section-header">
              <h2>✅ <?php echo t('my_bookings'); ?></h2>
            </div>

            <div class="bookings-list">
              <?php foreach ($sitter_bookings as $booking):
                $end_datetime = strtotime($booking['end_date'] . ' ' . ($booking['end_hour'] ?? '00:00'));
                $is_finished = $end_datetime && $end_datetime < time();
                $already_rated = aDejaNote($booking['advertisement_id'], $viewer_id);
              ?>
                <div class="booking-card booking-accepted">
                  <div class="booking-header">
                    <h3><?php echo htmlspecialchars($booking['ad_title']); ?></h3>
                    <?php if ($is_finished): ?>
                      <span class="booking-status status-finished"><?php echo t('finished'); ?></span>
                    <?php else: ?>
                      <span class="booking-status status-accepted">En cours</span>
                    <?php endif; ?>
                  </div>

                  <div class="booking-info">
                    <p><strong><?php echo t('owner'); ?>:</strong> <?php echo htmlspecialchars($booking['owner_first_name'] . ' ' . $booking['owner_last_name']); ?></p>
                    <p><strong><?php echo t('animal'); ?>:</strong> <?php echo htmlspecialchars($booking['animal_name']); ?></p>
                    <p><strong><?php echo t('date'); ?>:</strong> <?php echo formatDate($booking['start_date'], 'short'); ?> à <?php echo $booking['start_hour']; ?> - <?php echo formatDate($booking['end_date'], 'short'); ?> à <?php echo $booking['end_hour']; ?></p>
                  </div>

                  <?php if ($is_finished): ?>
                    <div class="booking-actions">
                      <?php if (!$already_rated): ?>
                        <button class="btn btn-primary btn-sm" onclick="openRatingModal(<?php echo (int)$booking['advertisement_id']; ?>, <?php echo (int)$booking['owner_id']; ?>, '<?php echo htmlspecialchars(addslashes($booking['ad_title'])); ?>', <?php echo (int)$booking['animal_id']; ?>, true)">
                          <i class="fas fa-star"></i> Note r le propriétaire
                        </button>
                      <?php else: ?>
                        <span class=\"rated-badge\"><i class=\"fas fa-check\"></i> <?php echo t('review_sent'); ?></span>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          </section>
        <?php endif; ?>

        <!-- Advertisements Section -->
        <?php if ($is_own_profile): ?>
          <section class="section">
            <div class="section-header">
              <h2>📋 <?php echo t('my_ads'); ?></h2>
            </div>

            <?php if ($ad_message): ?>
              <div class="alert alert-<?php echo $ad_message['type']; ?>">
                <i class="fas fa-<?php echo $ad_message['type'] === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo htmlspecialchars($ad_message['text']); ?>
              </div>
            <?php endif; ?>

            <?php if (empty($user_ads)): ?>
              <div class="empty-state">
                <div class="empty-icon">📋</div>
                <h3><?php echo t('no_ads'); ?></h3>
                <p><?php echo t('add_first_animal'); ?></p>
                <a href="<?php echo $base_url; ?>/app/Views/create_advertisement.php" class="btn btn-primary">
                  <i class="fas fa-plus"></i> <?php echo t('post_ad'); ?>
                </a>
              </div>
            <?php else: ?>
              <div class="ads-grid">
                <?php foreach ($user_ads as $ad):
                  // Check if ad is finished by comparing date + time
                  $end_hour = $ad['end_hour'] ?? '00:00';

                  // Handle if end_hour is an object (DateTime from PDO)
                  if (is_object($end_hour)) {
                    $end_hour = $end_hour->format('H:i:s');
                  }

                  $end_datetime = strtotime($ad['end_date'] . ' ' . $end_hour);
                  $current_time = time();
                  $is_finished = $end_datetime && $end_datetime < $current_time;

                  $can_rate = $is_finished && !empty($ad['user_id']) && $ad['user_id'] != $viewer_id;
                  $already_rated = $can_rate && aDejaNote($ad['id'], $viewer_id);
                  $is_owner = ($ad['user_id'] == $viewer_id);
                ?>
                  <div class="ad-card <?php echo $is_finished ? 'finished' : ''; ?>">
                    <div class="ad-card-header">
                      <h3><?php echo htmlspecialchars($ad['title']); ?></h3>
                      <span class="ad-type <?php echo htmlspecialchars($ad['type']); ?>">
                        <?php echo $ad['type'] === 'gardiennage' ? t('home_sitting') : t('daily_walk'); ?>
                      </span>
                    </div>
                    <p class="ad-description"><?php echo htmlspecialchars($ad['description']); ?></p>
                    <div class="ad-meta">
                      <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($ad['city']); ?></span>
                      <span><i class="fas fa-euro-sign"></i> <?php echo number_format($ad['price'], 2); ?>€</span>
                    </div>
                    <div class="ad-meta">
                      <span><i class="fas fa-calendar"></i> <?php echo formatDate($ad['start_date'], 'short'); ?> à <?php echo $ad['start_hour']; ?> - <?php echo formatDate($ad['end_date'], 'short'); ?> à <?php echo $ad['end_hour']; ?></span>
                    </div>

                    <?php if ($is_finished): ?>
                      <div class="ad-status-container" style="display: flex; align-items: center; gap: 10px; margin: 10px 0;">
                        <div class="ad-status finished-badge">
                          <i class="fas fa-check-circle"></i> <?php echo t('finished'); ?>

                          <?php
                          // Check if there's an accepted sitter for this ad
                          $accepted_sitter = obtenirGardienAccepte($ad['id']);
                          if ($accepted_sitter && $is_owner) {
                            // Check if owner already rated this sitter for this ad
                            $already_rated_sitter = aDejaNote($ad['id'], $viewer_id);
                            if (!$already_rated_sitter): ?>
                              <button class="btn btn-primary btn-sm" onclick="openRatingModal(<?php echo (int)$ad['id']; ?>, <?php echo (int)$accepted_sitter['sitter_id']; ?>, '<?php echo htmlspecialchars(addslashes($ad['title'])); ?>', <?php echo (int)$ad['animal_id']; ?>, false)" style="margin: 0;">
                                <i class="fas fa-star"></i> Noter le gardien
                              </button>
                            <?php else: ?>
                              <span class="rated-badge"><i class="fas fa-check"></i> Gardien noté</span>
                          <?php endif;
                          }
                          ?>
                        </div>
                      <?php endif; ?>

                      <div class="ad-actions">
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Supprimer cette annonce ?');">
                          <input type="hidden" name="delete_ad_id" value="<?php echo (int)$ad['id']; ?>">
                          <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i> Supprimer
                          </button>
                        </form>

                        <?php if ($can_rate && !$already_rated): ?>
                          <?php
                          // Determine if viewer is the owner or a sitter
                          if ($is_owner) {
                            // Owner is viewing and ad is finished - can rate the sitter
                            $button_text = "Noter le gardien";
                          } else {
                            // Sitter can rate the owner and animal
                            $button_text = "Noter l'expérience";
                          }
                          ?>
                          <button class="btn btn-primary btn-sm" onclick="openRatingModal(<?php echo (int)$ad['id']; ?>, <?php echo (int)$ad['user_id']; ?>, '<?php echo htmlspecialchars(addslashes($ad['title'])); ?>', <?php echo (int)$ad['animal_id']; ?>, <?php echo $is_owner ? 'false' : 'true'; ?>)">
                            <i class="fas fa-star"></i> <?php echo $button_text; ?>
                          </button>
                        <?php elseif ($already_rated): ?>
                          <span class="rated-badge"><i class="fas fa-check"></i> <?php echo t('already_rated'); ?></span>
                        <?php endif; ?>
                      </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
          </section>
        <?php endif; ?>

      </div>

      <!-- Sidebar -->
      <aside class="profile-sidebar">
        <div class="sidebar-card">
          <h3><?php echo t('about_profile'); ?></h3>
          <p><?php echo sprintf(t('about_user_text'), htmlspecialchars($profile_user['first_name'])); ?></p>
        </div>

        <div class="sidebar-card">
          <h3><?php echo t('services_title'); ?></h3>
          <ul>
            <li>🏠 <?php echo t('home_sitting'); ?></li>
            <li>🚶 <?php echo t('walking'); ?></li>
          </ul>
        </div>

        <?php if (!$is_own_profile): ?>
          <div class="sidebar-card">
            <button class="btn btn-primary btn-block">
              <i class="fas fa-envelope"></i> <?php echo t('contact'); ?>
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
        <h1><?php echo t('add_animal'); ?></h1>
        <button class="modal-close" onclick="closeAddAnimalModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="modal-body">
        <p class="modal-subtitle"><?php echo t('add_pet_subtitle'); ?></p>

        <?php if ($add_animal_message): ?>
          <div class="alert alert-<?php echo $add_animal_message['type']; ?>">
            <i class="fas fa-<?php echo $add_animal_message['type'] === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <?php echo htmlspecialchars($add_animal_message['text']); ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="" class="modal-form" id="addAnimalForm">
          <div class="form-row">
            <div class="form-group">
              <label for="modal_name"><?php echo t('name'); ?></label>
              <input type="text" id="modal_name" name="name" placeholder="Ex: Rocky" required maxlength="50">
              <div class="help-text"><?php echo t('max_50_chars'); ?></div>
            </div>
            <div class="form-group">
              <label for="modal_race"><?php echo t('breed_type'); ?></label>
              <input type="text" id="modal_race" name="race" placeholder="Ex: Golden Retriever" required maxlength="50">
              <div class="help-text"><?php echo t('max_50_chars'); ?></div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="modal_gender"><?php echo t('gender'); ?></label>
              <select id="modal_gender" name="gender" required>
                <option value=""><?php echo t('select_option'); ?></option>
                <option value="male"><?php echo t('male'); ?></option>
                <option value="female"><?php echo t('female'); ?></option>
              </select>
            </div>
            <div class="form-group">
              <label for="modal_birthdate">Date de naissance</label>
              <input type="date" id="modal_birthdate" name="birthdate" required>
            </div>
          </div>

          <div class="form-buttons">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-plus"></i> <?php echo t('add_animal'); ?>
            </button>
            <button type="button" class="btn btn-secondary" onclick="closeAddAnimalModal()"><?php echo t('cancel'); ?></button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Rating (hidden, opens via JavaScript) -->
  <div class="modal-overlay" id="ratingModal" style="display: none;">
    <div class="modal-content">
      <div class="modal-header">
        <h1 id="ratingModalTitle">Noter le gardien</h1>
        <button class="modal-close" onclick="closeRatingModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="modal-body">
        <p class="modal-subtitle" id="ratingAdTitle"></p>

        <form method="POST" action="" class="modal-form" id="ratingForm">
          <input type="hidden" name="rate_ad_id" id="rate_ad_id">
          <input type="hidden" name="sitter_id" id="sitter_id">
          <input type="hidden" name="animal_id" id="animal_id">

          <div class="form-group">
            <label>Note du gardien (sur 5)</label>
            <div class="rating-input">
              <input type="radio" name="rating" value="5" id="star5" required>
              <label for="star5"><i class="fas fa-star"></i></label>
              <input type="radio" name="rating" value="4" id="star4">
              <label for="star4"><i class="fas fa-star"></i></label>
              <input type="radio" name="rating" value="3" id="star3">
              <label for="star3"><i class="fas fa-star"></i></label>
              <input type="radio" name="rating" value="2" id="star2">
              <label for="star2"><i class="fas fa-star"></i></label>
              <input type="radio" name="rating" value="1" id="star1">
              <label for="star1"><i class="fas fa-star"></i></label>
            </div>
          </div>

          <!-- Animal rating section (shown only for sitters) -->
          <div id="animalRatingSection" style="display: none;">
            <div class="form-group">
              <label>Note de l'animal (sur 5)</label>
              <div class="rating-input">
                <input type="radio" name="animal_rating" value="5" id="animal_star5">
                <label for="animal_star5"><i class="fas fa-star"></i></label>
                <input type="radio" name="animal_rating" value="4" id="animal_star4">
                <label for="animal_star4"><i class="fas fa-star"></i></label>
                <input type="radio" name="animal_rating" value="3" id="animal_star3">
                <label for="animal_star3"><i class="fas fa-star"></i></label>
                <input type="radio" name="animal_rating" value="2" id="animal_star2">
                <label for="animal_star2"><i class="fas fa-star"></i></label>
                <input type="radio" name="animal_rating" value="1" id="animal_star1">
                <label for="animal_star1"><i class="fas fa-star"></i></label>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="comment">Commentaire (optionnel)</label>
            <textarea name="comment" id="comment" rows="4" placeholder="Partagez votre expérience..."></textarea>
          </div>

          <div class="modal-actions">
            <button type="button" class="btn btn-secondary" onclick="closeRatingModal()">Annuler</button>
            <button type="submit" class="btn btn-primary">Envoyer la note</button>
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

    // Rating modal functions
    function openRatingModal(adId, sitterId, adTitle, animalId, isSitter) {
      document.getElementById('rate_ad_id').value = adId;
      document.getElementById('sitter_id').value = sitterId;
      document.getElementById('animal_id').value = animalId || '';
      document.getElementById('ratingAdTitle').textContent = 'Annonce: ' + adTitle;

      const animalRatingSection = document.getElementById('animalRatingSection');
      if (isSitter && animalId) {
        // Sitter is rating - show animal rating field
        animalRatingSection.style.display = 'block';
      } else {
        // Owner is rating - hide animal rating field
        animalRatingSection.style.display = 'none';
      }

      document.getElementById('ratingModal').style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }

    function closeRatingModal() {
      document.getElementById('ratingModal').style.display = 'none';
      document.body.style.overflow = 'auto';
      document.getElementById('ratingForm').reset();
    }

    // Close modal when clicking outside
    document.getElementById('ratingModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeRatingModal();
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