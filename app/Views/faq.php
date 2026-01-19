<?php
// Define the base
$base_url = "/keep-my-pet/";  // For HTML links
$base_dir = __DIR__ . "/../../";  // For PHP includes
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>FAQ</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/faq.css">
</head>

<body>
  <?php
  // HEADER
  include $base_dir . "/app/Views/Components/header.php";
  ?>

  <section class="faq-container">
    <div class="faq-content">
      <h2>Foire aux questions (FAQ)</h2>
      <p>Retrouvez ici les réponses aux questions les plus fréquentes de nos utilisateurs.</p>

      <div class="faq-list">
        <details class="faq-item">
          <summary>Comment sont sélectionnés les gardiens ?</summary>
          <div class="faq-answer">
            <p>Tous nos gardiens passent par un processus de vérification rigoureux comprenant l'identité, l'expérience avec les animaux et les avis des anciens propriétaires.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary>Quels types d'animaux sont acceptés ?</summary>
          <div class="faq-answer">
            <p>KeepMyPet accueille principalement les chiens et les chats, mais vous pouvez également trouver des gardiens spécialisés pour les rongeurs, oiseaux ou nouveaux animaux de compagnie (NAC).</p>
          </div>
        </details>

        <details class="faq-item">
          <summary>Que se passe-t-il en cas d'urgence ?</summary>
          <div class="faq-answer">
            <p>Le gardien dispose de vos coordonnées d'urgence et de celles de votre vétérinaire. Notre assistance est également disponible 7j/7 pour vous accompagner en cas de besoin.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary>Comment se déroule le paiement ?</summary>
          <div class="faq-answer">
            <p>Le paiement s'effectue de manière sécurisée directement sur la plateforme lors de la réservation. Le gardien est rémunéré une fois la prestation terminée.</p>
          </div>
      </div>
    </div>
    </div>
  </section>

  <?php include $base_dir . '/app/Views/Components/footer.php'; ?>

</body>

</html>