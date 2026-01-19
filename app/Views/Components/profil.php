<?php
// Define the base
$base_url = "/keep-my-pet/";  // For HTML links
$base_dir = __DIR__ . "/../../";  // For PHP includes

// Check if user is admin (you need to set this based on your session/auth)
$isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin' ? true : false;

// User data 
$user = [
  'name' => 'Jean Dupont',
  'rating' => 5,
  'reviews' => 18,
  'address' => 'Paris',
  'account_date' => '02/08/2024',
  'avatar' => $base_url . 'public/assets/images/avatar_default.jpg'
];
?>

<div class="profile-page-container">
  <!-- Profile User Section -->
  <div class="profil-user">
    <div class="profil-user-bg"></div>
    <div class="profil-user-content">
      <img src="<?php echo $user['avatar']; ?>" alt="Avatar" class="profil-user-avatar">
      <div class="profil-user-info">
        <h1 class="profil-user-name"><?php echo htmlspecialchars($user['name']); ?></h1>
        <div class="profil-user-rating">
          <span class="stars">★ <?php echo $user['rating']; ?></span>
          <span class="reviews">(<?php echo $user['reviews']; ?> avis)</span>
        </div>
      </div>
      <div class="profil-user-actions">
        <button class="btn btn-primary">Suivre</button>
        <div class="options-container">
          <button class="btn btn-secondary options-btn" title="Plus d'options" onclick="toggleOptionsMenu()">
            <img src="<?php echo $base_url; ?>public/assets/images/option.png" alt="Options">
          </button>
          <!-- Options Menu Popup -->
          <div class="options-menu" id="optionsMenu">
            <button class="options-menu-item" onclick="reportUser()">
              <span class="options-menu-icon">⚠️</span>
              <span class="options-menu-text">Signaler l'utilisateur</span>
            </button>
            <?php if ($isAdmin): ?>
              <button class="options-menu-item options-menu-item-danger" onclick="deleteAccount()">
                <span class="options-menu-icon">🗑️</span>
                <span class="options-menu-text">Supprimer le compte</span>
              </button>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Profile Info Section -->
  <div class="profile-info-section">
    <div class="profile-info-grid">
      <div class="info-card">
        <div class="info-icon">📍</div>
        <div class="info-content">
          <span class="info-label">Localisation</span>
          <span class="info-value"><?php echo htmlspecialchars($user['address']); ?></span>
        </div>
      </div>
      <div class="info-card">
        <div class="info-icon">📅</div>
        <div class="info-content">
          <span class="info-label">Membre depuis</span>
          <span class="info-value"><?php echo htmlspecialchars($user['account_date']); ?></span>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?php echo $base_url; ?>public/assets/js/Components/profil.js"></script>