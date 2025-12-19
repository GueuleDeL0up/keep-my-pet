<?php
// Define the base
$base_url = "/keep-my-pet/";  // For HTML links
$base_dir = __DIR__ . "/../../";  // For PHP includes

// HEADER
include $base_dir . "/app/Views/Components/header.php";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>KeepMyPet - À propos de G2Cidées</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/about_g2cidees.css">
</head>

<body>
  <section class="about-container">
    <div class="about-content">
      <div class="about-text">
        <h2>À propos de G2Cidées</h2>
        <p>
          G2Cidées est une équipe de développeurs passionnés, née de l'envie de créer des solutions
          numériques utiles et intuitives. À l'origine du projet <strong>KeepMyPet</strong>,
          notre mission est de mettre la technologie au service du bien-être animal.
        </p>
        <p>
          Spécialisés dans le développement web moderne, nous croyons que chaque ligne de code
          doit contribuer à simplifier la vie des utilisateurs, qu'ils soient à deux ou à quatre pattes.
        </p>

        <div class="team-values">
          <div class="value-tag">Passion</div>
          <div class="value-tag">Innovation</div>
          <div class="value-tag">Proximité</div>
        </div>
      </div>

      <div class="about-image">
        <div class="team-photo-wrapper">
          <img src="<?php echo $base_url; ?>/public/assets/images/G2Cidees_Logo.png" alt="Équipe G2Cidées">
        </div>
      </div>
    </div>
  </section>
</body>

</html>

<?php
// FOOTER
include $base_dir . '/app/Views/Components/footer.php';
?>