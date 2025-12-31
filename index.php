<?php
require_once __DIR__ . '/auth_guard.php';

$uname = htmlspecialchars($_SESSION['username'] ?? '-', ENT_QUOTES, 'UTF-8');
$role  = $_SESSION['role'] ?? 'petugas';
$roleLabel = ($role === 'admin') ? 'Admin' : 'Petugas';
?>

<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Perpustakaan SMA Pamulang</title>
  <script>
  (function () {
    const THEME_KEY = "lib_theme_v1";
    const theme = localStorage.getItem(THEME_KEY) || "dark";
    document.documentElement.setAttribute("data-theme", theme);
  })();
</script>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

  <!-- TOPBAR -->
  <header class="topbar">
    <button class="icon-btn" id="btnMenu" aria-label="Buka/Tutup menu">☰</button>

    <div class="brand__logo">
      <img class="brand__logo-img" src="assets/logo-un.png" alt="Logo">
    </div>
        <div class="brand__title">Perpustakaan SMA Pamulang</div>
    </div>

    <div class="topbar__right">
    
      <button class="btn btn--ghost theme-btn" id="btnTheme" type="button" title="Ganti tema">
        <span class="theme-btn__icon" id="themeIcon">🌙</span>
        <span class="theme-btn__text" id="themeText">Dark</span>
      </button>

      <!-- USER CHIP -->
      <div class="userchip" title="<?php echo $roleLabel; ?>">
        <div class="userchip__avatar <?php echo ($role === 'admin') ? 'is-admin' : 'is-petugas'; ?>">
          <?php if ($role === 'admin'): ?>
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="M4 7l4.5 5L12 6l3.5 6L20 7v11H4V7zm2 9h12v-6.2l-2.7 3.1L12 9.4l-3.3 3.5L6 9.8V16z"/>
            </svg>
          <?php else: ?>
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="M4 6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6zm2 0v12h12V6H6zm2 3h6v2H8V9zm0 4h8v2H8v-2zm9-4a2 2 0 1 1 0 4 2 2 0 0 1 0-4z"/>
            </svg>
          <?php endif; ?>
        </div>

        <div class="userchip__meta">
          <div class="userchip__name"><?php echo $uname; ?></div>
          <div class="userchip__role <?php echo ($role === 'admin') ? 'role-admin' : 'role-petugas'; ?>">
            <?php echo $roleLabel; ?>
          </div>
        </div>
      </div>

      <?php if ($role === 'admin'): ?>
        <a class="btn btn--ghost" href="users.php" title="Kelola user">Kelola User</a>
      <?php endif; ?>

      <a
        class="btn btn--danger"
        href="logout.php"
        onclick="return confirm('Apakah kamu benar-benar ingin logout?');"
        style="text-decoration:none;display:inline-flex;align-items:center;justify-content:center;"
      >
        Logout
      </a>
    </div>
  </header>

  <!-- OVERLAY SIDEBAR MOBILE -->
  <div class="overlay" id="overlay"></div>

  <div class="layout">
    <!-- TOPBAR -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar__inner">
        <nav class="nav" id="navMenu">
          <a class="nav__item" href="#dashboard" data-target="dashboard">
            <span class="nav__icon">🏠</span>
            <span class="nav__text">Dashboard</span>
          </a>
          <a class="nav__item" href="#books" data-target="books">
            <span class="nav__icon">📗</span>
            <span class="nav__text">Data Buku</span>
          </a>
          <a class="nav__item" href="#members" data-target="members">
            <span class="nav__icon">👤</span>
            <span class="nav__text">Data Peminjam</span>
          </a>
          <a class="nav__item" href="#loans" data-target="loans">
            <span class="nav__icon">🔁</span>
            <span class="nav__text">Peminjaman & Pengembalian</span>
          </a>
          <a class="nav__item" href="#reports" data-target="reports">
            <span class="nav__icon">📊</span>
            <span class="nav__text">Laporan</span>
          </a>
        </nav>

        <div class="note">
          <div class="note__title">Catatan</div>
          <div class="note__text">
            <b>Aturan</b>
            <ul style="margin:8px 0 0 18px; padding:0;">
              <li>Maks pinjam: <b>3 buku</b></li>
              <li>Lama pinjam: <b>7 hari</b></li>
              <li>Denda: <b>Rp1.000/hari</b></li>
            </ul>
            <div style="height:10px"></div>
            <b>Jam layanan</b>
            <ul style="margin:8px 0 0 18px; padding:0;">
              <li>Senin – Jumat: 08.00 – 20.00</li>
              <li>Sabtu: 08.00 – 15.00</li>
              <li>Minggu: Libur</li>
            </ul>
          </div>
        </div>
      </div>
    </aside>

    <!-- MAIN -->
    <main class="main" id="mainContent">

      <!-- DASHBOARD -->
      <section id="dashboard" class="panel" data-section="dashboard">
        <div class="panel__header">
          <h1 class="panel__title">Dashboard</h1>
          <div class="pill">Status: Login sebagai <b><?php echo $roleLabel; ?></b></div>
        </div>
        <div class="grid">
          <div class="card">
            <div class="card__title">Total Buku (judul)</div>
            <div class="card__value" id="statBooks">0</div>
            <div class="card__sub">Jumlah judul buku terdaftar</div>
          </div>
          <div class="card">
            <div class="card__title">Total Anggota</div>
            <div class="card__value" id="statMembers">0</div>
            <div class="card__sub">Jumlah anggota terdaftar</div>
          </div>
          <div class="card">
            <div class="card__title">Sedang Dipinjam</div>
            <div class="card__value" id="statBorrowed">0</div>
            <div class="card__sub">Transaksi aktif (belum kembali)</div>
          </div>
        </div>

        <div class="panel__footer">
          <div class="hint">
            Tip: klik menu di sidebar untuk menampilkan section tertentu.
          </div>
        </div>
      </section>

      <!-- DATA BUKU -->
      <section id="books" class="panel" data-section="books">
        <div class="panel__header">
          <h2 class="panel__title">Data Buku</h2>
          <div class="panel__tools">
            <div class="searchbar">
              <input id="qBooks" class="searchbar__input" type="search" placeholder="Cari: kode / judul / penulis" autocomplete="off">
            </div>

            <button class="btn" id="btnAddBook" type="button">+ Tambah Buku</button>
          </div>
        </div>
        <div class="tablewrap">
          <table class="table">
            <thead>
             <tr>
              <th>
                <button class="th-sort" type="button" data-sort-group="books" data-sort-key="code">
                  Kode <span class="th-sort__icon" aria-hidden="true">↕</span>
                </button>
              </th>
              <th>
                <button class="th-sort" type="button" data-sort-group="books" data-sort-key="title">
                  Judul <span class="th-sort__icon" aria-hidden="true">↕</span>
                </button>
              </th>
              <th>
                <button class="th-sort" type="button" data-sort-group="books" data-sort-key="author">
                  Penulis <span class="th-sort__icon" aria-hidden="true">↕</span>
                </button>
              </th>
              <th>
                <button class="th-sort" type="button" data-sort-group="books" data-sort-key="stock">
                  Stok <span class="th-sort__icon" aria-hidden="true">↕</span>
                </button>
              </th>
              <th class="tr">Aksi</th>
            </tr>
            </thead>
            <tbody id="tblBooks"></tbody>
          </table>
        </div>
      </section>

      <!-- DATA PEMIMJAN/ANGGOTA -->
      <section id="members" class="panel" data-section="members">
        <div class="panel__header">
          <h2 class="panel__title">Data Peminjam</h2>
          <div class="panel__tools">
            <div class="searchbar">
              <input id="qMembers" class="searchbar__input" type="search" placeholder="Cari: ID / nama / kelas" autocomplete="off">
            </div>

            <button class="btn" id="btnAddMember" type="button">+ Tambah Anggota</button>
          </div>
        </div>
        <div class="tablewrap">
          <table class="table">
            <thead>
              <tr>
                <th>
                  <button class="th-sort" type="button" data-sort-group="members" data-sort-key="memberId">
                    ID <span class="th-sort__icon" aria-hidden="true">↕</span>
                  </button>
                </th>
                <th>
                  <button class="th-sort" type="button" data-sort-group="members" data-sort-key="name">
                    Nama <span class="th-sort__icon" aria-hidden="true">↕</span>
                  </button>
                </th>
                <th>
                  <button class="th-sort" type="button" data-sort-group="members" data-sort-key="className">
                    Kelas <span class="th-sort__icon" aria-hidden="true">↕</span>
                  </button>
                </th>
                <th>Status</th>
                <th class="tr">Aksi</th>
              </tr>
            </thead>
            <tbody id="tblMembers"></tbody>
          </table>
        </div>
      </section>

      <!-- PEMINJAMAN -->
      <section id="loans" class="panel" data-section="loans">
        <div class="panel__header">
          <h2 class="panel__title">Peminjaman & Pengembalian</h2>
          <div class="panel__tools">
            <div class="searchbar">
              <input id="qLoans" class="searchbar__input" type="search" placeholder="Cari: nama / ID / judul" autocomplete="off">
            </div>

            <button class="btn" id="btnAddLoan" type="button">+ Transaksi</button>
          </div>
        </div>
        <div class="tablewrap">
          <table class="table">
            <thead>
              <tr>
                <th>
                  <button class="th-sort" type="button" data-sort-group="loans" data-sort-key="date">
                    Tanggal <span class="th-sort__icon" aria-hidden="true">↕</span>
                  </button>
                </th>
                <th>
                  <button class="th-sort" type="button" data-sort-group="loans" data-sort-key="memberName">
                    Anggota <span class="th-sort__icon" aria-hidden="true">↕</span>
                  </button>
                </th>
                <th>
                  <button class="th-sort" type="button" data-sort-group="loans" data-sort-key="bookTitle">
                    Buku <span class="th-sort__icon" aria-hidden="true">↕</span>
                  </button>
                </th>
                <th>
                  <button class="th-sort" type="button" data-sort-group="loans" data-sort-key="status">
                    Status <span class="th-sort__icon" aria-hidden="true">↕</span>
                  </button>
                </th>
                <th class="tr">Aksi</th>
              </tr>
            </thead>
            <tbody id="tblLoans"></tbody>
          </table>
        </div>
      </section>

      <!-- LAPORAN -->
      <section id="reports" class="panel" data-section="reports">
        <div class="panel__header">
          <h2 class="panel__title">Laporan</h2>
          <button class="btn btn--ghost" id="btnExport" type="button">Export</button>
        </div>

        <div class="card card--wide">
          <div class="card__title">Ringkasan</div>
          <div class="card__sub" id="reportSummary">
            -
          </div>
        </div>
      </section>

    </main>
  </div>

  <!-- MODAL OVERLAY -->
  <div class="modal-overlay" id="modalOverlay" aria-hidden="true"></div>

  <!-- MODAL: BUKU -->
  <div class="modal" id="modalBook" role="dialog" aria-modal="true" aria-labelledby="modalBookTitle" hidden>
    <div class="modal__header">
      <div>
        <div class="modal__title" id="modalBookTitle">Tambah Buku</div>
        <div class="modal__sub">Masukkan data buku (disimpan di LocalStorage)</div>
      </div>
      <button class="icon-btn icon-btn--sm" data-close="modalBook" aria-label="Tutup">✕</button>
    </div>

    <form class="modal__body" id="formBook">
      <div class="form-grid">
        <div class="field">
          <label>Kode Buku</label>
          <input name="code" placeholder="contoh: BK-001" required />
        </div>
        <div class="field">
          <label>Stok</label>
          <input name="stock" type="number" min="0" step="1" placeholder="contoh: 5" required />
        </div>
        <div class="field full">
          <label>Judul</label>
          <input name="title" placeholder="contoh: Laskar Pelangi" required />
        </div>
        <div class="field full">
          <label>Penulis</label>
          <input name="author" placeholder="contoh: Andrea Hirata" required />
        </div>
      </div>

      <div class="modal__actions">
        <button class="btn btn--ghost" type="button" data-close="modalBook">Batal</button>
        <button class="btn" type="submit">Simpan</button>
      </div>
    </form>
  </div>

