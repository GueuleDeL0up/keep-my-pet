<?php
// Define the base
$base_url = "/keep-my-pet/";  // For HTML links
$base_dir = __DIR__ . "/../../";  // For PHP includes

// Start session and load language
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once $base_dir . 'app/Config/language.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KeepMyPet - Accueil</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/home.css">
</head>

<body>
  <?php
  // HEADER
  include $base_dir . "/app/Views/Components/header.php";
  ?>
  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-content">
      <h1><?php echo t('welcome'); ?></h1>
      <p><?php echo t('home_subtitle'); ?></p>
      <div class="cta-buttons">
        <a href="<?php echo $base_url; ?>/app/Views/sign_up.php" class="btn-primary"><?php echo t('sign_up_now'); ?></a>
        <a href="<?php echo $base_url; ?>/app/Views/advertisements.php" class="btn-secondary"><?php echo t('view_ads'); ?></a>
      </div>
    </div>
  </section>

  <!-- About Section -->
  <section class="about">
    <div class="about-text">
      <h2><?php echo t('about_us_title'); ?></h2>
      <p><?php echo t('about_us_text1'); ?></p>
      <p><?php echo t('about_us_text2'); ?></p>
      <p><?php echo t('about_us_text3'); ?></p>
    </div>
    <div class="about-image">
      <img src="<?php echo $base_url; ?>/public/assets/images/animals.png" alt="<?php echo t('our_services'); ?>">
    </div>
  </section>

  <!-- Services Section -->
  <section class="services">
    <h2><?php echo t('our_services'); ?></h2>
    <div class="services-grid">
      <div class="service-card">
        <div class="service-icon">🐕</div>
        <h3><?php echo t('daily_walk'); ?></h3>
        <p><?php echo t('walk_desc'); ?></p>
      </div>
      <div class="service-card">
        <div class="service-icon">🏠</div>
        <h3><?php echo t('home_care'); ?></h3>
        <p><?php echo t('care_desc'); ?></p>
      </div>
      <div class="service-card">
        <div class="service-icon">❤️</div>
        <h3><?php echo t('custom_care'); ?></h3>
        <p><?php echo t('custom_desc'); ?></p>
      </div>
    </div>
  </section>

  <!-- How It Works -->
  <section class="how-it-works">
    <div class="how-it-works-text">
      <h2><?php echo t('how_it_works'); ?></h2>
      <ul>
        <li><?php echo t('how_step1'); ?></li>
        <li><?php echo t('how_step2'); ?></li>
        <li><?php echo t('how_step3'); ?></li>
        <li><?php echo t('how_step4'); ?></li>
      </ul>
    </div>
    <img src="<?php echo $base_url; ?>/public/assets/images/question.png" alt="<?php echo t('how_it_works'); ?>">
  </section>

  <?php
  // FOOTER
  include $base_dir . '/app/Views/Components/footer.php';
  ?>

</body>

</html>