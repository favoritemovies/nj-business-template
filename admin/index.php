<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/functions.php';

$images = list_images();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin — Gallery</title>
<style>
body{ font:16px/1.6 system-ui, sans-serif; margin:0; color:#111; background:#fff; }
.top{ display:flex; align-items:center; justify-content:space-between; gap:12px; padding:14px 18px; border-bottom:1px solid #e5e7eb; position:sticky; top:0; background:#fff; }
h1{ font-size:18px; margin:0; }
a.btn, button.btn{
  display:inline-flex; align-items:center; gap:8px; padding:10px 12px;
  background:#000; color:#fff; border:1px solid #000; border-radius:10px; text-decoration:none; cursor:pointer;
}
.wrap{ max-width:1100px; margin:18px auto; padding:0 16px; }

.box{ border:1px solid #e5e7eb; border-radius:12px; padding:14px; margin:12px 0; }
.box h2{ font-size:16px; margin:0 0 12px; }

input[type="file"], input[type="text"]{
  padding:10px; border:1px solid #ddd; border-radius:10px; width:100%;
}
.grid{ display:grid; gap:12px; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); }
.tile{ border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; background:#fff; }
.tile .ph{ aspect-ratio: 3/4; background:#f3f4f6; display:grid; place-items:center; }
.tile img{ width:100%; height:100%; object-fit:cover; display:block; }
.tile .meta{ padding:8px; display:flex; align-items:center; justify-content:space-between; gap:8px; }
.meta small{ color:#6b7280; }
.meta form{ margin:0; }
.meta button{ background:#b91c1c; border:1px solid #991b1b; color:#fff; padding:8px 12px; border-radius:8px; cursor:pointer; }
.note{ color:#6b7280; font-size:13px; margin-top:6px; }
</style>
</head>
<body>
  <div class="top">
  <h1>Gallery (<?=count($images)?>)</h1>
  <div style="display:flex;gap:10px;align-items:center;">
    <a class="btn" href="/admin/edit_home.php">Text: Home</a>
    <a class="btn" href="/admin/edit_services.php">Text: Services</a>
    <a class="btn" href="/admin/logout.php">Logout</a>
  </div>
</div>

<div class="wrap">
  <div class="box">
    <h2>Upload Photo</h2>
    <form action="/admin/upload.php" method="post" enctype="multipart/form-data">
      <div style="display:grid; gap:10px; grid-template-columns: 1fr 1fr;">
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp" required>
        <input type="text" name="alt" placeholder="Alt text (optional)">
      </div>
      <div class="note">Supported: JPG/JPEG, PNG, WebP. HEIC/HEIF is not supported here (convert before upload).</div>
      <button class="btn" type="submit" style="margin-top:10px;">Upload</button>
    </form>
  </div>

  <div class="box">
    <h2>All Photos</h2>
      <?php if (!$images): ?>
         <div class="note">В папке <code><?= htmlspecialchars(PUBLIC_PREFIX) ?></code> нет изображений.</div>
      <?php else: ?>
        <div class="grid">
          <?php foreach ($images as $abs): $name = basename($abs); ?>
            <div class="tile">
              <div class="ph">
                <img src="<?= public_url($abs) ?>" alt="<?= htmlspecialchars($name) ?>">
              </div>
              <div class="meta">
                <small title="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?></small>
                <form action="/admin/delete.php" method="post" onsubmit="return confirm('Delete <?=htmlspecialchars($name)?>?');">
                  <input type="hidden" name="file" value="<?= htmlspecialchars($name) ?>">
                  <button type="submit">Delete</button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
