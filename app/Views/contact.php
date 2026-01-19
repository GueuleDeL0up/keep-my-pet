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
  <title>Contact - KeepMyPet</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/contact_modern.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
  <!-- HEADER -->
  <?php include $base_dir . "/app/Views/Components/header.php"; ?>

  <section class="contact-container">
    <!-- Header Section -->
    <div class="contact-header">
      <h1>Contactez-nous</h1>
      <p>Vous avez une question ou besoin d'assistance ? Notre équipe est là pour vous aider dans les plus brefs délais.</p>
    </div>

    <!-- Content Layout -->
    <div class="contact-content">
      <div class="contact-form-side">
        <h2>Envoyez-nous un message</h2>

        <form action="send_contact.php" method="POST" class="keepmypet-form">
          <div class="form-row">
            <div class="form-group">
              <label for="nom">Nom</label>
              <input type="text" id="nom" name="nom" placeholder="Votre nom" required>
            </div>
            <div class="form-group">
              <label for="prenom">Prénom</label>
              <input type="text" id="prenom" name="prenom" placeholder="Votre prénom" required>
            </div>
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="exemple@mail.com" required>
          </div>

          <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="5" placeholder="Comment pouvons-nous vous aider ?" required></textarea>
          </div>

          <button type="submit" class="btn-submit">Envoyer</button>
        </form>
      </div>
      <img src="<?php echo $base_url; ?>/public/assets/images/question_cat.png" alt="Contact KeepMyPet">
    </div>
    </div>

    <!-- Info Cards -->
    <div class="contact-info">
      <div class="info-card">
        <i class="fas fa-envelope"></i>
        <h3>Email</h3>
        <p>contact@keepmypet.fr</p>
      </div>
      <div class="info-card">
        <i class="fas fa-phone"></i>
        <h3>Téléphone</h3>
        <p>+33 (0)1 23 45 67 89</p>
      </div>
      <div class="info-card">
        <i class="fas fa-clock"></i>
        <h3>Horaires</h3>
        <p>Lun-Ven: 9h-18h<br>Sam: 10h-14h</p>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <?php include $base_dir . '/app/Views/Components/footer.php'; ?>

  <script>
    // Form submission handler
    document.querySelector('.keepmypet-form')?.addEventListener('submit', function(e) {
      const submitBtn = this.querySelector('.btn-submit');
      const originalText = submitBtn.textContent;

      submitBtn.textContent = 'Envoi en cours...';
      submitBtn.disabled = true;

      setTimeout(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
      }, 1500);
    });
  </script>
</body>

</html>