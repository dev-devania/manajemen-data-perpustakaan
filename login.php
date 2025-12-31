<?php
require_once __DIR__ . '/session.php';

if (!empty($_SESSION['user_id'])) {
  header('Location: index.php');
  exit;
}

$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login — Perpustakaan Universitas Pamulang</title>
  <style>
    :root{
      --bg:#0b1220; --panel:#0f1a2e; --line:rgba(233,240,255,.12);
      --text:#e9f0ff; --muted:rgba(233,240,255,.72);
      --shadow:0 10px 30px rgba(0,0,0,.35); --radius:16px;
      --brand:#7dd3fc; --danger:#fb7185;
    }
    *{box-sizing:border-box}
    body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial;color:var(--text);background:var(--bg)}
    body::before{
      content:""; position:fixed; inset:0; z-index:-1;
      background:
        radial-gradient(1200px 800px at 10% 10%, #12234a 0%, var(--bg) 45%),
        radial-gradient(900px 600px at 80% 30%, #0f2b3f 0%, transparent 55%);
    }
    .wrap{min-height:100vh;display:grid;place-items:center;padding:18px}
    .card{
      width:min(420px, 100%);
      background:var(--panel);
      border:1px solid var(--line);
      border-radius:18px;
      box-shadow:var(--shadow);
      padding:16px;
    }
    .brand{display:flex;gap:10px;align-items:center;margin-bottom:10px}
    .logo{
      width:44px;height:44px;border-radius:14px;display:grid;place-items:center;
      background:rgba(125,211,252,.12);border:1px solid rgba(125,211,252,.25);
      font-size:20px;
    }
    .logo{ overflow:hidden; }
    .logo img{
      width:70%;
      height:70%;
      object-fit:contain;
      display:block;
    }

    .title{font-weight:900;letter-spacing:.2px}
    .sub{color:var(--muted);font-size:12px;margin-top:2px}
    .field{margin-top:12px}
    label{display:block;color:var(--muted);font-size:12px;margin-bottom:6px}
    input{
      width:100%; padding:10px 12px; border-radius:12px;
      border:1px solid var(--line); background:rgba(255,255,255,.03);
      color:var(--text); outline:none;
    }
    .btn{
      margin-top:14px; width:100%;
      border:1px solid rgba(125,211,252,.40);
      background:rgba(125,211,252,.12);
      color:var(--text); padding:10px 12px;
      border-radius:14px; cursor:pointer; font-weight:800;
    }
    .btn:hover{background:rgba(125,211,252,.18)}
    .err{
      margin-top:10px; padding:10px 12px; border-radius:14px;
      border:1px solid rgba(251,113,133,.55);
      background:rgba(251,113,133,.10); color:var(--text);
      font-size:13px;
    }
    .hint{margin-top:10px;color:var(--muted);font-size:12px;line-height:1.4}
    .row{display:flex;justify-content:space-between;gap:10px;align-items:center;margin-top:10px}
    .pill{
      font-size:12px;padding:6px 10px;border-radius:999px;
      border:1px solid var(--line);background:rgba(255,255,255,.04);
      color:var(--muted);
    }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
    <div class="brand">
      <div class="logo">
        <img src="assets/logo-un.png" alt="Logo">
      </div>
      <div>
        <div class="title">Login</div>
        <div class="sub">Perpustakaan Universitas Pamulang</div>
      </div>
    </div>

  <div class="row">
    <div class="pill">Akses dibatasi</div>
  </div>

      <?php if ($error): ?>
        <div class="err"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
      <?php endif; ?>

      <form method="POST" action="login_process.php" autocomplete="off">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <div class="field">
          <label>Username</label>
          <input name="username" required />
        </div>
        <div class="field">
          <label>Password</label>
          <input name="password" type="password" required />
        </div>
        <button class="btn" type="submit">Masuk</button>
      </form>

      <div class="hint">
        Forgot password? Contact the <b>Admin</b>.
      </div>
    </div>
  </div>
</body>
</html>
