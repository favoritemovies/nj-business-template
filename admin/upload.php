<?php
require __DIR__.'/auth.php';
require __DIR__.'/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /admin/index.php'); exit; }
if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) { header('Location:/admin/index.php?err=upload'); exit; }

$mime = mime_content_type($_FILES['image']['tmp_name']);
$allowed = ['image/jpeg','image/png','image/webp'];
if (!in_array($mime, $allowed, true)) { header('Location:/admin/index.php?err=type'); exit; }

$origName = $_FILES['image']['name'];
$norm = preg_replace('/[^A-Za-z0-9._-]+/', '_', $origName);
$dir  = img_dir();
$dest = $dir.'/'.$norm;
if (file_exists($dest)) {
  $p = pathinfo($norm);
  $norm = $p['filename'].'-'.date('Ymd-His').'.'.$p['extension'];
  $dest = $dir.'/'.$norm;
}
if (!move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
  header('Location:/admin/index.php?err=move'); exit;
}

function make_variants_all(string $srcAbs): void {
  $p = pathinfo($srcAbs);
  $dir = dirname($srcAbs);
  $base = $p['filename'];
  $variants = [
    640  => ["$dir/{$base}-640.jpg",  "$dir/{$base}-640.webp"],
    1024 => ["$dir/{$base}-1024.jpg", "$dir/{$base}-1024.webp"],
    1600 => ["$dir/{$base}-1600.jpg", "$dir/{$base}-1600.webp"],
  ];

  if (class_exists('Imagick')) {
    try{
      foreach ($variants as $w => [$jpg, $webp]) {
        $im = new Imagick($srcAbs);
        $im->setImageOrientation(imagick::ORIENTATION_TOPLEFT);
        $im->stripImage();
        $im->resizeImage($w, 0, Imagick::FILTER_LANCZOS, 1, true);
      
        $im->setImageFormat('jpeg');
        $im->setImageCompressionQuality(82);
        $im->writeImage($jpg);
      
        $im->setImageFormat('webp');
        $im->setImageCompressionQuality(80);
        $im->writeImage($webp);
        $im->clear(); $im->destroy();
      }
      return;
    }catch(Throwable $e){}
  }

  if (function_exists('imagecreatetruecolor')) {
    $info = getimagesize($srcAbs);
    if ($info) {
      [$w0,$h0,$type] = $info;
      $create = match($type){
        IMAGETYPE_JPEG => 'imagecreatefromjpeg',
        IMAGETYPE_PNG  => 'imagecreatefrompng',
        IMAGETYPE_WEBP => 'imagecreatefromwebp',
        default => null
      };
      if ($create && function_exists($create)) {
        $src = @$create($srcAbs);
        if ($src) {
          foreach ($variants as $w => [$jpg, $webp]) {
            $newW = $w;
            $newH = (int)round($h0 * ($newW / $w0));
            $dst = imagecreatetruecolor($newW, $newH);
            imagecopyresampled($dst, $src, 0,0,0,0, $newW,$newH, $w0,$h0);
          
            imagejpeg($dst, $jpg, 82);
           
            if (function_exists('imagewebp')) { imagewebp($dst, $webp, 80); }
            imagedestroy($dst);
          }
          imagedestroy($src);
          return;
        }
      }
    }
  }

  $bin = function_exists('shell_exec')
    ? (trim((string)@shell_exec('command -v magick')) ?: trim((string)@shell_exec('command -v convert')))
    : '';
  if ($bin) {
    $run = function(string $args) use ($bin){ @shell_exec($bin.' '.$args); };
    foreach ($variants as $w => [$jpg, $webp]) {
      $run(escapeshellarg($srcAbs).' -auto-orient -strip -resize '.intval($w)
        .' -background white -alpha remove -alpha off '.escapeshellarg($jpg));
      
      $run(escapeshellarg($jpg).' -quality 80 '.escapeshellarg($webp));
    }
    return;
  }

}
make_variants_all($dest);

$alt = trim($_POST['alt'] ?? '');
if ($alt !== '') {
  $mapFile = img_dir() . '/alt_texts.json';
  $map = file_exists($mapFile) ? json_decode(file_get_contents($mapFile), true) : [];
  if (!is_array($map)) $map = [];
  $map[$norm] = $alt;
  file_put_contents($mapFile, json_encode($map, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
}

header('Location:/admin/index.php?ok=1');
exit;
