<?php
declare(strict_types=1);

require __DIR__ . '/auth.php';
require __DIR__ . '/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Method Not Allowed');
}
if (empty($_POST['file'])) {
  http_response_code(400);
  exit('No file');
}

$name = basename((string)$_POST['file']);
$dir  = img_dir();

$realDir  = realpath($dir);
$realPath = realpath($dir . DIRECTORY_SEPARATOR . $name);

if (!$realPath || strpos($realPath, $realDir) !== 0) {
  http_response_code(400);
  exit('Bad path');
}

if (is_file($realPath) && is_writable($realPath)) {
  unlink($realPath);
}

$baseNoExt = pathinfo($name, PATHINFO_FILENAME);
$variants = glob(
  $dir . DIRECTORY_SEPARATOR . $baseNoExt . '-*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}',
  GLOB_BRACE
) ?: [];
foreach ($variants as $v) {
  if (is_file($v) && is_writable($v)) {
    @unlink($v);
  }
}

$metaFile = $dir . '/alt.json';
if (is_file($metaFile) && is_writable($metaFile)) {
  $meta = json_decode((string)file_get_contents($metaFile), true) ?: [];
  unset($meta[$name]);
  file_put_contents($metaFile, json_encode($meta, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
}

header('Location: /admin/index.php?deleted=' . rawurlencode($name));
exit;
