<?php
// Define the base
$base_url = "/keep-my-pet/";  // For HTML links
$base_dir = __DIR__ . "/../../";  // For PHP includes
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
      <h1>Bienvenue chez KeepMyPet</h1>
      <p>La plateforme de confiance pour faire garder votre animal de compagnie par des experts bienveillants</p>
      <div class="cta-buttons">
        <a href="<?php echo $base_url; ?>/app/Views/sign_up.php" class="btn-primary">S'inscrire maintenant</a>
        <a href="<?php echo $base_url; ?>/app/Views/advertisements.php" class="btn-secondary">Voir les annonces</a>
      </div>
    </div>
  </section>

  <!-- About Section -->
  <section class="about">
    <div class="about-text">
      <h2>Qui sommes-nous ?</h2>
      <p>
        KeepMyPet met en relation les propriétaires d'animaux avec des gardiens
        et promeneurs de confiance.
      </p>
      <p>
        Que ce soit pour une balade quotidienne, une promenade ou une garde à domicile,
        nous veillons au bien-être de votre compagnon pendant votre absence.
      </p>
      <p>
        Profitez l'esprit tranquille, votre animal est entre de bonnes mains.
      </p>
    </div>
    <div class="about-image">
      <img src="<?php echo $base_url; ?>/public/assets/images/animals.png" alt="Animaux heureux">
    </div>
  </section>

  <!-- Services Section -->
  <section class="services">
    <h2>Nos services</h2>
    <div class="services-grid">
      <div class="service-card">
        <div class="service-icon">🐕</div>
        <h3>Promenade quotidienne</h3>
        <p>Des promeneurs expérimentés pour faire bouger votre animal et lui permettre d'explorer le quartier en toute sécurité.</p>
      </div>
      <div class="service-card">
        <div class="service-icon">🏠</div>
        <h3>Garde à domicile</h3>
        <p>Confiez votre animal à un gardien bienveillant qui s'occupera de lui dans le confort de votre maison.</p>
      </div>
      <div class="service-card">
        <div class="service-icon">❤️</div>
        <h3>Soins personnalisés</h3>
        <p>Chaque gardien propose des services adaptés aux besoins spécifiques de votre animal : alimentation, jeux, affection.</p>
      </div>
    </div>
  </section>

  <!-- How It Works -->
  <section class="how-it-works">
    <div class="how-it-works-text">
      <h2>Comment ça marche ?</h2>
      <ul>
        <li>Inscrivez votre animal avec ses besoins et habitudes spécifiques.</li>
        <li>Choisissez un gardien ou promeneur qui convient parfaitement.</li>
        <li>Organisez les détails et fixez un planning qui vous convient.</li>
        <li>Profitez l'esprit tranquille et recevez des nouvelles de votre compagnon.</li>
      </ul>
    </div>
    <img src="<?php echo $base_url; ?>/public/assets/images/question.png" alt="Comment ça marche">
  </section>

  <?php
  // FOOTER
  include $base_dir . '/app/Views/Components/footer.php';
  ?>

</body>

</html>