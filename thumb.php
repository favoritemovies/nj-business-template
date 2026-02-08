<?php
error_reporting(0);

$src = $_GET['src'] ?? '';
$w   = (int)($_GET['w'] ?? 0);
$q   = (int)($_GET['q'] ?? 82);

if ($w <= 0) $w = 640;
if ($w > 2400) $w = 2400;              // ✅ лимит ширины
if ($q < 50 || $q > 95) $q = 82;

$root   = realpath(__DIR__);
$imgDir = realpath($root . '/img/portfolio');
if (!$root || !$imgDir) { http_response_code(404); exit; }

$path = $root . '/' . ltrim($src, '/');
$real = realpath($path);

if (!$real || strpos($real, $imgDir) !== 0 || !is_file($real)) {
  http_response_code(404); exit;
}

$cacheDir = $root . '/img/cache';
if (!is_dir($cacheDir)) @mkdir($cacheDir, 0775, true);

$hash = md5($real.'|'.$w.'|'.filemtime($real).'|'.$q);
$out  = $cacheDir . '/' . $hash . '.jpg';

if (!is_file($out)) {
  if (!function_exists('imagecreatetruecolor')) { http_response_code(500); exit; }

  $ext = strtolower(pathinfo($real, PATHINFO_EXTENSION));
  if ($ext === 'jpg' || $ext === 'jpeg') {
    $im = @imagecreatefromjpeg($real);
  } elseif ($ext === 'png') {
    $im = @imagecreatefrompng($real);
  } elseif ($ext === 'webp' && function_exists('imagecreatefromwebp')) {
    $im = @imagecreatefromwebp($real);
  } else {
    $im = @imagecreatefromstring(@file_get_contents($real));
  }
  if (!$im) { http_response_code(500); exit; }

  if (($ext === 'jpg' || $ext === 'jpeg') && function_exists('exif_read_data')) {
    $exif = @exif_read_data($real);
    $orientation = $exif['Orientation'] ?? 1;
    switch ($orientation) {
      case 3: $im = imagerotate($im, 180, 0); break;
      case 6: $im = imagerotate($im, -90, 0); break;
      case 8: $im = imagerotate($im, 90, 0); break;
    }
  }

  $ow = imagesx($im); $oh = imagesy($im);
  if ($ow > $w) {
    $nh  = (int) round($oh * ($w / $ow));
    $dst = imagecreatetruecolor($w, $nh);
    imagecopyresampled($dst, $im, 0,0,0,0, $w,$nh, $ow,$oh);
    imagedestroy($im);
    $im = $dst;
  }

  imageinterlace($im, true);
  @imagejpeg($im, $out, $q);
  imagedestroy($im);
}

$mtime = filemtime($out);
$etag  = '"' . $hash . '"';

header('Content-Type: image/jpeg');
header('Cache-Control: public, max-age=31536000, immutable');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $mtime) . ' GMT');
header('ETag: ' . $etag);
header('X-Content-Type-Options: nosniff');

if (!empty($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) {
  http_response_code(304);
  exit;
}

readfile($out);
