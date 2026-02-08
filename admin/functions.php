<?php
declare(strict_types=1);

function project_root(): string {
  return realpath(__DIR__ . '/..') ?: dirname(__DIR__);
}

function img_dir(): string {
  $p = project_root() . '/img/portfolio';
  if (!is_dir($p)) @mkdir($p, 0775, true);
  return $p;
}

function public_url(string $abs): string {
  $root = project_root();
  $url  = str_replace('\\', '/', $abs);
  $url  = str_replace($root, '', $url);
  if ($url === $abs) { // fallback
    $url = '/img/portfolio/' . basename($abs);
  }
  if ($url === '' || $url[0] !== '/') $url = '/' . $url;
  return $url;
}

function is_generated_variant(string $name): bool {
  return (bool)preg_match('/-\d{2,5}\.(jpe?g|png|webp)$/i', $name);
}

function list_images(): array {
  $dir = img_dir();
  $files = glob($dir.'/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}', GLOB_BRACE) ?: [];

  $files = array_values(array_filter($files, function($abs){
    return !is_generated_variant(basename($abs));
  }));

  natsort($files);
  return array_values($files);
}
