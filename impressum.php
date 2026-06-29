<?php
declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Impressum | <?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="/config/legal.css" />
</head>
<body>
  <main class="legal-shell">
    <a class="back-link" href="/index.php">Zurück zur Startseite</a>
    <section class="legal-card">
      <h1>Impressum</h1>
      <p>Diese Seite ist als Platzhalter angelegt.</p>
      <div class="legal-placeholder">
        <p><strong>Angaben gemäß § 5 TMG</strong></p>
        <p>Name, Anschrift und Kontakt bitte ergänzen.</p>
      </div>
    </section>
  </main>
</body>
</html>
