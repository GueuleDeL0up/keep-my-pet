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
  <title>KeepMyPet - Contact</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/contact.css">
</head>

<body>
  <section class="contact-container">
    <div class="contact-content">
      <div class="contact-form-side">
        <h2>Contactez-nous</h2>
        <p>Une question ? Notre équipe est là pour vous répondre dans les plus brefs délais.</p>

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

      <div class="contact-illustration">
        <img src="<?php echo $base_url; ?>/public/assets/images/question_cat.png" alt="Contact KeepMyPet">
      </div>
    </div>
  </section>
</body>

</html>

<?php
// FOOTER
include $base_dir . '/app/Views/Components/footer.php';
?>