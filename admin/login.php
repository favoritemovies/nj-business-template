<?php
require __DIR__ . '/config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $u = $_POST['user'] ?? '';
  $p = $_POST['pass'] ?? '';
  if ($u === ADMIN_USER && $p === ADMIN_PASS) {
    $_SESSION['logged_in'] = true;
    header('Location: /admin/index.php');
    exit;
  }
  $error = 'Invalid username or password';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin — Login</title>
<style>
body { font:16px/1.5 system-ui, sans-serif; background:#f6f7f8; margin:0; display:grid; place-items:center; height:100vh; }
.box { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:24px; width:min(380px, 92vw); box-shadow:0 10px 24px rgba(0,0,0,.06); }
h1 { margin:0 0 12px; font-size:20px; }
label{ display:block; margin:10px 0 6px; font-weight:600; }
input{ width:100%; padding:12px; border:1px solid #ddd; border-radius:10px; }
button{ margin-top:14px; padding:12px 16px; border:1px solid #000; background:#000; color:#fff; border-radius:10px; cursor:pointer; width:100%;}
.error{ color:#b91c1c; margin-top:10px; }
</style>
</head>
<body>
  <form class="box" method="post">
    <h1>Admin Login</h1>
    <label>Username</label>
    <input name="user" required>
    <label>Password</label>
    <input name="pass" type="password" required>
    <button type="submit">Sign In</button>
    <?php if ($error): ?><div class="error"><?=htmlspecialchars($error)?></div><?php endif; ?>
  </form>
</body>
</html>