<!-- MODAL: PEMINJAM/ANGGOTA -->
<div class="modal" id="modalMember" role="dialog" aria-modal="true" aria-labelledby="modalMemberTitle" hidden>
  <div class="modal__header">
    <div>
      <div class="modal__title" id="modalMemberTitle">Tambah Anggota</div>
      <div class="modal__sub">Masukkan data peminjam</div>
    </div>
    <button class="icon-btn icon-btn--sm" data-close="modalMember" aria-label="Tutup">✕</button>
  </div>

  <form class="modal__body" id="formMember">
    <div class="form-grid">
      <div class="field full">
        <label>ID Anggota</label>
        <input name="memberId" id="memberId" placeholder="contoh: 8564226543" required />
      </div>

      <div class="field full">
        <label>Nama</label>
        <input name="name" id="memberName" placeholder="contoh: Hanni NJ" required />
      </div>

      <!-- KELASNYA -->
      <div class="field full">
        <label>Kelas</label>
        <select name="class" id="memberClass" required>
          
          <option value="X IPA-1">X IPA-1</option>
          <option value="X IPA-2">X IPA-2</option>
          <option value="X IPA-3">X IPA-3</option>
          <option value="X IPA-4">X IPA-4</option>

          <option value="X IPS-1">X IPS-1</option>
          <option value="X IPS-2">X IPS-2</option>
          <option value="X IPS-3">X IPS-3</option>
          <option value="X IPS-4">X IPS-4</option>

          <option value="XI IPA-1">XI IPA-1</option>
          <option value="XI IPA-2">XI IPA-2</option>
          <option value="XI IPA-3">XI IPA-3</option>
          <option value="XI IPA-4">XI IPA-4</option>

          <option value="XI IPS-1">XI IPS-1</option>
          <option value="XI IPS-2">XI IPS-2</option>
          <option value="XI IPS-3">XI IPS-3</option>
          <option value="XI IPS-4">XI IPS-4</option>

          <option value="XII IPA-1">XII IPA-1</option>
          <option value="XII IPA-2">XII IPA-2</option>
          <option value="XII IPA-3">XII IPA-3</option>
          <option value="XII IPA-4">XII IPA-4</option>

          <option value="XII IPS-1">XII IPS-1</option>
          <option value="XII IPS-2">XII IPS-2</option>
          <option value="XII IPS-3">XII IPS-3</option>
          <option value="XII IPS-4">XII IPS-4</option>
        </select>
      </div>
    </div>

    <div class="modal__actions">
      <button class="btn btn--ghost" type="button" data-close="modalMember">Batal</button>
      <button class="btn" type="submit">Simpan</button>
    </div>
  </form>
