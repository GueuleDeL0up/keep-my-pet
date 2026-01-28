<?php
// Base config
$base_url = "/keep-my-pet/";
$base_dir = __DIR__ . "/../../";

session_start();

// Load language system (keep UI consistent)
require_once $base_dir . 'app/Config/language.php';
$current_lang = getCurrentLanguage();

// DB connection
require_once $base_dir . 'app/Models/connection_db.php';

// Fetch last emails for debugging
$emails = [];
$selectedEmail = null;

try {
  // List of last 10
  $stmt = $db->query("SELECT id, to_email, subject, body, token, created_at FROM debug_emails ORDER BY id DESC LIMIT 10");
  $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Selected email: ?id=42 or ?latest=1 (defaults to latest if available)
  if (!empty($_GET['id'])) {
    $id = (int) $_GET['id'];
    $stmtSel = $db->prepare("SELECT id, to_email, subject, body, token, created_at FROM debug_emails WHERE id = ? LIMIT 1");
    $stmtSel->execute([$id]);
    $selectedEmail = $stmtSel->fetch(PDO::FETCH_ASSOC) ?: null;
  } elseif (!empty($_GET['latest']) || empty($_GET)) {
    $stmtSel = $db->query("SELECT id, to_email, subject, body, token, created_at FROM debug_emails ORDER BY id DESC LIMIT 1");
    $selectedEmail = $stmtSel->fetch(PDO::FETCH_ASSOC) ?: null;
  }
} catch (Exception $e) {
  $emails = [];
  $selectedEmail = null;
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($current_lang); ?>">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KeepMyPet - Debug Emails</title>
  <link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/log_in.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #0f172a;
      color: #e2e8f0;
      margin: 0;
      padding: 0;
    }

    .page {
      max-width: 960px;
      margin: 40px auto;
      padding: 0 20px;
    }

    h1 {
      margin-bottom: 8px;
      color: #f8fafc;
    }

    p.muted {
      color: #94a3b8;
      margin-top: 0;
    }

    .card {
      background: #111827;
      border: 1px solid #1f2937;
      border-radius: 12px;
      padding: 16px;
      margin-top: 16px;
    }

    .row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }

    .label {
      color: #94a3b8;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .value {
      color: #e2e8f0;
      word-break: break-word;
    }

    .body {
      margin-top: 12px;
      background: #0b1220;
      border: 1px solid #1f2937;
      border-radius: 8px;
      padding: 12px;
      color: #e2e8f0;
      white-space: pre-wrap;
    }

    .actions {
      margin-top: 20px;
      display: flex;
      gap: 10px;
    }

    .btn {
      padding: 10px 16px;
      border-radius: 8px;
      border: 1px solid #334155;
      color: #e2e8f0;
      background: #1e293b;
      text-decoration: none;
      font-weight: 600;
    }

    .btn:hover {
      background: #273449;
    }

    .empty {
      margin-top: 20px;
      color: #94a3b8;
    }

    .preview {
      margin-top: 20px;
      padding: 16px;
      background: #0b1220;
      border: 1px solid #1f2937;
      border-radius: 12px;
    }

    .preview h2 {
      margin: 0 0 12px;
      color: #f8fafc;
    }

    .preview iframe {
      width: 100%;
      height: 520px;
      border: 1px solid #1f2937;
      border-radius: 10px;
      background: white;
    }

    .preview-meta {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 10px;
      margin-bottom: 12px;
    }

    .preview-meta .label {
      font-size: 11px;
      color: #94a3b8;
      text-transform: uppercase;
      letter-spacing: 0.4px;
    }

    .preview-meta .value {
      color: #e2e8f0;
    }
  </style>
</head>

<body>
  <div class="page">
    <h1>Debug Emails</h1>
    <p class="muted">Aperçu des 10 derniers emails générés (reset mot de passe, etc.).</p>

    <div class="actions">
      <a class="btn" href="<?php echo $base_url; ?>app/Views/forgotten_password.php">Tester un nouvel envoi</a>
      <a class="btn" href="<?php echo $base_url; ?>app/Views/log_in.php">Aller à la connexion</a>
      <a class="btn" href="<?php echo $base_url; ?>app/Views/debug_emails.php?latest=1#preview">Voir le dernier email</a>
    </div>

    <?php if ($selectedEmail): ?>
      <div id="preview" class="preview">
        <h2>Email sélectionné</h2>
        <div class="preview-meta">
          <div>
            <div class="label">Destinataire</div>
            <div class="value"><?php echo htmlspecialchars($selectedEmail['to_email']); ?></div>
          </div>
          <div>
            <div class="label">Sujet</div>
            <div class="value"><?php echo htmlspecialchars($selectedEmail['subject']); ?></div>
          </div>
          <div>
            <div class="label">Date</div>
            <div class="value"><?php echo htmlspecialchars($selectedEmail['created_at']); ?></div>
          </div>
          <div>
            <div class="label">Token</div>
            <div class="value"><?php echo htmlspecialchars($selectedEmail['token'] ?? ''); ?></div>
          </div>
        </div>
        <iframe srcdoc="<?php echo htmlspecialchars($selectedEmail['body'], ENT_QUOTES); ?>" title="Aperçu email"></iframe>
      </div>
    <?php endif; ?>

    <?php if (empty($emails)): ?>
      <p class="empty">Aucun email enregistré pour le moment.</p>
    <?php else: ?>
      <?php foreach ($emails as $email): ?>
        <div class="card">
          <div class="row">
            <div>
              <div class="label">Destinataire</div>
              <div class="value"><?php echo htmlspecialchars($email['to_email']); ?></div>
            </div>
            <div>
              <div class="label">Date</div>
              <div class="value"><?php echo htmlspecialchars($email['created_at']); ?></div>
            </div>
          </div>
          <div class="row" style="margin-top: 10px;">
            <div>
              <div class="label">Sujet</div>
              <div class="value"><?php echo htmlspecialchars($email['subject']); ?></div>
            </div>
            <div>
              <div class="label">Token</div>
              <div class="value"><?php echo htmlspecialchars($email['token'] ?? ''); ?></div>
            </div>
          </div>
          <div class="actions" style="margin-top: 12px;">
            <a class="btn" href="<?php echo $base_url; ?>app/Views/debug_emails.php?id=<?php echo (int) $email['id']; ?>#preview">Ouvrir cet email</a>
          </div>
          <div class="body"><?php echo nl2br(htmlspecialchars($email['body'])); ?></div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</body>

</html>