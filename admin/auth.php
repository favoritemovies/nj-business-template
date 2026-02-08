<?php
require __DIR__ . '/config.php';
if (empty($_SESSION['logged_in'])) {
  header('Location: /admin/login.php');
  exit;
}
