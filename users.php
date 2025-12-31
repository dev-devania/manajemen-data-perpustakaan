<?php
require_once __DIR__ . '/admin_guard.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/session.php';

$flash_ok = $_SESSION['flash_ok'] ?? '';
$flash_err = $_SESSION['flash_err'] ?? '';
unset($_SESSION['flash_ok'], $_SESSION['flash_err']);

$conn = db();
$res = $conn->query("SELECT id, username, role, created_at FROM users ORDER BY id ASC");
$users = $res->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Kelola User</title>
  <style>
    :root{
      --bg:#0b1220; --panel:#0f1a2e; --line:rgba(233,240,255,.12);
      --text:#e9f0ff; --muted:rgba(233,240,255,.72);
      --shadow:0 10px 30px rgba(0,0,0,.35);
      --brand:#7dd3fc; --danger:#fb7185; --radius:16px;
    }
    *{box-sizing:border-box}
    body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial;color:var(--text);background:var(--bg)}
    body::before{
      content:""; position:fixed; inset:0; z-index:-1;
      background:
        radial-gradient(1200px 800px at 10% 10%, #12234a 0%, var(--bg) 45%),
        radial-gradient(900px 600px at 80% 30%, #0f2b3f 0%, transparent 55%);
    }
    .topbar{
      position:sticky; top:0; z-index:10;
      display:flex; align-items:center; gap:10px;
      padding:12px 16px;
      background:var(--panel);
      border-bottom:1px solid var(--line);
    }
    .brand{display:flex; gap:10px; align-items:center; font-weight:900}
    .pill{
      font-size:12px;padding:6px 10px;border-radius:999px;
      border:1px solid var(--line);background:rgba(255,255,255,.04);
      color:var(--muted);
    }
    .right{margin-left:auto; display:flex; gap:10px; align-items:center}
    .btn{
      border:1px solid rgba(125,211,252,.40);
      background:rgba(125,211,252,.12);
      color:var(--text);
      padding:10px 12px;
      border-radius:14px;
      cursor:pointer;
      font-weight:800;
      text-decoration:none;
      display:inline-flex; align-items:center; justify-content:center;
    }
    .btn:hover{background:rgba(125,211,252,.18)}
    .btn--ghost{border-color:var(--line); background:rgba(255,255,255,.03)}
    .btn--danger{border-color:rgba(251,113,133,.55); background:rgba(251,113,133,.12)}
    .btn--danger:hover{background:rgba(251,113,133,.18)}
    .wrap{padding:16px; max-width:1100px; margin:0 auto}
    .grid{display:grid; grid-template-columns: 420px 1fr; gap:12px}
    .card{
      border:1px solid var(--line);
      background:rgba(16,31,58,.70);
      border-radius:18px;
      box-shadow:var(--shadow);
      overflow:hidden;
    }
    .card__p{padding:14px}
    h1{margin:0 0 10px; font-size:18px}
    label{display:block;color:var(--muted);font-size:12px;margin:10px 0 6px}
    input, select{
      width:100%; padding:10px 12px; border-radius:12px;
      border:1px solid var(--line); background:rgba(255,255,255,.03);
      color:var(--text); outline:none;
    }
    .msg{margin:12px 0 0; padding:10px 12px; border-radius:14px; font-size:13px}
    .ok{border:1px solid rgba(125,211,252,.35); background:rgba(125,211,252,.10)}
    .err{border:1px solid rgba(251,113,133,.55); background:rgba(251,113,133,.10)}
    .table-wrap{overflow:auto}
    table{width:100%; border-collapse:collapse; min-width:620px}
    th,td{padding:12px; border-bottom:1px solid var(--line); text-align:left}
    th{font-size:12px; color:var(--muted); background:rgba(255,255,255,.02)}
    .actions{display:flex; gap:8px; justify-content:flex-end}
    .small{font-size:12px; color:var(--muted); line-height:1.4}
    @media (max-width: 960px){
      .grid{grid-template-columns:1fr}
      table{min-width:0}
    }
  </style>
</head>
<body>

  <div class="topbar">
    <div class="brand">👤 Kelola User</div>
    <span class="pill">Login: <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?></span>
    <span class="pill">Role: <?php echo htmlspecialchars($_SESSION['role'], ENT_QUOTES, 'UTF-8'); ?></span>
    <div class="right">
      <a class="btn btn--ghost" href="index.php">← Kembali</a>
      <a class="btn btn--danger" href="logout.php">Logout</a>
    </div>
  </div>

  <div class="wrap">
    <div class="grid">
      <!-- Form tambah user -->
      <div class="card">
        <div class="card__p">
          <h1>Tambah Petugas</h1>
          <div class="small">
            Username disarankan huruf/angka/underscore. Password minimal 6 karakter.
          </div>

          <?php if ($flash_ok): ?>
            <div class="msg ok"><?php echo htmlspecialchars($flash_ok, ENT_QUOTES, 'UTF-8'); ?></div>
          <?php endif; ?>
          <?php if ($flash_err): ?>
            <div class="msg err"><?php echo htmlspecialchars($flash_err, ENT_QUOTES, 'UTF-8'); ?></div>
          <?php endif; ?>

          <form method="POST" action="users_save.php" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="action" value="create">

            <label>Username</label>
            <input name="username" placeholder="contoh: petugas1" required>

            <label>Password</label>
            <input name="password" type="password" placeholder="minimal 6 karakter" required>

            <label>Role</label>
            <select name="role">
              <option value="petugas" selected>petugas</option>
              <option value="admin">admin</option>
            </select>

            <div style="margin-top:12px">
              <button class="btn" type="submit">Tambah User</button>
            </div>
          </form>
        </div>
      </div>

      <!-- list user -->
      <div class="card">
        <div class="card__p">
          <h1>Daftar User</h1>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Username</th>
                  <th>Role</th>
                  <th>Dibuat</th>
                  <th style="text-align:right">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($users as $u): ?>
                  <tr>
                    <td><?php echo (int)$u['id']; ?></td>
                    <td><b><?php echo htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8'); ?></b></td>
                    <td><?php echo htmlspecialchars($u['role'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($u['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                      <div class="actions">
                        <?php if ((int)$u['id'] === (int)$_SESSION['user_id']): ?>
                          <span class="pill">Anda</span>
                        <?php else: ?>
                          <form method="POST" action="users_save.php" onsubmit="return confirm('Hapus user ini?');">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>">
                            <button class="btn btn--danger" type="submit">Hapus</button>
                          </form>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div class="small" style="margin-top:20px">
          </div>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
