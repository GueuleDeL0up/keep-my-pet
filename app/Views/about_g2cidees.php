<?php
// Define the base
$base_url = "/keep-my-pet/";  // For HTML links
$base_dir = __DIR__ . "/../../";  // For PHP includes

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Load language system
require_once $base_dir . 'app/Config/language.php';
$current_language = getCurrentLanguage();
?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($current_language); ?>">

<head>
  <meta charset="UTF-8">
  <title><?php echo t('about_g2cidees_title'); ?></title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/about_g2cidees.css">
</head>

<body>
  <?php
  // HEADER
  include $base_dir . "/app/Views/Components/header.php";
  ?>

  <section class="about-container">
    <div class="about-content">
      <div class="about-text">
        <h2><?php echo t('about_g2cidees_title'); ?></h2>
        <p><?php echo t('about_g2cidees_text1'); ?></p>
        <p><?php echo t('about_g2cidees_text2'); ?></p>

        <div class="team-values">
          <div class="value-tag"><?php echo t('team_value_passion'); ?></div>
          <div class="value-tag"><?php echo t('team_value_innovation'); ?></div>
          <div class="value-tag"><?php echo t('team_value_proximity'); ?></div>
        </div>
      </div>

      <div class="about-image">
        <div class="team-photo-wrapper">
          <img src="<?php echo $base_url; ?>/public/assets/images/G2Cidees_Logo.png" alt="<?php echo t('team_photo_alt'); ?>">
        </div>
      </div>
    </div>
  </section>

  <?php
  // FOOTER
  include $base_dir . '/app/Views/Components/footer.php';
  ?>

</body>

</html>