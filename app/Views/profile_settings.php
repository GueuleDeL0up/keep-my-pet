<?php
$base_url = "/keep-my-pet/";
$base_dir = dirname(__DIR__, 2) . "/";  // /Applications/MAMP/htdocs/keep-my-pet/

// Boot
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Load language system
require_once $base_dir . 'app/Config/language.php';

// Require DB & model
require_once $base_dir . 'app/Models/connection_db.php';
require_once $base_dir . 'app/Models/requests.users.php';
require_once $base_dir . 'app/Controller/ProfileController.php';

// Handle POST and load user
$result = ProfileController::handle($db, $base_dir, $base_url);
$user = $result['user'] ?? null;
$history = $result['history'] ?? [];

// CSRF token
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

// If saved flag present, show toast via JS
$saved = !empty($_GET['saved']);

// HEADER
include $base_dir . "/app/Views/Components/header.php";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>⚙️ <?php echo t('settings'); ?> - <?php echo t('account'); ?></title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/profile_settings.css">
</head>

<body>

  <main class="settings-page">
    <aside class="settings-sidebar">
      <div class="logo-small">KeepMyPet</div>
      <nav>
        <ul>
          <li class="active" data-tab="compte"><span class="icon">👤</span> <?php echo t('account'); ?></li>
          <li data-tab="securite"><span class="icon">🔒</span> <?php echo t('security'); ?></li>
          <li data-tab="historique"><span class="icon">🕘</span> <?php echo t('history'); ?></li>
          <li data-tab="preferences"><span class="icon">⚙️</span> <?php echo t('preferences'); ?></li>
        </ul>
      </nav>
    </aside>

    <section class="settings-main">
      <div class="settings-header">
        <h1>⚙️ <?php echo t('settings'); ?></h1>
      </div>

      <section id="compte">
        <h2><?php echo t('account'); ?></h2>
        <p class="lead"><?php echo t('manage_account_info'); ?></p>

        <?php if (!empty($result['errors'])): ?>
          <div class="settings-card" style="background:#ffdddd;color:#5a0000;margin-bottom:16px;padding:14px;border-radius:12px">
            <strong><?php echo t('errors_plural'); ?></strong>
            <ul>
              <?php foreach ($result['errors'] as $err): ?>
                <li><?php echo htmlspecialchars($err); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <div class="settings-card">
          <form id="accountForm" method="post" action="<?php echo $base_url; ?>/app/Views/profile_settings.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <div class="two-col">
              <div class="col">
                <label><?php echo t('email'); ?></label>
                <input type="email" name="email" placeholder="<?php echo t('email'); ?>" value="<?php echo htmlspecialchars($_POST['email'] ?? ($user['email'] ?? '')); ?>">

                <label><?php echo t('last_name'); ?></label>
                <input type="text" name="last_name" placeholder="Dupont" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ($user['last_name'] ?? '')); ?>">

                <label><?php echo t('first_name'); ?></label>
                <input type="text" name="first_name" placeholder="Jean" value="<?php echo htmlspecialchars($_POST['first_name'] ?? ($user['first_name'] ?? '')); ?>">

                <label><?php echo t('phone'); ?></label>
                <input type="text" name="phone_number" placeholder="06 12 34 56 78" value="<?php echo htmlspecialchars($_POST['phone_number'] ?? ($user['phone_number'] ?? '')); ?>">
              </div>

              <div class="col">
                <label><?php echo t('address'); ?></label>
                <input type="text" name="address" placeholder="10 rue de Rivoli" value="<?php echo htmlspecialchars($_POST['address'] ?? ($user['address'] ?? '')); ?>">

                <label><?php echo t('postal_code'); ?></label>
                <input type="text" name="postal_code" placeholder="75001" value="<?php echo htmlspecialchars($_POST['postal_code'] ?? ($user['postal_code'] ?? '')); ?>">
              </div>
            </div>

            <div class="form-actions">
              <button type="submit" id="saveBtn" class="btn primary"><?php echo t('save_changes'); ?></button>
            </div>
          </form>
        </div>
      </section>

      <section class="hidden" id="securite">
        <h3><?php echo t('security'); ?></h3>
        <p><?php echo t('manage_password_session'); ?></p>
        <div class="settings-card">
          <form id="passwordForm" method="post" action="<?php echo $base_url; ?>/app/Views/profile_settings.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="action" value="change_password">

            <label><?php echo t('current_password'); ?></label>
            <input type="password" name="current_password" placeholder="<?php echo t('current_password'); ?>" required>

            <label><?php echo t('new_password'); ?></label>
            <input type="password" name="new_password" placeholder="<?php echo t('new_password'); ?>" required>

            <label><?php echo t('confirm_password'); ?></label>
            <input type="password" name="new_password_confirm" placeholder="<?php echo t('confirm_password'); ?>" required>

            <div class="form-actions"><button type="submit" id="passwordSaveBtn" class="btn primary"><?php echo t('update_password'); ?></button></div>
          </form>
        </div>
      </section>

      <section class="hidden" id="historique">
        <h3><?php echo t('history_title'); ?></h3>
        <p><?php echo t('history_description'); ?></p>
        <div class="settings-card">
          <?php if (empty($history)): ?>
            <p><?php echo t('no_finished_bookings'); ?></p>
          <?php else: ?>
            <div class="history-list">
              <?php foreach ($history as $item):
                $role_label = $item['role'] === 'sitter' ? t('sitter') : t('owner');
                $counterpart = $item['role'] === 'sitter'
                  ? ($item['owner_first_name'] . ' ' . $item['owner_last_name'])
                  : ($item['sitter_first_name'] . ' ' . $item['sitter_last_name']);
                $counterpart_id = $item['role'] === 'sitter' ? (int)$item['owner_id'] : (int)$item['sitter_id'];
                $start_hour = is_object($item['start_hour']) ? $item['start_hour']->format('H:i') : $item['start_hour'];
                $end_hour = is_object($item['end_hour']) ? $item['end_hour']->format('H:i') : $item['end_hour'];
              ?>
                <div class="history-row">
                  <div class="history-main">
                    <div class="history-title"><?php echo htmlspecialchars($item['ad_title']); ?></div>
                    <div class="history-meta">
                      <span class="badge"><?php echo t('finished'); ?></span>
                      <span><?php echo $role_label; ?> ?? <?php echo t('with'); ?>
                        <a class="history-link" href="<?php echo $base_url; ?>/app/Views/user_profile.php?id=<?php echo $counterpart_id; ?>">
                          <?php echo htmlspecialchars($counterpart); ?>
                        </a>
                      </span>
                      <span><?php echo htmlspecialchars($item['animal_name']); ?></span>
                      <span><?php echo formatDate($item['start_date'], 'short'); ?> <?php echo $start_hour; ?>
                        ??? <?php echo formatDate($item['end_date'], 'short'); ?> <?php echo $end_hour; ?></span>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </section>

      <section class="hidden" id="preferences">
        <h3><?php echo t('preferences'); ?></h3>
        <div class="settings-card">
          <form method="POST" action="<?php echo $base_url; ?>app/API/changeLanguage.php">
            <label><?php echo t('language'); ?></label>
            <select name="lang" id="language-select" onchange="this.form.submit();">
              <option value="fr" <?php echo $current_language === 'fr' ? 'selected' : ''; ?>>🇫🇷 <?php echo t('french'); ?></option>
              <option value="en" <?php echo $current_language === 'en' ? 'selected' : ''; ?>>🇬🇧 <?php echo t('english'); ?></option>
              <option value="es" <?php echo $current_language === 'es' ? 'selected' : ''; ?>>🇪🇸 <?php echo t('spanish'); ?></option>
            </select>
          </form>

          <label><?php echo t('font'); ?></label>
          <select id="font-select">
            <option value="default"><?php echo t('default_segoe'); ?></option>
            <option value="verdana"><?php echo t('verdana_accessibility'); ?></option>
            <option value="arial">Arial</option>
            <option value="georgia">Georgia</option>
          </select>

          <label><?php echo t('theme'); ?></label>
          <select id="theme-select">
            <option value="light"><?php echo t('light'); ?></option>
            <option value="dark"><?php echo t('dark'); ?></option>
          </select>
          <div class="form-actions">
            <button class="btn" onclick="savePreferences()"><?php echo t('save'); ?></button>
          </div>
        </div>
      </section>

    </section>

  </main>

  <div id="toast" class="toast" aria-hidden="true"><?php echo $saved ? t('save_changes') : t('save'); ?></div>

  <script>
    var PROFILE_SAVED = <?php echo $saved ? 'true' : 'false'; ?>;

    function saveLanguagePreference() {
      var select = document.getElementById("language-select");
      var lang = select.value;
      var base_url = "/keep-my-pet/";

      console.log('Saving language preference:', lang);

      fetch(base_url + 'app/API/changeLanguage.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: 'lang=' + encodeURIComponent(lang)
        })
        .then(response => {
          console.log('Response status:', response.status);
          return response.json();
        })
        .then(data => {
          console.log('Response data:', data);
          if (data.success) {
            console.log('Language saved, reloading...');
            // Reload page to apply language changes
            setTimeout(() => {
              window.location.href = window.location.href;
            }, 500);
          } else {
            console.error('Error:', data.error);
          }
        })
        .catch(error => {
          console.error('Fetch error:', error);
        });
    }
  </script>
  <script src="<?php echo $base_url; ?>/public/assets/js/theme.js"></script>
  <script src="<?php echo $base_url; ?>/public/assets/js/profile_settings.js"></script>

  <?php include $base_dir . '/app/Views/Components/footer.php'; ?>
</body>

</html>