<?php
session_start();

$base_url = "/keep-my-pet/";
$base_dir = __DIR__ . "/../../";

// Guard: only admin special
if (empty($_SESSION['is_admin'])) {
  header('Location: ' . $base_url . '/app/Views/log_in.php');
  exit;
}

// Load dependencies
include $base_dir . 'app/Models/connection_db.php';
include $base_dir . 'app/Models/requests.users.php';
include $base_dir . 'app/Models/requests.advertisements.php';

$messages = ['success' => [], 'errors' => []];

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'delete_user') {
    $userId = (int)($_POST['user_id'] ?? 0);
    if ($userId > 0) {
      // Delete user's ads first to avoid FK issues
      supprimerAnnoncesParUtilisateur($userId);
      if (supprimerUtilisateur($db, $userId)) {
        $messages['success'][] = "Utilisateur #{$userId} supprimé.";
      } else {
        $messages['errors'][] = "Impossible de supprimer l'utilisateur #{$userId}.";
      }
    } else {
      $messages['errors'][] = 'Identifiant utilisateur invalide.';
    }
  }

  if ($action === 'delete_ad') {
    $adId = (int)($_POST['ad_id'] ?? 0);
    if ($adId > 0) {
      if (supprimerAnnonceAdmin($adId)) {
        $messages['success'][] = "Annonce #{$adId} supprimée.";
      } else {
        $messages['errors'][] = "Impossible de supprimer l'annonce #{$adId}.";
      }
    } else {
      $messages['errors'][] = 'Identifiant annonce invalide.';
    }
  }
}

// Filters
$user_search = trim($_GET['user_search'] ?? '');
$ad_search = trim($_GET['ad_search'] ?? '');

$users = $user_search !== '' ? rechercherUtilisateurs($db, $user_search) : recupereTousUtilisateurs($db);
$ads = $ad_search !== '' ? rechercherAnnonces(['search' => $ad_search]) : obtenirToutesAnnonces();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - KeepMyPet</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/global-preferences.css">
  <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/admin.css">
  <script type="text/javascript" src="<?php echo $base_url; ?>/public/assets/js/theme.js"></script>
</head>

<body>
  <main class="admin-container">
    <div class="admin-header">
      <h1>Espace admin</h1>
      <a href="<?php echo $base_url; ?>app/Views/logout.php" class="btn-logout">Déconnexion</a>
    </div>

    <?php if (!empty($messages['success'])): ?>
      <div class="alert success">
        <?php foreach ($messages['success'] as $msg): ?>
          <p><?php echo htmlspecialchars($msg); ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($messages['errors'])): ?>
      <div class="alert error">
        <?php foreach ($messages['errors'] as $msg): ?>
          <p><?php echo htmlspecialchars($msg); ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <section class="panel">
      <div class="panel-header">
        <h2>Utilisateurs (<?php echo count($users); ?>)</h2>
        <form method="GET" class="filter-form">
          <input type="text" name="user_search" placeholder="Rechercher (email, prénom, nom)" value="<?php echo htmlspecialchars($user_search); ?>">
          <button type="submit">Filtrer</button>
        </form>
      </div>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Prénom</th>
              <th>Nom</th>
              <th>Email</th>
              <th>Téléphone</th>
              <th>Admin</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($users)): ?>
              <tr><td colspan="7" class="empty">Aucun utilisateur</td></tr>
            <?php else: ?>
              <?php foreach ($users as $user): ?>
                <tr>
                  <td><?php echo htmlspecialchars($user['id']); ?></td>
                  <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                  <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                  <td><?php echo htmlspecialchars($user['email']); ?></td>
                  <td><?php echo htmlspecialchars($user['phone_number']); ?></td>
                  <td><?php echo !empty($user['is_admin']) ? 'Oui' : 'Non'; ?></td>
                  <td>
                    <form method="POST" onsubmit="return confirm('Supprimer cet utilisateur ?');" style="display:inline-block;">
                      <input type="hidden" name="action" value="delete_user">
                      <input type="hidden" name="user_id" value="<?php echo (int)$user['id']; ?>">
                      <button type="submit" class="danger">Supprimer</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section class="panel">
      <div class="panel-header">
        <h2>Annonces (<?php echo count($ads); ?>)</h2>
        <form method="GET" class="filter-form">
          <input type="text" name="ad_search" placeholder="Rechercher (titre, description)" value="<?php echo htmlspecialchars($ad_search); ?>">
          <button type="submit">Filtrer</button>
        </form>
      </div>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Titre</th>
              <th>Ville</th>
              <th>Type</th>
              <th>Prix</th>
              <th>Utilisateur</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($ads)): ?>
              <tr><td colspan="7" class="empty">Aucune annonce</td></tr>
            <?php else: ?>
              <?php foreach ($ads as $ad): ?>
                <tr>
                  <td><?php echo htmlspecialchars($ad['id']); ?></td>
                  <td><?php echo htmlspecialchars($ad['title']); ?></td>
                  <td><?php echo htmlspecialchars($ad['city']); ?></td>
                  <td><?php echo htmlspecialchars($ad['type']); ?></td>
                  <td><?php echo htmlspecialchars(number_format((float)$ad['price'], 2)); ?>€</td>
                  <td><?php echo htmlspecialchars(($ad['first_name'] ?? '') . ' ' . ($ad['last_name'] ?? '')); ?></td>
                  <td>
                    <form method="POST" onsubmit="return confirm('Supprimer cette annonce ?');" style="display:inline-block;">
                      <input type="hidden" name="action" value="delete_ad">
                      <input type="hidden" name="ad_id" value="<?php echo (int)$ad['id']; ?>">
                      <button type="submit" class="danger">Supprimer</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</body>

</html>
