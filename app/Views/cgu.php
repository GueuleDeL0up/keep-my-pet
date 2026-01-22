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

require_once $base_dir . '/app/Models/requests.legal.php';

$cgu = obtenirDerniereCGU();
?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($current_language); ?>">

<head>
  <meta charset="UTF-8">
  <title><?php echo t('cgu_title'); ?></title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/legal.css">
</head>

<body>
  <?php include $base_dir . '/app/Views/Components/header.php'; ?>

  <main class="legal-page">
    <div class="legal-card">
      <h1><?php echo t('cgu_title'); ?></h1>
      <?php if ($cgu): ?>
        <div class="legal-meta"><?php echo t('cgu_last_update'); ?> <?php echo formatDate($cgu['updated_at'] ?? 'now', 'short'); ?></div>
        <div class="legal-content">
          <?php echo nl2br(htmlspecialchars($cgu['contenu'] ?? '', ENT_QUOTES, 'UTF-8')); ?>
        </div>
      <?php else: ?>
        <div class="legal-empty"><?php echo t('cgu_empty'); ?></div>
      <?php endif; ?>
    </div>
  </main>

  <?php include $base_dir . '/app/Views/Components/footer.php'; ?>
</body>

</html>