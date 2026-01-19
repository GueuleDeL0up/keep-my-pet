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
    // Form submission handler avec validation et feedback
    document.querySelector('.keepmypet-form')?.addEventListener('submit', function(e) {
      e.preventDefault();

      const form = this;
      const submitBtn = form.querySelector('.btn-submit');
      const originalText = submitBtn.textContent;
      const formData = new FormData(form);

      // Désactiver le bouton
      submitBtn.disabled = true;
      submitBtn.textContent = 'Envoi en cours...';

      // Envoyer les données via fetch
      fetch('/keep-my-pet/app/Views/send_contact.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Succès - afficher le message
            showNotification(data.message, 'success');
            form.reset();

            // Réactiver le bouton après 3 secondes
            setTimeout(() => {
              submitBtn.disabled = false;
              submitBtn.textContent = originalText;
            }, 3000);
          } else {
            // Erreur
            showNotification(data.message, 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
          }
        })
        .catch(error => {
          showNotification('Erreur lors de l\'envoi du message. Veuillez réessayer.', 'error');
          submitBtn.disabled = false;
          submitBtn.textContent = originalText;
          console.error('Erreur:', error);
        });
    });

    // Fonction pour afficher les notifications
    function showNotification(message, type) {
      const notification = document.createElement('div');
      notification.className = `notification notification-${type}`;
      notification.innerHTML = `
        <div class="notification-content">
          <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
          <span>${message}</span>
        </div>
      `;

      // Ajouter les styles si nécessaire
      const style = document.createElement('style');
      style.textContent = `
        .notification {
          position: fixed;
          top: 20px;
          right: 20px;
          padding: 16px 20px;
          border-radius: 8px;
          box-shadow: 0 4px 12px rgba(0,0,0,0.15);
          z-index: 10000;
          animation: slideIn 0.3s ease;
          max-width: 400px;
        }
        .notification-success {
          background: #ecfdf5;
          border-left: 4px solid #10b981;
          color: #065f46;
        }
        .notification-error {
          background: #fef2f2;
          border-left: 4px solid #ef4444;
          color: #7f1d1d;
        }
        .notification-content {
          display: flex;
          align-items: center;
          gap: 12px;
          font-weight: 500;
        }
        @keyframes slideIn {
          from { transform: translateX(400px); opacity: 0; }
          to { transform: translateX(0); opacity: 1; }
        }
        @media (max-width: 480px) {
          .notification {
            left: 20px;
            right: 20px;
            max-width: none;
          }
        }
      `;

      if (!document.querySelector('style[data-notification]')) {
        style.setAttribute('data-notification', 'true');
        document.head.appendChild(style);
      }

      document.body.appendChild(notification);

      // Supprimer après 5 secondes
      setTimeout(() => {
        notification.style.animation = 'slideIn 0.3s ease reverse';
        setTimeout(() => notification.remove(), 300);
      }, 5000);
    }
  </script>
</body>

</html>