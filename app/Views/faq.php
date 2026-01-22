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

// Load FAQ from database
require_once $base_dir . '/app/Models/requests.faq.php';
$faqs = obtenirToutesFAQ();
?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($current_language); ?>">

<head>
  <meta charset="UTF-8">
  <title><?php echo t('faq_title'); ?></title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/faq.css">
</head>

<body>
  <?php
  // HEADER
  include $base_dir . "/app/Views/Components/header.php";
  ?>

  <section class="faq-container">
    <div class="faq-content">
      <h2><?php echo t('faq_title'); ?></h2>
      <p><?php echo t('faq_subtitle'); ?></p>

      <div class="faq-list">
        <?php foreach ($faqs as $faq): ?>
          <details class="faq-item">
            <summary><?php echo htmlspecialchars($faq['question'], ENT_QUOTES, 'UTF-8'); ?></summary>
            <div class="faq-answer">
              <p><?php echo nl2br(htmlspecialchars($faq['reponse'], ENT_QUOTES, 'UTF-8')); ?></p>
            </div>
          </details>
        <?php endforeach; ?>

        <?php if (empty($faqs)): ?>
          <p style="text-align: center; color: #999; padding: 20px;"><?php echo t('faq_empty'); ?></p>
        <?php endif; ?>
      </div>
    </div>
    </div>
  </section>

  <?php include $base_dir . '/app/Views/Components/footer.php'; ?>

</body>

</html>