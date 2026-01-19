<?php
// Define the base
$base_url = "/keep-my-pet/";  // For HTML links
$base_dir = __DIR__ . "/../../../";  // For PHP includes
?>

<link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/Components/footer.css">

<footer>
  <div class="logo-container">
    <img src="<?php echo $base_url; ?>/public/assets/images/G2Cidees_Logo.png" alt="G2Cidées Logo">
  </div>

  <div class="keepmypet-container">
    <h3>KeepMyPet</h3>
    <a href="<?php echo $base_url; ?>/app/Views/contact.php">Contact</a>
    <a href="<?php echo $base_url; ?>/app/Views/faq.php">FAQ</a>
  </div>

  <div class="g2cidees-container">
    <h3>G2Cidées</h3>
    <a href="<?php echo $base_url; ?>/app/Views/about_g2cidees.php">À propos de G2Cidées</a>
  </div>

  <div class="copyright-container">
    <h3>Copyright</h3>
    <p>©G2Cidées 2025</p>
  </div>
</footer>