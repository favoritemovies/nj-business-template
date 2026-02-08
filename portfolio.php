<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <meta name="description" content="Selected makeup and hair looks for parties, events, weddings, and photoshoots." />

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:opsz,wght@6..96,400;500;600&family=Source+Serif+4:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">

  <title>Name — Portfolio</title>

  <link rel="stylesheet" href="css/style.css" />
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

<body>
  <!-- Header -->
  <header class="site-header">
    <div class="container nav-wrap">
      <a class="logo" href="index.php">Brand Name</a>

      <div class="mobile-contacts" aria-label="Quick contacts">
        <!-- Email -->
        <a class="contact-icon"
   target="_blank" rel="noopener"
   href="https://mail.google.com/"
   aria-label="Email">
          <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M3 7.5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-9Z" stroke-width="1.8"/>
            <path d="M4 8l8 6 8-6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </a>
        <!-- Instagram -->
        <a class="contact-icon" href="https://instagram.com/" target="_blank" rel="noopener" aria-label="Instagram">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <rect x="3" y="3" width="18" height="18" rx="5" stroke="#000" stroke-width="1.8"/>
            <circle cx="12" cy="12" r="4" stroke="#000" stroke-width="1.8"/>
            <circle cx="17.5" cy="6.5" r="1" fill="#000"/>
          </svg>
        </a>
      </div>

      <button class="nav-toggle" aria-label="Menu" aria-expanded="false">
        <span class="bar"></span><span class="bar"></span><span class="bar"></span>
      </button>

      <nav class="site-nav" aria-label="Primary">
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="portfolio.php" class="active">Portfolio</a></li>
          <li><a href="inquiry.html">Inquire</a></li>
          <li><a href="services.php">Services</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <!-- Main -->
  <main>
    <section class="section">
      <div class="container narrow center">
        <h1><span data-i18n="portfolio.title">Portfolio</span></h1>
        <p class="muted" data-i18n="portfolio.lead"></p>
      </div>
    </section>

    <section class="section">
      <div class="container">
        <div class="gallery">
        <!--  <figure><img src="/img/portfolio/1-1.jpg" alt="Bride on rooftop; off-shoulder gown, Hollywood waves; natural glam with city skyline." loading="lazy"></figure>
          <figure><img src="/img/portfolio/1-2.jpg" alt="Half-up, half-down style with loose waves; twist at the crown." loading="lazy"></figure>
          <figure><img src="/img/portfolio/1-3.jpg" alt="Back view of elegant low chignon; smooth finish and subtle highlights." loading="lazy"></figure>
          <figure><img src="/img/portfolio/1-4.jpg" alt="Woman in a white evening dress with soft makeup." loading="lazy"></figure>
          <figure><img src="/img/portfolio/1-5.jpg" alt="Smiling bride; soft bronze eyes, natural lashes; low updo; strapless lace gown." loading="lazy"></figure>
          <figure><img src="/img/portfolio/1-6.jpg" alt="Sleek styling: low chignon on dark hair — back view." loading="lazy"></figure> -->         
          
          <?php
          


$dir = __DIR__ . '/img/portfolio';
$webBase = 'img/portfolio/';


$images = glob($dir.'/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}', GLOB_BRACE) ?: [];


usort($images, function($a,$b){ return filemtime($a) <=> filemtime($b); });


$images = array_values(array_filter($images, function($abs){
  return !preg_match('/-\d{2,5}\.(jpe?g|png|webp)$/i', basename($abs));
}));

foreach ($images as $abs) {
  $name = basename($abs);
  if (isset($skip) && is_array($skip) && in_array($name, $skip, true)) continue;

    $srcPath = '/img/portfolio/'.$name;
  $v = filemtime($abs);
  
   $alt = preg_replace('/[_-]+/', ' ', pathinfo($name, PATHINFO_FILENAME));

  $sizes = '(min-width:1200px) calc((min(1100px, 100vw) - 48px)/4), '.
           '(min-width:1040px) calc((min(1100px, 100vw) - 40px)/3), '.
           '(min-width:700px)  calc((min(1100px, 100vw) - 20px)/2), '.
           'calc(100vw - 40px)';

  echo '<figure><img
    src="/thumb.php?src='.rawurlencode($srcPath).'&w=800&v='.$v.'"
    srcset="
      /thumb.php?src='.rawurlencode($srcPath).'&w=640&v='.$v.' 640w,
      /thumb.php?src='.rawurlencode($srcPath).'&w=1024&v='.$v.' 1024w,
      /thumb.php?src='.rawurlencode($srcPath).'&w=1600&v='.$v.' 1600w"
    sizes="(max-width:640px) 100vw, (max-width:1100px) 50vw, 33vw"
    alt="'.htmlspecialchars($alt, ENT_QUOTES).'"
    loading="lazy"
  ></figure>';
}
          ?>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="site-footer">
    <div class="container"><p>© Brand Name</p></div>
  </footer>

  <!-- Email modal -->
  <div id="email-modal" class="modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="emailModalTitle">
    <div class="modal-backdrop" data-close="email-modal"></div>
    <div class="modal-dialog">
      <button class="modal-close" data-close="email-modal" aria-label="Close">×</button>

      <h2 id="emailModalTitle">Write a message</h2>
      <form id="email-form">
        <div class="form-row">
          <label for="em-name">Name</label>
          <input id="em-name" name="name" required>
        </div>
        <div class="form-row">
          <label for="em-from">Email</label>
          <input id="em-from" name="from" type="email" required>
        </div>
        <div class="form-row">
          <label for="em-msg">Message</label>
          <textarea id="em-msg" name="message" rows="5" required></textarea>
        </div>
        <button class="btn" type="submit">Send</button>
        <p class="xs muted" id="email-status" role="status" aria-live="polite"></p>
      </form>
    </div>
  </div>
  <div class="lightbox" id="lightbox" aria-hidden="true">
  <button class="close" type="button" aria-label="Close">×</button>
  <img id="lightboxImg" alt="">
</div>

</body>
</html>
