<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/config.php';
ini_set('display_errors','1'); error_reporting(E_ALL);

$FILE = CONTENT_DIR . '/services.json';

// загрузим или дефолт
$svc = json_decode(@file_get_contents($FILE), true) ?: [
  'title' => 'Services & Packages',
  'lead'  => 'Makeup and hair services for parties, events, weddings, and photoshoots.',
  'packages' => [
    'makeup' => [
      'title' => 'Makeup Package',
      'price' => '~$150',
      'items' => [
        'Professional makeup application',
        'Skin preparation',
        'False lashes (optional)',
        'Long-lasting products'
      ]
    ],
    'hair' => [
      'title' => 'Hair Styling Package',
      'price' => '~$150',
      'items' => [
        'Professional hairstyling',
        'Blowout or updo styling',
        'Finishing and hold products'
      ]
    ],
    'combo' => [
      'title' => 'Makeup + Hair Package',
      'price' => '~$280',
      'items' => [
        'Full makeup application',
        'Professional hairstyling',
        'Complete polished look for any occasion'
      ]
    ],
  ],
  'sections' => [
    'additional' => [
      'title' => 'Additional Services',
      'items' => [
        'Extra touch-up time — $50 per hour',
        'Hairstyle change — $60',
        'Extensions application — $30'
      ]
    ],
    'travel' => [
      'title' => 'Travel Fees',
      'items' => [
        'Local area — included',
        'Other locations — starting from $50',
        'Fee may vary depending on distance and number of clients'
      ]
    ],
    'early' => [
      'title' => 'Early Start',
      'items' => [
        'Appointments before 8am — $100 per artist'
      ]
    ],
  ],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // плоские поля
  $svc['title'] = $_POST['title'] ?? $svc['title'];
  $svc['lead']  = $_POST['lead']  ?? '';

  // helper для разбора textarea в массив пунктов
  $toLines = function($name){
    $raw = trim($_POST[$name] ?? '');
    if ($raw === '') return [];
    $arr = preg_split('~\r?\n~', $raw);
    return array_values(array_filter(array_map('trim', $arr), fn($x)=>$x!==''));
  };

  // packages
  foreach (['makeup','hair','combo'] as $k) {
    $svc['packages'][$k]['title'] = $_POST["p_{$k}_title"] ?? $svc['packages'][$k]['title'];
    $svc['packages'][$k]['price'] = $_POST["p_{$k}_price"] ?? ($svc['packages'][$k]['price'] ?? '');
    $svc['packages'][$k]['items'] = $toLines("p_{$k}_items");
  }

  // sections
  foreach (['additional','travel','early'] as $k) {
    $svc['sections'][$k]['title'] = $_POST["s_{$k}_title"] ?? $svc['sections'][$k]['title'];
    $svc['sections'][$k]['items'] = $toLines("s_{$k}_items");
  }

  file_put_contents($FILE, json_encode($svc, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
  header('Location: /admin/edit_services.php?saved=1'); exit;
}

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES,'UTF-8'); }
$P = $svc['packages']; $S = $svc['sections'];
$lines = fn($arr)=> implode("\n", $arr ?? []);
?>
<!doctype html><meta charset="utf-8">
<title>Admin — Services</title>
<style>
body{font:16px/1.6 system-ui,sans-serif;max-width:1000px;margin:20px auto;padding:0 16px}
h1{margin:0 0 12px} h2{margin:20px 0 8px}
label{display:block;font-weight:600;margin:12px 0 6px}
input[type=text],textarea{width:100%;padding:10px;border:1px solid #ddd;border-radius:10px}
.grid{display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr))}
.card{border:1px solid #eee;border-radius:12px;padding:14px}
button{margin-top:14px;padding:10px 14px;border-radius:10px;background:#000;color:#fff;border:1px solid #000;cursor:pointer}
.top{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
a{color:#111}.note{color:#666}
</style>
<div class="top">
  <h1>Services content</h1>
  <div><a href="/admin/edit_home.php">Home</a> · <a href="/admin/index.php">Gallery</a> · <a href="/admin/logout.php">Logout</a></div>
</div>
<?php if (!empty($_GET['saved'])): ?><div class="note">Saved.</div><?php endif; ?>

<form method="post">
  <label>Page title</label>
  <input type="text" name="title" value="<?=h($svc['title'])?>">

  <label>Lead (optional)</label>
  <input type="text" name="lead" value="<?=h($svc['lead'] ?? '')?>">

  <h2>Packages</h2>
  <div class="grid">
    <?php foreach (['makeup'=>'Makeup','hair'=>'Hair Styling','combo'=>'Makeup + Hair'] as $k=>$label): ?>
<div class="card">
        <h3><?=h($label)?></h3>
        <label>Title</label>
        <input type="text" name="p_<?=h($k)?>_title" value="<?=h($P[$k]['title'] ?? '')?>">
        <label>Price</label>
        <input type="text" name="p_<?=h($k)?>_price" value="<?=h($P[$k]['price'] ?? '')?>">
        <label>Items (one per line)</label>
        <textarea name="p_<?=h($k)?>_items" rows="8"><?=h($lines($P[$k]['items'] ?? []))?></textarea>
      </div>
    <?php endforeach; ?>
  </div>

  <h2>Sections</h2>
  <div class="grid">
    <?php foreach (['additional'=>'Additional services','travel'=>'Travel fees','early'=>'Early start'] as $k=>$label): ?>
      <div class="card">
        <h3><?=h($label)?></h3>
        <label>Title</label>
        <input type="text" name="s_<?=h($k)?>_title" value="<?=h($S[$k]['title'] ?? '')?>">
        <label>Items (one per line)</label>
        <textarea name="s_<?=h($k)?>_items" rows="8"><?=h($lines($S[$k]['items'] ?? []))?></textarea>
      </div>
    <?php endforeach; ?>
  </div>

  <button type="submit">Save</button>
</form>
