<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <meta name="description" content="Makeup and hair services for parties, events, weddings, and photoshoots." />

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:opsz,wght@6..96,400;500;600&family=Source+Serif+4:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">

  <title>Name — Services</title>

  <link rel="stylesheet" href="css/style.css">
  <link rel="icon" type="image/png" href="/img/site/favicon.png">
  <link rel="shortcut icon" href="/img/site/favicon.png" type="image/png">

  <script defer src="js/script.js"></script>

  <?php
  // Optional analytics (GA4). Put your GA4 id into /content/config.json
  // Example: { "ga4_id": "G-XXXXXXXXXX" }
  $cfg = json_decode(@file_get_contents(__DIR__ . '/content/config.json'), true) ?: [];
  $ga4 = trim((string)($cfg['ga4_id'] ?? ''));

  if ($ga4 !== ''): ?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($ga4, ENT_QUOTES, 'UTF-8') ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '<?= htmlspecialchars($ga4, ENT_QUOTES, 'UTF-8') ?>');
    </script>
  <?php endif; ?>
</head>

<body class="home">
<?php
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$home = json_decode(@file_get_contents(__DIR__ . '/content/home.json'), true) ?: [];
$svc = json_decode(@file_get_contents(__DIR__ . '/content/services.json'), true) ?: [];
$p = $svc['packages'] ?? [];
$s = $svc['sections'] ?? [];
?>

<header class="site-header">
  <div class="container nav-wrap">
    <a class="logo" href="index.php">Brand Name</a>
   
    <div class="mobile-contacts" aria-label="Quick contacts">

  <a class="contact-icon"
   target="_blank" rel="noopener"
   href="https://mail.google.com/"
   aria-label="Email">
  <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true">
    <path d="M3 7.5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-9Z" stroke-width="1.8"/>
    <path d="M4 8l8 6 8-6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
  </svg>
</a>

  <a class="contact-icon" href="https://instagram.com/" target="_blank" rel="noopener" aria-label="Instagram">
    <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true">
      <rect x="3" y="3" width="18" height="18" rx="5" stroke-width="1.8"/>
      <circle cx="12" cy="12" r="4" stroke-width="1.8"/>
      <circle class="ig-dot" cx="17.5" cy="6.5" r="1.2"/>
    </svg>
  </a>
</div>
<button class="nav-toggle" aria-label="Menu" aria-expanded="false">
      <span class="bar"></span><span class="bar"></span><span class="bar"></span>
    </button>

    <nav class="site-nav" aria-label="Primary">
      <ul>
        <li><a href="index.php" class="active">Home</a></li>
        <li><a href="portfolio.php">Portfolio</a></li>
        <li><a href="inquiry.html">Inquire</a></li>
        <li><a href="services.php">Services</a></li>
      </ul>
    </nav>
  </div>
</header>

<main class="home">
   <section class="hero">
    <div class="hero-inner">
      <h1 class="hero-title">
        <?= h($home['hero_title'] ?? 'Wedding and Event Makeup and Hair Services') ?>
      </h1>
      <a class="btn" href="<?= h($home['hero_button_link'] ?? '/inquiry.html') ?>">
        <?= h($home['hero_button_text'] ?? 'Book now') ?>
      </a>
    </div>
  </section>

  <section id="about" class="section">
    <div class="container narrow">
<h2><?= h($home['about_title'] ?? 'About') ?></h2>

<p><?= nl2br(h($home['about_p1'] ?? 'Hello! I’m a professional makeup and hair artist offering on-location services for parties, events, weddings, and photoshoots.')) ?></p>

<p><?= nl2br(h($home['about_p2'] ?? 'Every look is created with attention to detail to highlight natural beauty, enhance individual features, and deliver a polished result tailored to each client.')) ?></p>

    </div>
  </section>
</main>
  <footer class="site-footer">
    <div class="container"><p>© Brand Name</p></div>
  </footer>
</body>
</html>
