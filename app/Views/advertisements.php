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
  <title>KeepMyPet - Annonces</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/advertisements.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<main class="ads-container">
  <div class="ads-top-bar">
    <div class="search-wrapper">
      <input type="text" placeholder="Rechercher sur KeepMyPet" />
      <i class="fas fa-search search-icon"></i>
    </div>
    <a href="#" class="btn-deposit">
      <i class="fas fa-plus-circle"></i> Déposer une annonce
    </a>
  </div>

  <div class="filters-row">

    <select class="filter-pill">
      <option value="" disabled selected>Type d'annonce</option>
      <option value="garde">Garde à domicile</option>
      <option value="promenade">Promenade</option>
      <option value="visite">Visite</option>
    </select>

    <select class="filter-pill">
      <option value="" disabled selected>Type d'animal</option>
      <option value="chien">Chien</option>
      <option value="chat">Chat</option>
      <option value="nac">NAC (Lapin, Hamster...)</option>
    </select>

    <select class="filter-pill">
      <option value="" disabled selected>Localisation</option>
      <option value="autour">Autour de moi</option>
      <option value="paris">Paris</option>
      <option value="lyon">Lyon</option>
      <option value="marseille">Marseille</option>
      <option value="autre">Autre ville...</option>
    </select>

    <select class="filter-pill">
      <option value="" disabled selected>Date / Période</option>
      <option value="today">Aujourd'hui</option>
      <option value="weekend">Ce week-end</option>
      <option value="week">Cette semaine</option>
      <option value="month">Ce mois-ci</option>
    </select>

    <select class="filter-pill">
      <option value="" disabled selected>Tarif</option>
      <option value="asc">Prix croissant</option>
      <option value="desc">Prix décroissant</option>
      <option value="under-20">Moins de 20€</option>
      <option value="20-50">Entre 20€ et 50€</option>
    </select>

    <select class="filter-pill">
      <option value="" disabled selected>Avis</option>
      <option value="4plus">4 étoiles et +</option>
      <option value="5">5 étoiles uniquement</option>
    </select>

  </div>

  <div class="ads-grid">
    <div class="ads-list-col">
      <div class="ad-card active">
        <div class="ad-icon dog">
          <i class="fas fa-dog"></i>
        </div>
        <div class="ad-summary">
          <h3>Rocky</h3>
          <p>Gardiennage à Paris 18e du 15 au 20 nov 2025</p>
        </div>
        <div class="ad-meta">
          <div class="rating"><i class="fas fa-star"></i> 4.8</div>
          <div class="price">25 € / jour</div>
        </div>
      </div>

      <div class="ad-card">
        <div class="ad-icon cat">
          <i class="fas fa-cat"></i>
        </div>
        <div class="ad-summary">
          <h3>Milo</h3>
          <p>Gardiennage à Lille pour le 15 décembre 2025</p>
        </div>
        <div class="ad-meta">
          <div class="rating"><i class="fas fa-star"></i> 4.5</div>
          <div class="price">27 € / jour</div>
        </div>
      </div>

      <div class="ad-card">
        <div class="ad-icon cat">
          <i class="fas fa-cat"></i>
        </div>
        <div class="ad-summary">
          <h3>Ronron</h3>
          <p>Visite à domicile à Lyon</p>
        </div>
        <div class="ad-meta">
          <div class="rating"><i class="fas fa-star"></i> 5.0</div>
          <div class="price">20 € / jour</div>
        </div>
      </div>
    </div>

    <div class="ads-detail-col">
      <h2>Gardiennage à domicile</h2>
      <p class="detail-subtitle">
        <strong>Titre :</strong> Garde d'un chien labrador pendant les vacances
      </p>

      <ul class="detail-info">
        <li>
          <i class="fas fa-paw"></i> <strong>Type :</strong> Gardiennage
        </li>
        <li>
          <i class="fas fa-dog"></i> <strong>Animal :</strong> Chien - Labrador (Rocky)
        </li>
        <li>
          <i class="fas fa-map-marker-alt"></i>
          <strong>Lieu :</strong> Paris 18e
        </li>
        <li>
          <i class="far fa-calendar-alt"></i> <strong>Dates :</strong> Du 15 au 20 novembre 2025
        </li>
        <li>
          <i class="fas fa-sun"></i> <strong>Horaires :</strong> Journée et nuit
        </li>
        <li>
          <i class="fas fa-euro-sign"></i> <strong>Tarif :</strong> 25 € / jour
        </li>
      </ul>

      <div class="detail-desc">
        <strong>Description :</strong><br />
        Je recherche une personne de confiance pour garder mon labrador
        "Rocky" pendant mes vacances. Il est très sociable et adore les
        promenades. Fourniture de nourriture et accessoires assurée.
      </div>

      <div class="owner-rating">
        <i class="fas fa-star"></i> Note du propriétaire : 4.8 / 5
      </div>
    </div>
  </div>
</main>

<body>
</body>

</html>

<?php
// FOOTER
include $base_dir . '/app/Views/Components/footer.php';
?>