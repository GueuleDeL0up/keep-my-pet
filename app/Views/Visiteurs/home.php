<?php
// Define the base
$base_url = "/App_dev_web/";  // For HTML links
$base_dir = __DIR__ . "/../../../";  // For PHP includes

// HEADER
include $base_dir . "/app/Views/header.php";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>KeepMyPet - Accueil</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/home.css">
</head>

<body>
  <!-- INTRO -->
  <div class="intro">
    <div class="image">
      <img src="<?php echo $base_url; ?>/public/assets/images/animaux.png" alt="Animaux">
    </div>

    <div class="text">
      KeepMyPet met en relation les propriétaires d'animaux avec des gardiens
      et promeneurs de confiance. Que ce soit pour une balade quotidienne, une
      promenade ou une garde à domicile, nous veillons au bien-être de votre
      compagnon pendant votre absence. Profitez l'esprit tranquille, votre animal
      est entre de bonnes mains.
    </div>
  </div>

  <!-- COMMENT ÇA MARCHE -->
  <div class="how-it-works">
    <div class="text">
      <h2>Comment ça marche ?</h2>
      <ul>
        <li>Inscrivez votre animal avec ses besoins et habitudes.</li>
        <li>Choisissez un gardien ou promeneur et sélectionnez celui qui convient.</li>
        <li>Profitez l'esprit tranquille et recevez des nouvelles de votre compagnon.</li>
      </ul>
    </div>

    <div class="image">
      <img src="<?php echo $base_url; ?>/public/assets/images/question.png" alt="Question">
    </div>
  </div>

</body>

</html>

<?php
// FOOTER
include $base_dir . '/app/Views/footer.php';
?>

<style>
  /* Debugging Borders */
  * {
    border: 1px solid red;
  }
</style>