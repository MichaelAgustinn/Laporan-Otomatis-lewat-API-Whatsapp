<?php
function loadEnv($path = null)
{
  if ($path === null) {
    $path = __DIR__ . '/.env';
  }

  if (!file_exists($path)) {
    die('.env file tidak ditemukan: ' . $path);
  }

  $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;

    list($name, $value) = explode('=', $line, 2);
    putenv(trim($name) . '=' . trim($value));
  }
}
session_start();
loadEnv();

$user = $_POST['username'] ?? '';
$pass = $_POST['password'] ?? '';

if (
  $user === getenv('DASHBOARD_USER') &&
  $pass === getenv('DASHBOARD_PASS')
) {
  $_SESSION['login'] = true;
  header('Location: index.php');
  exit;
}

header('Location: login.php?error=1');
