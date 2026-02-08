<?php
require __DIR__.'/auth.php';
require __DIR__.'/functions.php';

function make_variants_all(string $srcAbs): void {
  $p    = pathinfo($srcAbs);
  $dir  = dirname($srcAbs);
  $base = $p['filename'];

  if (preg_match('/-(640|1024|1600)\.(jpe?g|webp)$/i', $srcAbs)) return;

  $variants = [
    640  => ["$dir/{$base}-640.jpg",  "$dir/{$base}-640.webp"],
    1024 => ["$dir/{$base}-1024.jpg", "$dir/{$base}-1024.webp"],
    1600 => ["$dir/{$base}-1600.jpg", "$dir/{$base}-1600.webp"],
  ];

  if (class_exists('Imagick')) {
    try {
      foreach ($variants as $w => [$jpg, $webp]) {
        $im = new Imagick($srcAbs);
        $im->setImageOrientation(imagick::ORIENTATION_TOPLEFT);
        $im->stripImage();
        $im->resizeImage($w, 0, Imagick::FILTER_LANCZOS, 1, true);
        $im->setImageFormat('jpeg');  $im->setImageCompressionQuality(82); $im->writeImage($jpg);
        $im->setImageFormat('webp');  $im->setImageCompressionQuality(80); $im->writeImage($webp);
        $im->clear(); $im->destroy();
      }
      return;
    } catch (Throwable $e) {}
  }

  // GD
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
            $newW = $w; $newH = (int)round($h0 * ($newW / $w0));
            $dst = imagecreatetruecolor($newW, $newH);
            imagecopyresampled($dst, $src, 0,0,0,0, $newW,$newH, $w0,$h0);
            imagejpeg($dst, $jpg, 82);
            if (function_exists('imagewebp')) imagewebp($dst, $webp, 80);
            imagedestroy($dst);
          }
          imagedestroy($src);
          return;
        }
      }
    }
  }

  // CLI magick/convert
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
  }
}

$all = list_images();
$done = 0; $skip = 0;

foreach ($all as $abs) {
  if (preg_match('/-(640|1024|1600)\.(jpe?g|webp)$/i', $abs)) { $skip++; continue; }
  make_variants_all($abs);
  $done++;
}

?><!doctype html>
<meta charset="utf-8">
<title>Backfill variants</title>
<link rel="stylesheet" href="data:text/css,
body{font:16px/1.5 system-ui,sans-serif;margin:20px}
code{background:#f3f4f6;padding:2px 6px;border-radius:6px}"/>
<h1>Done</h1>
<p>Processed originals: <b><?= (int)$done ?></b></p>
<p>Skipped variants (already existed): <b><?= (int)$skip ?></b></p>
<p><a href="/admin/">← Back to Admin</a></p>
