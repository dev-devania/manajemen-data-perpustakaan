<?php
require_once __DIR__ . '/admin_guard.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: users.php');
  exit;
}

// CSRF
$csrf = $_POST['csrf_token'] ?? '';
if (!$csrf || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) {
  $_SESSION['flash_err'] = 'Permintaan tidak valid (CSRF).';
  header('Location: users.php');
  exit;
}

$action = $_POST['action'] ?? '';
$conn = db();

if ($action === 'create') {
  $username = trim($_POST['username'] ?? '');
  $password = (string)($_POST['password'] ?? '');
  $role = $_POST['role'] ?? 'petugas';

  // validasi sederhana
  if ($username === '' || $password === '') {
    $_SESSION['flash_err'] = 'Username dan password wajib diisi.';
    header('Location: users.php');
    exit;
  }

  if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
    $_SESSION['flash_err'] = 'Username harus 3-30 karakter (huruf/angka/underscore).';
    header('Location: users.php');
    exit;
  }

  if (strlen($password) < 6) {
    $_SESSION['flash_err'] = 'Password minimal 6 karakter.';
    header('Location: users.php');
    exit;
  }

  if (!in_array($role, ['admin','petugas'], true)) {
    $role = 'petugas';
  }

  $hash = password_hash($password, PASSWORD_DEFAULT);

  try {
    $stmt = $conn->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hash, $role);
    $stmt->execute();
    $_SESSION['flash_ok'] = "User '$username' berhasil ditambahkan.";
  } catch (Throwable $e) {
    $_SESSION['flash_err'] = "Gagal menambah user. Username mungkin sudah dipakai.";
  }

  header('Location: users.php');
  exit;
}

if ($action === 'delete') {
  $id = (int)($_POST['id'] ?? 0);
  if ($id <= 0) {
    $_SESSION['flash_err'] = 'ID tidak valid.';
    header('Location: users.php');
    exit;
  }

  if ($id === (int)$_SESSION['user_id']) {
    $_SESSION['flash_err'] = 'Kamu tidak bisa menghapus akun yang sedang dipakai.';
    header('Location: users.php');
    exit;
  }

  $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();

  $_SESSION['flash_ok'] = "User berhasil dihapus.";
  header('Location: users.php');
  exit;
}

$_SESSION['flash_err'] = 'Aksi tidak dikenal.';
header('Location: users.php');
exit;
