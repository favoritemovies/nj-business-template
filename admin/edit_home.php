<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/config.php';
ini_set('display_errors','1'); error_reporting(E_ALL);

$FILE = CONTENT_DIR . '/home.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = [
    'hero_title'        => $_POST['hero_title']        ?? '',
    'hero_button_text'  => $_POST['hero_button_text']  ?? '',
    'hero_button_link'  => $_POST['hero_button_link']  ?? '',
    'about_title'       => $_POST['about_title']       ?? '',
    'about_p1'          => $_POST['about_p1']          ?? '',
    'about_p2'          => $_POST['about_p2']          ?? '',
  ];
  file_put_contents($FILE, json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
  header('Location: /admin/edit_home.php?saved=1');
  exit;
}

$home = json_decode(@file_get_contents($FILE), true) ?: [];
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES,'UTF-8'); }
?>
<!doctype html><meta charset="utf-8">
<title>Admin — Home</title>
<style>
body{font:16px/1.6 system-ui,sans-serif;max-width:900px;margin:20px auto;padding:0 16px}
h1{margin:0 0 12px}
label{display:block;font-weight:600;margin:14px 0 6px}
input[type=text],textarea{width:100%;padding:10px;border:1px solid #ddd;border-radius:10px}
button{margin-top:14px;padding:10px 14px;border-radius:10px;background:#000;color:#fff;border:1px solid #000;cursor:pointer}
.top{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
a{color:#111}
.note{color:#666}
</style>
<div class="top">
  <h1>Admin — Home Content</h1>
  <div>
    <a href="/admin/index.php">Gallery</a> ·
    <a href="/admin/edit_services.php">Services</a> ·
    <a href="/admin/logout.php">Logout</a>
  </div>
</div>
<?php if (!empty($_GET['saved'])): ?><div class="note">Saved.</div><?php endif; ?>

<form method="post">
  <label>Hero title</label>
  <input type="text" name="hero_title" value="<?=h($home['hero_title'] ?? '')?>">

  <label>Hero button text</label>
  <input type="text" name="hero_button_text" value="<?=h($home['hero_button_text'] ?? '')?>">

  <label>Hero button link</label>
  <input type="text" name="hero_button_link" value="<?=h($home['hero_button_link'] ?? '/inquiry.html')?>">

  <label>About title</label>
  <input type="text" name="about_title" value="<?=h($home['about_title'] ?? 'About')?>">

  <label>About — first paragraph</label>
  <textarea name="about_p1" rows="3"><?=h($home['about_p1'] ?? '')?></textarea>

  <label>About — second paragraph</label>
  <textarea name="about_p2" rows="3"><?=h($home['about_p2'] ?? '')?></textarea>

  <button type="submit">Save</button>
</form>
