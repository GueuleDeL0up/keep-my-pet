<?php
// Define the base
$base_url = "/keep-my-pet/";  // For HTML links
$base_dir = __DIR__ . "/../../../";  // For PHP includes

// Load language if not already loaded
if (!function_exists('t')) {
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
  require_once $base_dir . 'app/Config/language.php';
}
?>

<link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/Components/footer.css">

<footer>
  <div class="footer-inner">
    <div class="logo-container">
      <img src="<?php echo $base_url; ?>/public/assets/images/G2Cidees_Logo.png" alt="G2Cidées Logo">
    </div>

    <div class="keepmypet-container">
      <h3>KeepMyPet</h3>
      <a href="<?php echo $base_url; ?>/app/Views/contact.php"><?php echo t('contact'); ?></a>
      <a href="<?php echo $base_url; ?>/app/Views/faq.php"><?php echo t('faq'); ?></a>
      <a href="<?php echo $base_url; ?>/app/Views/cgu.php"><?php echo t('cgu'); ?></a>
      <a href="<?php echo $base_url; ?>/app/Views/mentions_legales.php"><?php echo t('mentions_legales'); ?></a>
    </div>

    <div class="g2cidees-container">
      <h3>G2Cidées</h3>
      <a href="<?php echo $base_url; ?>/app/Views/about_g2cidees.php"><?php echo t('about'); ?></a>
    </div>

    <div class="copyright-container">
      <h3><?php echo t('copyright'); ?></h3>
      <p>©G2Cidées 2025</p>
    </div>
  </div>
</footer>