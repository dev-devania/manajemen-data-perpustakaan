<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: login.php');
  exit;
}

$csrf = $_POST['csrf_token'] ?? '';
if (!$csrf || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) {
  $_SESSION['login_error'] = 'Permintaan tidak valid (CSRF).';
  header('Location: login.php');
  exit;
}

$username = trim($_POST['username'] ?? '');
$password = (string)($_POST['password'] ?? '');

if ($username === '' || $password === '') {
  $_SESSION['login_error'] = 'Username dan password wajib diisi.';
  header('Location: login.php');
  exit;
}

$conn = db();

$stmt = $conn->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
  $_SESSION['login_error'] = 'Username atau password salah.';
  header('Location: login.php');
  exit;
}

$user = $res->fetch_assoc();

if (!password_verify($password, $user['password_hash'])) {
  $_SESSION['login_error'] = 'Username atau password salah.';
  header('Location: login.php');
  exit;
}

session_regenerate_id(true);

$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];

header('Location: index.php');
exit;
