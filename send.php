<?php
declare(strict_types=1);

// ⚠️ В продакшене лучше отключить показ ошибок:
// ini_set('display_errors', '0');
error_reporting(E_ALL);
ini_set('display_errors', '1');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// --------------------------------------------------
// Папка для сохранения заявок из формы (админка)
// --------------------------------------------------
$root  = __DIR__;
$inbox = $root . '/admin/inbox';

if (!is_dir($inbox)) {
  @mkdir($inbox, 0775, true);
}

if (!is_dir($inbox) || !is_writable($inbox)) {
  http_response_code(500);
  exit('Серверное хранилище недоступно');
}

// --------------------------------------------------
// Чтение настроек из config.json (ШАБЛОН)
// --------------------------------------------------
// Создай файл: /content/config.json
//
// Пример содержимого:
//
// {
//   "contact_email": "your@email.com",           // ← КУДА ПРИХОДЯТ ЗАЯВКИ
//   "mail_from_email": "no-reply@yourdomain.com",// ← ОТ КОГО ПИСЬМО
//   "mail_from_name": "Website Contact Form",    // ← ИМЯ ОТПРАВИТЕЛЯ
//   "smtp_host": "smtp.sendgrid.net",
//   "smtp_user": "apikey",
//   "smtp_pass": "SG.XXXXXXXXXXXXXXXXXXXXXXXX",  // ← SMTP ключ / API ключ
//   "smtp_port": 587
// }
// --------------------------------------------------
$cfg = json_decode(@file_get_contents(__DIR__ . '/content/config.json'), true) ?: [];

// ОСНОВНЫЕ НАСТРОЙКИ ПОЧТЫ
$CONTACT_EMAIL   = trim((string)($cfg['contact_email'] ?? ''));      // ОБЯЗАТЕЛЬНО
$MAIL_FROM_EMAIL = trim((string)($cfg['mail_from_email'] ?? ''));    // МОЖНО ОСТАВИТЬ ПУСТЫМ
$MAIL_FROM_NAME  = trim((string)($cfg['mail_from_name'] ?? 'Форма с сайта'));

$SMTP_HOST = trim((string)($cfg['smtp_host'] ?? 'smtp.sendgrid.net'));
$SMTP_USER = trim((string)($cfg['smtp_user'] ?? 'apikey'));
$SMTP_PASS = trim((string)($cfg['smtp_pass'] ?? ''));                // ОБЯЗАТЕЛЬНО
$SMTP_PORT = (int)($cfg['smtp_port'] ?? 587);

// ЕСЛИ НАСТРОЙКИ НЕ ЗАДАНЫ — ПИСЬМО НЕ ОТПРАВЛЯЕМ,
// НО ЗАЯВКУ ВСЁ РАВНО СОХРАНЯЕМ В admin/inbox
$MAIL_ENABLED = ($CONTACT_EMAIL !== '' && $SMTP_PASS !== '');

// --------------------------------------------------
// Данные из формы
// --------------------------------------------------
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$date    = trim($_POST['date'] ?? '');
$guests  = trim($_POST['guests'] ?? '');
$message = trim($_POST['message'] ?? '');
$ref     = trim($_POST['ref'] ?? '');

if ($name === '' || $email === '' || $date === '') {
  http_response_code(400);
  exit('Не заполнены обязательные поля');
}

// --------------------------------------------------
// Сохраняем заявку в папку admin/inbox
// --------------------------------------------------
$stamp   = date('Ymd-His');
$slug    = preg_replace('~[^a-z0-9\-]+~i','-', $name) ?: 'request';
$caseDir = $inbox . "/{$stamp}-{$slug}";
@mkdir($caseDir, 0775, true);

$meta = [
  'name'    => $name,
  'email'   => $email,
  'date'    => $date,
  'guests'  => $guests,
  'message' => $message,
  'ref'     => $ref,
  'ip'      => $_SERVER['REMOTE_ADDR'] ?? '',
  'ua'      => $_SERVER['HTTP_USER_AGENT'] ?? '',
  'time'    => date('c'),
];

file_put_contents(
  $caseDir . '/request.json',
  json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
);

// --------------------------------------------------
// Обработка загруженных файлов (фото)
// --------------------------------------------------
$savedFiles = [];
$maxFiles = 5;                      // максимум файлов
$maxSize  = 10 * 1024 * 1024;       // максимум 10 МБ на файл
$allowed  = [
  'image/jpeg' => 'jpg',
  'image/png'  => 'png',
  'image/webp' => 'webp'
];

if (!empty($_FILES['photos']) && is_array($_FILES['photos']['name'])) {
  $count = min(count($_FILES['photos']['name']), $maxFiles);

  for ($i = 0; $i < $count; $i++) {
    if (($_FILES['photos']['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) continue;
    if (($_FILES['photos']['size'][$i] ?? 0) > $maxSize) continue;

    $tmp  = $_FILES['photos']['tmp_name'][$i] ?? '';
    $type = $tmp ? (mime_content_type($tmp) ?: '') : '';
    if (!isset($allowed[$type])) continue;

    $ext  = $allowed[$type];
    $dest = $caseDir . '/' . sprintf('photo-%02d.%s', count($savedFiles) + 1, $ext);

    if ($tmp && move_uploaded_file($tmp, $dest)) {
      $savedFiles[] = $dest;
    }
  }
}

file_put_contents(
  $caseDir . '/files.json',
  json_encode($savedFiles, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
);

// --------------------------------------------------
// Отправка письма (опционально)
// --------------------------------------------------
if ($MAIL_ENABLED) {
  $mail = new PHPMailer(true);

  try {
    $mail->isSMTP();
    $mail->Host       = $SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = $SMTP_USER;
    $mail->Password   = $SMTP_PASS; // ← КЛЮЧ SMTP / API (менять в config.json)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $SMTP_PORT;

    // ОТ КОГО ПИСЬМО
    $fromEmail = $MAIL_FROM_EMAIL !== '' ? $MAIL_FROM_EMAIL : $CONTACT_EMAIL;
    $mail->setFrom($fromEmail, $MAIL_FROM_NAME);

    // КУДА ПРИХОДИТ ПИСЬМО
    $mail->addAddress($CONTACT_EMAIL);

    // ОТВЕТИТЬ ПОЛЬЗОВАТЕЛЮ
    $mail->addReplyTo($email, $name);

    // Вложения (фото)
    foreach ($savedFiles as $file) {
      $mail->addAttachment($file, basename($file));
    }

    $mail->isHTML(false);
    $mail->Subject = 'Новая заявка с сайта';
    $mail->Body =
      "Имя: $name\n" .
      "Email: $email\n" .
      "Дата: $date\n" .
      "Гостей: $guests\n" .
      "Источник: $ref\n\n" .
      "Сообщение:\n$message\n";

    $mail->send();
  } catch (Exception $e) {
    error_log("Ошибка отправки почты: {$mail->ErrorInfo}");
  }
} else {
  // ✋ Почта не настроена.
  // Заявки сохраняются ТОЛЬКО в папку admin/inbox
  // Чтобы включить отправку писем:
  // 1) Открой content/config.json
  // 2) Укажи contact_email и smtp_pass
}

// --------------------------------------------------
// Редирект после успешной отправки
// --------------------------------------------------
header('Location: /thanks.html');
exit;