</div>


  <!-- MODAL TRANSAKSI -->
  <div class="modal" id="modalLoan" role="dialog" aria-modal="true" aria-labelledby="modalLoanTitle" hidden>
    <div class="modal__header">
      <div>
        <div class="modal__title" id="modalLoanTitle">Transaksi</div>
        <div class="modal__sub">Pinjam / Kembalikan buku</div>
      </div>
      <button class="icon-btn icon-btn--sm" data-close="modalLoan" aria-label="Tutup">✕</button>
    </div>

    <form class="modal__body" id="formLoan">
      <div class="tabs" role="tablist">
        <button class="tab is-active" type="button" data-tab="borrow">Pinjam</button>
        <button class="tab" type="button" data-tab="return">Kembalikan</button>
      </div>

      <div class="tabpanes">
        <!-- BORROW PANE -->
        <div class="tabpane is-visible" data-pane="borrow">
          <div class="form-grid">
            <div class="field">
              <label>Tanggal</label>
              <input name="date" type="date" required />
            </div>

            <div class="field full">
              <label>Anggota</label>
              <select name="memberSelect" required></select>
              <div class="help">Hanya anggota status “aktif”.</div>
            </div>

            <div class="field full">
              <label>Buku</label>
              <select name="bookSelect" required></select>
              <div class="help">Hanya buku dengan stok &gt; 0.</div>
            </div>
          </div>
        </div>

        <!-- RETURN PANE -->
        <div class="tabpane" data-pane="return">
          <div class="form-grid">
            <div class="field full">
              <label>Pilih Transaksi Aktif</label>
              <select name="activeLoanSelect"></select>
              <div class="help">Daftar transaksi “Dipinjam”.</div>
            </div>
          </div>
        </div>
      </div>

      <div class="modal__actions">
        <button class="btn btn--ghost" type="button" data-close="modalLoan">Batal</button>
        <button class="btn" type="submit">Simpan</button>
      </div>
    </form>
  </div>

  <!-- TOAST -->
  <div class="toast" id="toast" hidden></div>

  <script>
    // HELPERS
    const $ = (sel, el=document) => el.querySelector(sel);
    const $$ = (sel, el=document) => Array.from(el.querySelectorAll(sel));

    function toast(msg) {
      const t = $('#toast');
      t.textContent = msg;
      t.hidden = false;
      t.classList.add('is-visible');
      clearTimeout(window.__toastTimer);
      window.__toastTimer = setTimeout(() => {
        t.classList.remove('is-visible');
        setTimeout(() => t.hidden = true, 160);
      }, 1800);
    }

    function todayISO() {
      const d = new Date();
      const yyyy = d.getFullYear();
      const mm = String(d.getMonth()+1).padStart(2,'0');
      const dd = String(d.getDate()).padStart(2,'0');
      return `${yyyy}-${mm}-${dd}`;
    }


    // TEMA (dark/light)
    const btnTheme = $('#btnTheme');
    const themeIcon = $('#themeIcon');
    const themeText = $('#themeText');

    function applyTheme(theme) {
      document.body.dataset.theme = theme;
      localStorage.setItem('theme', theme);
      if (theme === 'light') { themeIcon.textContent = '☀️'; themeText.textContent = 'Light'; }
      else { themeIcon.textContent = '🌙'; themeText.textContent = 'Dark'; }
    }

    const savedTheme = localStorage.getItem('theme') || 'dark';
    applyTheme(savedTheme);

    btnTheme.addEventListener('click', () => {
      const next = (document.body.dataset.theme === 'light') ? 'dark' : 'light';
      applyTheme(next);
    });


    // SIDEBAR TOGGLE (DESKTOP+MOBILE)
    const body = document.body;
    const btnMenu = $('#btnMenu');
    const overlay = $('#overlay');
    const main = $('#mainContent');

    const savedCollapsed = localStorage.getItem('sidebarCollapsed');
    if (savedCollapsed === '1') body.classList.add('sidebar-collapsed');

    function isMobile() { return window.matchMedia('(max-width: 980px)').matches; }

    function openMobileSidebar() {
      body.classList.add('sidebar-open');
      overlay.classList.add('is-visible');
    }
    function closeMobileSidebar() {
      body.classList.remove('sidebar-open');
      overlay.classList.remove('is-visible');
    }

    btnMenu.addEventListener('click', () => {
      if (isMobile()) {
        body.classList.contains('sidebar-open') ? closeMobileSidebar() : openMobileSidebar();
        return;
      }
      body.classList.toggle('sidebar-collapsed');
      localStorage.setItem('sidebarCollapsed', body.classList.contains('sidebar-collapsed') ? '1' : '0');
    });

    overlay.addEventListener('click', closeMobileSidebar);
    window.addEventListener('resize', () => { if (!isMobile()) closeMobileSidebar(); });

 
    // ROUTER
    const panels = $$('.panel[data-section]');
    const navItems = $$('.nav__item[data-target]');

    function setActiveNav(target) {
      navItems.forEach(a => a.classList.toggle('is-active', a.dataset.target === target));
    }

    function showSection(target) {
      const exists = panels.some(p => p.dataset.section === target);
      const finalTarget = exists ? target : 'dashboard';
      panels.forEach(p => p.classList.toggle('is-visible', p.dataset.section === finalTarget));
      setActiveNav(finalTarget);
      main.scrollTo({ top: 0, behavior: 'smooth' });
      if (isMobile()) closeMobileSidebar();
    }

    function getTargetFromHash() {
      const h = (window.location.hash || '').replace('#', '').trim();
      return h || 'dashboard';
    }

    showSection(getTargetFromHash());

    navItems.forEach(a => {
      a.addEventListener('click', (e) => {
        e.preventDefault();
        const target = a.dataset.target;
        window.location.hash = target;
        showSection(target);
      });
    });

    window.addEventListener('hashchange', () => showSection(getTargetFromHash()));

 
    // DATA STORAGE (LocalStorage)
    const KEY_BOOKS = 'lib_books';
    const KEY_MEMBERS = 'lib_members';
    const KEY_LOANS = 'lib_loans';

    function loadJSON(key, fallback) {
      try { return JSON.parse(localStorage.getItem(key) || ''); } catch { return fallback; }
    }
    function saveJSON(key, data) { localStorage.setItem(key, JSON.stringify(data)); }

    function getBooks() { return loadJSON(KEY_BOOKS, []); }
    function getMembers() { return loadJSON(KEY_MEMBERS, []); }
    function getLoans() { return loadJSON(KEY_LOANS, []); }

    function setBooks(v) { saveJSON(KEY_BOOKS, v); }
    function setMembers(v) { saveJSON(KEY_MEMBERS, v); }
    function setLoans(v) { saveJSON(KEY_LOANS, v); }

 
    // RENDER
    const tblBooks = $('#tblBooks');
    const tblMembers = $('#tblMembers');
    const tblLoans = $('#tblLoans');
    

    // SORTING ASC/DESC
    const collator = new Intl.Collator('id', { numeric: true, sensitivity: 'base' });

    const sortState = {
      books:   { key: null, dir: 'asc' },
      members: { key: null, dir: 'asc' },
      loans:   { key: null, dir: 'asc' },
    };

    const sortDefs = {
      books: {
        code:   { type: 'text',   get: b => b.code },
        title:  { type: 'text',   get: b => b.title },
        author: { type: 'text',   get: b => b.author },
        stock:  { type: 'number', get: b => Number(b.stock || 0) },
      },
      members: {
        memberId:  { type: 'text', get: m => m.memberId },
        name:      { type: 'text', get: m => m.name },
        className: { type: 'text', get: m => m.className },
      },
      loans: {
        date:       { type: 'text', get: l => l.date }, 
        memberName: { type: 'text', get: l => l.memberName },
        bookTitle:  { type: 'text', get: l => l.bookTitle },
        status:     { type: 'text', get: l => l.status },
        _default:   { type: 'number', get: l => (l.createdAt || 0) },
      }
    };

    function sortList(list, group) {
      const s = sortState[group];
      const defs = sortDefs[group];
      if (!s || !defs) return list.slice();

      if (group === 'loans' && !s.key) {
        return list.slice().sort((a,b) => (defs._default.get(b) - defs._default.get(a)));
      }

      const def = s.key ? defs[s.key] : null;
      if (!def) return list.slice();

      const dirMul = (s.dir === 'desc') ? -1 : 1;

      return list.slice().sort((a, b) => {
        const av = def.get(a);
        const bv = def.get(b);

        let cmp = 0;
        if (def.type === 'number') cmp = (Number(av) || 0) - (Number(bv) || 0);
        else cmp = collator.compare(String(av ?? ''), String(bv ?? ''));

        if (cmp === 0) {
          if (group === 'loans') return (sortDefs.loans._default.get(b) - sortDefs.loans._default.get(a));
          if (group === 'books') return collator.compare(String(a.code ?? ''), String(b.code ?? ''));
          if (group === 'members') return collator.compare(String(a.memberId ?? ''), String(b.memberId ?? ''));
        }

        return cmp * dirMul;
      });
    }

    function updateSortIcons(group) {
      const s = sortState[group];
      document.querySelectorAll(`.th-sort[data-sort-group="${group}"]`).forEach(btn => {
        const icon = btn.querySelector('.th-sort__icon');
        if (!icon) return;

        const key = btn.dataset.sortKey;
        const active = (s.key === key);

        btn.classList.toggle('is-active', active);
        icon.textContent = active ? (s.dir === 'asc' ? '▲' : '▼') : '↕';
      });
    }

    function bindSortHeaders() {
      document.addEventListener('click', (e) => {
        const btn = e.target.closest('.th-sort');
        if (!btn) return;

        const group = btn.dataset.sortGroup;
        const key = btn.dataset.sortKey;
        const s = sortState[group];
        if (!s) return;

        if (s.key === key) s.dir = (s.dir === 'asc') ? 'desc' : 'asc';
        else { s.key = key; s.dir = 'asc'; }

        updateSortIcons(group);

        if (group === 'books') renderBooks();
        if (group === 'members') renderMembers();
        if (group === 'loans') renderLoans();
      });

      updateSortIcons('books');
      updateSortIcons('members');
      updateSortIcons('loans');
    }


    function esc(s) {
      return String(s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
    }

    function renderStats() {
      const books = getBooks();
      const members = getMembers();
      const loans = getLoans();
      const active = loans.filter(x => x.status === 'Dipinjam').length;

      $('#statBooks').textContent = books.length;
      $('#statMembers').textContent = members.length;
      $('#statBorrowed').textContent = active;

      // SUMMARY LAPORAN
      const totalStock = books.reduce((a,b) => a + Number(b.stock || 0), 0);
      const borrowedTitles = loans.filter(x => x.status === 'Dipinjam').length;

      $('#reportSummary').innerHTML =
        `Total judul buku: <b>${books.length}</b> • Total stok: <b>${totalStock}</b><br>` +
        `Total anggota: <b>${members.length}</b> • Transaksi aktif: <b>${borrowedTitles}</b>`;
    }

    function renderBooks() {
      const books = getBooks();
      const q = ($('#qBooks')?.value || '').toString().trim().toLowerCase();

      if (!books.length) {
        tblBooks.innerHTML = `<tr><td colspan="5" class="muted">Belum ada data (demo).</td></tr>`;
        return;
      }

      const filtered = !q
        ? books
        : books.filter(b => (`${b.code} ${b.title} ${b.author}`).toLowerCase().includes(q));

      const rows = sortList(filtered, 'books');

      if (!rows.length) {
        tblBooks.innerHTML = `<tr><td colspan="5" class="muted">Tidak ada hasil.</td></tr>`;
        return;
      }

      tblBooks.innerHTML = rows.map(b => `
        <tr>
          <td><b>${esc(b.code)}</b></td>
          <td>${esc(b.title)}</td>
          <td>${esc(b.author)}</td>
          <td>${Number(b.stock)}</td>
          <td class="tr">
            <button class="btn btn--ghost btn--sm" data-edit-book="${esc(b.code)}">Edit</button>
            <button class="btn btn--ghost btn--sm" data-del-book="${esc(b.code)}">Hapus</button>
          </td>
        </tr>
      `).join('');
    }


    function renderMembers() {
      const members = getMembers();
      const q = ($('#qMembers')?.value || '').toString().trim().toLowerCase();

      if (!members.length) {
        tblMembers.innerHTML = `<tr><td colspan="5" class="muted">Belum ada data (demo).</td></tr>`;
        return;
      }

      const filtered = !q
        ? members
        : members.filter(m => (`${m.memberId} ${m.name} ${m.className} ${m.status || ''}`)
            .toLowerCase()
            .includes(q)
          );

      const rows = sortList(filtered, 'members');

      if (!rows.length) {
        tblMembers.innerHTML = `<tr><td colspan="5" class="muted">Tidak ada hasil.</td></tr>`;
        return;
      }

      tblMembers.innerHTML = rows.map(m => `
        <tr>
          <td><b>${esc(m.memberId)}</b></td>
          <td>${esc(m.name)}</td>
          <td>${esc(m.className)}</td>
          <td><span class="badge ${m.status === 'aktif' ? 'badge--ok' : 'badge--muted'}">${esc(m.status || 'aktif')}</span></td>
          <td class="tr">
            <button type="button" class="btn btn--ghost btn--sm" data-edit-member="${esc(m.memberId)}">Edit</button>
            <button type="button" class="btn btn--ghost btn--sm" data-del-member="${esc(m.memberId)}">Hapus</button>
          </td>
        </tr>
      `).join('');
    }


    function renderLoans() {
      const loans = getLoans();
      const q = ($('#qLoans')?.value || '').toString().trim().toLowerCase();

      if (!loans.length) {
        tblLoans.innerHTML = `<tr><td colspan="5" class="muted">Belum ada transaksi (demo).</td></tr>`;
        return;
      }

      const filtered = !q
        ? loans
        : loans.filter(l => (`${l.date} ${l.memberName} ${l.memberId} ${l.bookTitle} ${l.bookCode} ${l.status}`)
            .toLowerCase()
            .includes(q)
          );

      const rows = sortList(filtered, 'loans');

      if (!rows.length) {
        tblLoans.innerHTML = `<tr><td colspan="5" class="muted">Tidak ada hasil.</div></td></tr>`;
        return;
      }

      tblLoans.innerHTML = rows.map(l => `
        <tr>
          <td>${esc(l.date)}</td>
          <td>${esc(l.memberName)} <span class="muted">(${esc(l.memberId)})</span></td>
          <td>${esc(l.bookTitle)} <span class="muted">(${esc(l.bookCode)})</span></td>
          <td>
            <span class="badge ${l.status === 'Dipinjam' ? 'badge--warn' : 'badge--ok'}">${esc(l.status)}</span>
          </td>
          <td class="tr">
            ${l.status === 'Dipinjam' ? `<button class="btn btn--ghost btn--sm" data-return="${esc(l.id)}">Kembalikan</button>` : ''}
          </td>
        </tr>
      `).join('');
    }


    function rerenderAll() {
      renderStats();
      renderBooks();
      renderMembers();
      renderLoans();
      refreshLoanModalOptions();
    }

  
    // MODAL SYSTEM
    const modalOverlay = $('#modalOverlay');

    function openModal(id) {
      const m = document.getElementById(id);
      if (!m) return;
      m.hidden = false;
      modalOverlay.classList.add('is-visible');
      modalOverlay.setAttribute('aria-hidden', 'false');
      body.classList.add('modal-open');
      // focus first input
      const first = m.querySelector('input, select, button');
      if (first) setTimeout(() => first.focus(), 0);
    }

    function closeModal(id) {
      const m = document.getElementById(id);
      if (!m) return;
      m.hidden = true;
      modalOverlay.classList.remove('is-visible');
      modalOverlay.setAttribute('aria-hidden', 'true');
      body.classList.remove('modal-open');
    }

    modalOverlay.addEventListener('click', () => {
      ['modalBook','modalMember','modalLoan'].forEach(id => {
        const m = document.getElementById(id);
        if (m && !m.hidden) closeModal(id);
      });
    });

    $$('[data-close]').forEach(btn => {
      btn.addEventListener('click', () => closeModal(btn.dataset.close));
    });


    // EDIT STATE
    let editingBookCode = '';
    let editingMemberId = '';

    // open buttons
    $('#btnAddBook').addEventListener('click', () => {
      editingBookCode = '';
      $('#modalBookTitle').textContent = 'Tambah Buku';
      $('#formBook').reset();
      openModal('modalBook');
    });

    $('#btnAddMember').addEventListener('click', () => {
      const form = $('#formMember');
      form.reset();
      delete form.dataset.editingMemberId;
      $('#modalMemberTitle').textContent = 'Tambah Anggota';
      openModal('modalMember');
    });


    $('#btnAddLoan').addEventListener('click', () => {
      refreshLoanModalOptions();
      openModal('modalLoan');
    });

    $('#qBooks')?.addEventListener('input', renderBooks);
    $('#qMembers')?.addEventListener('input', renderMembers);
    $('#qLoans')?.addEventListener('input', renderLoans);



    // FORM ADD BOOK
    $('#formBook').addEventListener('submit', (e) => {
      e.preventDefault();
      const fd = new FormData(e.target);
      const code = (fd.get('code') || '').toString().trim();
      const title = (fd.get('title') || '').toString().trim();
      const author = (fd.get('author') || '').toString().trim();
      const stock = Number(fd.get('stock') || 0);

      if (!code || !title || !author || Number.isNaN(stock) || stock < 0) {
        toast('Data buku belum valid.');
        return;
      }

      const books = getBooks();
      const loans = getLoans();

      const newCodeLower = code.toLowerCase();
      const oldCodeLower = (editingBookCode || '').toLowerCase();

      const codeUsedByOther = books.some(b =>
        b.code.toLowerCase() === newCodeLower && b.code.toLowerCase() !== oldCodeLower
      );
      if (codeUsedByOther) {
        toast('Kode buku sudah dipakai.');
        return;
      }

      if (editingBookCode) {
        // buat edit
        const oldCode = editingBookCode;
        const idx = books.findIndex(b => b.code === oldCode);
        if (idx === -1) { toast('Data buku tidak ditemukan.'); return; }

        books[idx] = { ...books[idx], code, title, author, stock: Math.floor(stock) };

        loans.forEach(l => {
          if (l.bookCode === oldCode) {
            l.bookCode = code;
            l.bookTitle = title;
          }
        });

        setBooks(books);
        setLoans(loans);

        editingBookCode = '';
        toast('Buku berhasil diupdate.');
      } else {
        // buat add
        books.push({ code, title, author, stock: Math.floor(stock) });
        setBooks(books);
        toast('Buku berhasil ditambahkan.');
      }

      e.target.reset();
      closeModal('modalBook');
      rerenderAll();
    });

    function openMemberEdit(memberId) {
      const members = getMembers();
      const m = members.find(x => x.memberId === memberId);
      if (!m) { toast('Data anggota tidak ditemukan.'); return; }

      const form = $('#formMember');
      form.dataset.editingMemberId = m.memberId;

      $('#modalMemberTitle').textContent = 'Edit Anggota';
      $('#memberId').value = m.memberId;
      $('#memberName').value = m.name;
      $('#memberClass').value = m.className;

      openModal('modalMember');
    }


    // FORM ADD MEMBER
    $('#formMember').addEventListener('submit', (e) => {
      e.preventDefault();

      const form = e.target;
      const fd = new FormData(form);

      const memberId  = (fd.get('memberId') || '').toString().trim();
      const name      = (fd.get('name') || '').toString().trim();
      const className = (fd.get('class') || '').toString().trim();

      if (!memberId || !name || !className) {
        toast('Data peminjam belum lengkap.');
        return;
      }

      const members = getMembers();
      const editingId = (form.dataset.editingMemberId || '').trim();

      if (members.some(m => m.memberId.toLowerCase() === memberId.toLowerCase() && m.memberId !== editingId)) {
        toast('ID anggota sudah dipakai.');
        return;
      }

      if (editingId) {
        // buat ediy
        const idx = members.findIndex(m => m.memberId === editingId);
        if (idx === -1) {
          toast('Data anggota tidak ditemukan (gagal edit).');
          delete form.dataset.editingMemberId;
          return;
        }

        const old = members[idx];
        members[idx] = {
          ...old,
          memberId,
          name,
          className,
          status: old.status || 'aktif',
        };
        setMembers(members);

        const loans = getLoans();
        let changed = false;
        loans.forEach(l => {
          if (l.memberId === editingId) {
            l.memberId = memberId;
            l.memberName = name;
            changed = true;
          }
        });
        if (changed) setLoans(loans);

        toast('Data anggota berhasil diupdate.');
      } else {
        // buat add
        members.push({ memberId, name, className, status: 'aktif' });
        setMembers(members);
        toast('Anggota berhasil ditambahkan.');
      }

      form.reset();
      delete form.dataset.editingMemberId;
      $('#modalMemberTitle').textContent = 'Tambah Anggota';
      closeModal('modalMember');
      rerenderAll();
    });


    // LOAN MODAL TABS+OPTIONS
    const tabs = $$('.tab', $('#formLoan'));
    const panes = $$('.tabpane', $('#formLoan'));
    let currentLoanTab = 'borrow';

    function setLoanTab(tab) {
      currentLoanTab = tab;
      tabs.forEach(t => t.classList.toggle('is-active', t.dataset.tab === tab));
      panes.forEach(p => p.classList.toggle('is-visible', p.dataset.pane === tab));
    }

    tabs.forEach(t => t.addEventListener('click', () => setLoanTab(t.dataset.tab)));

    function refreshLoanModalOptions() {
      const dateInput = $('#formLoan [name="date"]');
      if (dateInput) dateInput.value = todayISO();

      // members aktif
      const memberSel = $('#formLoan [name="memberSelect"]');
      const members = getMembers().filter(m => m.status === 'aktif');
      memberSel.innerHTML = members.length
        ? members.map(m => `<option value="${esc(m.memberId)}">${esc(m.name)} (${esc(m.memberId)})</option>`).join('')
        : `<option value="">(Belum ada anggota aktif)</option>`;

      // books stok > 0
      const bookSel = $('#formLoan [name="bookSelect"]');
      const books = getBooks().filter(b => Number(b.stock) > 0);
      bookSel.innerHTML = books.length
        ? books.map(b => `<option value="${esc(b.code)}">${esc(b.title)} (${esc(b.code)}) • stok ${Number(b.stock)}</option>`).join('')
        : `<option value="">(Belum ada buku tersedia)</option>`;

      // active loans
      const activeLoanSel = $('#formLoan [name="activeLoanSelect"]');
      const loans = getLoans().filter(l => l.status === 'Dipinjam');
      activeLoanSel.innerHTML = loans.length
        ? loans.map(l => `<option value="${esc(l.id)}">${esc(l.memberName)} - ${esc(l.bookTitle)} (${esc(l.date)})</option>`).join('')
        : `<option value="">(Tidak ada transaksi aktif)</option>`;

      // pilih default tab
      setLoanTab('borrow');
    }


    // FORM LOAN PINJAM/KEMBALIKAN
    $('#formLoan').addEventListener('submit', (e) => {
      e.preventDefault();

      if (currentLoanTab === 'borrow') {
        const fd = new FormData(e.target);
        const date = (fd.get('date') || '').toString();
        const memberId = (fd.get('memberSelect') || '').toString();
        const bookCode = (fd.get('bookSelect') || '').toString();

        if (!date || !memberId || !bookCode) {
          toast('Lengkapi data transaksi.');
          return;
        }

        const members = getMembers();
        const books = getBooks();
        const m = members.find(x => x.memberId === memberId);
        const b = books.find(x => x.code === bookCode);

        if (!m || m.status !== 'aktif') { toast('Anggota tidak valid / nonaktif.'); return; }
        if (!b || Number(b.stock) <= 0) { toast('Stok buku habis.'); return; }

        b.stock = Number(b.stock) - 1;

        const loans = getLoans();
        const id = crypto?.randomUUID ? crypto.randomUUID() : String(Date.now()) + Math.random().toString(16).slice(2);
        loans.push({
          id,
          date,
          memberId: m.memberId,
          memberName: m.name,
          bookCode: b.code,
          bookTitle: b.title,
          status: 'Dipinjam',
          createdAt: Date.now()
        });

        setBooks(books);
        setLoans(loans);

        closeModal('modalLoan');
        rerenderAll();
        toast('Transaksi peminjaman berhasil.');
        return;
      }

      const fd = new FormData(e.target);
      const loanId = (fd.get('activeLoanSelect') || '').toString();
      if (!loanId) { toast('Pilih transaksi aktif.'); return; }

      const loans = getLoans();
      const books = getBooks();
      const loan = loans.find(x => x.id === loanId);
      if (!loan || loan.status !== 'Dipinjam') {
        toast('Transaksi tidak valid.');
        return;
      }

      const b = books.find(x => x.code === loan.bookCode);
      if (b) b.stock = Number(b.stock) + 1;

      loan.status = 'Dikembalikan';
      loan.returnedAt = Date.now();

      setBooks(books);
      setLoans(loans);

      closeModal('modalLoan');
      rerenderAll();
      toast('Pengembalian berhasil.');
    });


    //DELETE+QUICK RETRUN
    tblBooks.addEventListener('click', (e) => {
      const btnEdit = e.target.closest('[data-edit-book]');
      if (btnEdit) {
        const code = btnEdit.getAttribute('data-edit-book');
        const b = getBooks().find(x => x.code === code);
        if (!b) { toast('Data buku tidak ditemukan.'); return; }

        editingBookCode = code;
        $('#modalBookTitle').textContent = 'Edit Buku';

        const form = $('#formBook');
        form.querySelector('[name="code"]').value = b.code;
        form.querySelector('[name="title"]').value = b.title;
        form.querySelector('[name="author"]').value = b.author;
        form.querySelector('[name="stock"]').value = Number(b.stock);

        openModal('modalBook');
        return;
      }

      const btnDel = e.target.closest('[data-del-book]');
      if (!btnDel) return;

      const code = btnDel.getAttribute('data-del-book');
      if (!confirm('Hapus buku ini?')) return;

      const loans = getLoans();
      if (loans.some(l => l.status === 'Dipinjam' && l.bookCode === code)) {
        toast('Tidak bisa hapus: masih ada transaksi aktif.');
        return;
      }

      const books = getBooks().filter(b => b.code !== code);
      setBooks(books);
      rerenderAll();
      toast('Buku dihapus.');
    });


    tblMembers.addEventListener('click', (e) => {
      const btnEdit = e.target.closest('[data-edit-member]');
      if (btnEdit) {
        const id = btnEdit.getAttribute('data-edit-member');
        openMemberEdit(id);
        return;
      }

      const btnDel = e.target.closest('[data-del-member]');
      if (!btnDel) return;

      const id = btnDel.getAttribute('data-del-member');
      if (!confirm('Hapus anggota ini?')) return;

      const loans = getLoans();
      if (loans.some(l => l.status === 'Dipinjam' && l.memberId === id)) {
        toast('Tidak bisa hapus: anggota masih meminjam.');
        return;
      }

      const members = getMembers().filter(m => m.memberId !== id);
      setMembers(members);
      rerenderAll();
      toast('Anggota dihapus.');
    });


    tblLoans.addEventListener('click', (e) => {
      const btn = e.target.closest('[data-return]');
      if (!btn) return;
      const loanId = btn.getAttribute('data-return');
      refreshLoanModalOptions();
      openModal('modalLoan');
      setLoanTab('return');
      const sel = $('#formLoan [name="activeLoanSelect"]');
      sel.value = loanId;
    });


    // EXPORT (.CSV)
    function toCSVRow(arr) {
      return arr.map(v => `"${String(v ?? '').replaceAll('"', '""')}"`).join(',');
    }

    function downloadText(filename, content, mime='text/plain') {
      const blob = new Blob([content], { type: mime + ';charset=utf-8' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = filename;
      document.body.appendChild(a);
      a.click();
      a.remove();
      URL.revokeObjectURL(url);
    }

    $('#btnExport').addEventListener('click', () => {
      const books = getBooks();
      const members = getMembers();
      const loans = getLoans();

      let out = '';
      out += 'DATA BUKU\n';
      out += toCSVRow(['Kode','Judul','Penulis','Stok']) + '\n';
      books.forEach(b => out += toCSVRow([b.code, b.title, b.author, b.stock]) + '\n');

      out += '\nDATA PEMINJAM\n';
      out += toCSVRow(['ID','Nama','Kelas','Status']) + '\n';
      members.forEach(m => out += toCSVRow([m.memberId, m.name, m.className, m.status]) + '\n');

      out += '\nDATA TRANSAKSI\n';
      out += toCSVRow(['Tanggal','AnggotaID','Anggota','BukuKode','Buku','Status']) + '\n';
      loans.forEach(l => out += toCSVRow([l.date, l.memberId, l.memberName, l.bookCode, l.bookTitle, l.status]) + '\n');

      const stamp = new Date().toISOString().slice(0,10);
      downloadText(`export_perpustakaan_${stamp}.csv`, out, 'text/csv');
      toast('Export berhasil (CSV).');
    });

    // Init render
    bindSortHeaders();
    rerenderAll();
  </script>
</body>
</html>
