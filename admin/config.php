<?php
declare(strict_types=1);
session_start();

$ROOT = realpath(__DIR__ . '/..');   // корень сайта (на уровень выше /admin)

// === PATHS ===
define('IMG_DIR',        $ROOT . '/img/portfolio');
define('PUBLIC_PREFIX', '/img/portfolio');
define('CONTENT_DIR',    $ROOT . '/content');

if (!is_dir(CONTENT_DIR)) { @mkdir(CONTENT_DIR, 0775, true); }

if (!is_dir(IMG_DIR)) {
  http_response_code(500);
  exit("IMG dir not found: " . IMG_DIR);
}

// === ADMIN LOGIN/PASSWORD (ШАБЛОН) ===
// Хранится в content/config.json, чтобы не светить в коде и легко менять.
//
// Пример content/config.json:
// {
//   "admin_user": "admin",
//   "admin_pass": "ChangeMe123!"
// }
$cfg = json_decode(@file_get_contents(CONTENT_DIR . '/config.json'), true) ?: [];

define('ADMIN_USER', (string)($cfg['admin_user'] ?? 'admin'));
define('ADMIN_PASS', (string)($cfg['admin_pass'] ?? 'ChangeMe123!'));
