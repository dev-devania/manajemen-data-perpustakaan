/* SIDEBAR TOGGLE: desktop (collapse/expand. icon-only), mobile (open/close drawer + backdrop) */

const LS_KEY = "lib_mvp_v1";
const LS_SIDEBAR = "lib_sidebar_collapsed_v1";

const LIMIT_PER_MEMBER = 3;
const DEFAULT_DUE_DAYS = 7;

const $ = (sel, root = document) => root.querySelector(sel);
const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

function isMobile() {
  return window.matchMedia("(max-width: 980px)").matches;
}

function applySidebarPref() {
  const collapsed = localStorage.getItem(LS_SIDEBAR) === "1";
  document.body.classList.toggle("sidebar-collapsed", collapsed);
}

function setSidebarCollapsed(val) {
  document.body.classList.toggle("sidebar-collapsed", val);
  localStorage.setItem(LS_SIDEBAR, val ? "1" : "0");
}

function openDrawer() {
  const sidebar = $("#sidebar");
  const backdrop = $("#drawerBackdrop");
  if (!sidebar || !backdrop) return;
  sidebar.classList.add("is-open");
  backdrop.classList.add("is-open");
  backdrop.setAttribute("aria-hidden", "false");
}

function closeDrawer() {
  const sidebar = $("#sidebar");
  const backdrop = $("#drawerBackdrop");
  if (!sidebar || !backdrop) return;
  sidebar.classList.remove("is-open");
  backdrop.classList.remove("is-open");
  backdrop.setAttribute("aria-hidden", "true");
}

function toggleSidebar() {
  if (isMobile()) {
    const sidebar = $("#sidebar");
    if (!sidebar) return;
    if (sidebar.classList.contains("is-open")) closeDrawer();
    else openDrawer();
  } else {
    const now = document.body.classList.contains("sidebar-collapsed");
    setSidebarCollapsed(!now);
  }
}

function todayISO() {
  const d = new Date();
  const yyyy = d.getFullYear();
  const mm = String(d.getMonth() + 1).padStart(2, "0");
  const dd = String(d.getDate()).padStart(2, "0");
  return `${yyyy}-${mm}-${dd}`;
}

function addDaysISO(dateISO, days) {
  const d = new Date(dateISO + "T00:00:00");
  d.setDate(d.getDate() + days);
  const yyyy = d.getFullYear();
  const mm = String(d.getMonth() + 1).padStart(2, "0");
  const dd = String(d.getDate()).padStart(2, "0");
  return `${yyyy}-${mm}-${dd}`;
}

function uid(prefix) {
  const year = new Date().getFullYear();
  const rand = Math.floor(Math.random() * 9000) + 1000;
  return `${prefix}-${year}-${rand}`;
}

function toast(msg) {
  const el = $("#toast");
  if (!el) return alert(msg);
  el.textContent = msg;
  el.classList.add("is-show");
  clearTimeout(toast._t);
  toast._t = setTimeout(() => el.classList.remove("is-show"), 2400);
}

/* MEMBER CLASS OPTIONS (dropdown) */

function getClassOptions() {
  const grades = ["X", "XI", "XII"];
  const tracks = ["IPA", "IPS"];
  const out = [];
  for (const g of grades) {
    for (const t of tracks) {
      for (let i = 1; i <= 4; i++) out.push(`${g} ${t}-${i}`);
    }
  }
  return out;
}

function normalizeClassName(s) {
  const raw = String(s || "").trim();
  if (!raw) return "";

  const m1 = raw.match(/^(XII|XI|X)\s+(IPA|IPS)\s+(\d)\s*$/i);
  if (m1) return `${m1[1].toUpperCase()} ${m1[2].toUpperCase()}-${m1[3]}`;

  const m2 = raw.match(/^(XII|XI|X)\s+(IPA|IPS)\-(\d)\s*$/i);
  if (m2) return `${m2[1].toUpperCase()} ${m2[2].toUpperCase()}-${m2[3]}`;

  return raw;
}

function ensureClassDropdownOptions() {
  const sel = $("#memberClass");
  if (!sel) return;

  const hasRealOptions = sel.options && sel.options.length > 1;
  if (hasRealOptions) return;

  sel.innerHTML = "";
  const opts = getClassOptions();
  for (const v of opts) {
    const o = document.createElement("option");
    o.value = v;
    o.textContent = v;
    sel.appendChild(o);
  }
}

function classKey(className) {
  const m = normalizeClassName(className).match(/^(XII|XI|X)\s+(IPA|IPS)\-(\d)$/);
  if (!m) return [99, 99, 99];
  const gradeMap = { X: 10, XI: 11, XII: 12 };
  const trackMap = { IPA: 1, IPS: 2 };
  return [gradeMap[m[1]] ?? 99, trackMap[m[2]] ?? 99, Number(m[3] ?? 99)];
}

/* STATE */

function seed() {
  return {
    books: [
      { id: "B1", title: "Laskar Pelangi", author: "Andrea Hirata", category: "Novel", shelf: "A-1", stock: 3 },
      { id: "B2", title: "Fisika Dasar", author: "Halliday", category: "Sains", shelf: "C-2", stock: 2 },
      { id: "B3", title: "Sejarah Indonesia", author: "Sartono", category: "Sejarah", shelf: "B-3", stock: 1 },
    ],
    members: [
      { id: "M1", name: "Alya Putri", nis: "10101", className: "X IPA-1", contact: "08xxxx" },
      { id: "M2", name: "Bima Saputra", nis: "10102", className: "XI IPS-2", contact: "08xxxx" },
    ],
    tx: [
      { code: "TX-2025-1001", memberId: "M1", bookId: "B1", loanDate: todayISO(), dueDate: addDaysISO(todayISO(), 7), status: "DIPINJAM" }
    ]
  };
}

let state = loadState();

function loadState() {
  const raw = localStorage.getItem(LS_KEY);
  if (!raw) return seed();
  try { return JSON.parse(raw); } catch { return seed(); }
}

function saveState() {
  localStorage.setItem(LS_KEY, JSON.stringify(state));
}

/* NAV + PAGES */

function setPage(pageName) {
  $$(".nav__item").forEach(btn => btn.classList.toggle("is-active", btn.dataset.page === pageName));
  $$(".page").forEach(sec => sec.classList.remove("is-active"));
  const pageEl = $(`#page-${pageName}`);
  if (pageEl) pageEl.classList.add("is-active");

  if (isMobile()) closeDrawer();
}

function bindNav() {
  $$(".nav__item").forEach(btn => {
    btn.addEventListener("click", () => setPage(btn.dataset.page));
  });

  const btnMenu = $("#btnMenu");
  if (btnMenu) btnMenu.addEventListener("click", toggleSidebar);

  const backdrop = $("#drawerBackdrop");
  if (backdrop) backdrop.addEventListener("click", closeDrawer);

  window.addEventListener("resize", () => {
    if (!isMobile()) closeDrawer();
  });
}

/* MODAL */

function openModal(id) {
  const m = $("#" + id);
  if (!m) return;
  m.classList.add("is-open");
  m.setAttribute("aria-hidden", "false");
}

function closeModal(id) {
  const m = $("#" + id);
  if (!m) return;
  m.classList.remove("is-open");
  m.setAttribute("aria-hidden", "true");
}

function bindModalClose() {
  $$("[data-close]").forEach(el => {
    el.addEventListener("click", () => closeModal(el.dataset.close));
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      ["modalBook", "modalMember", "modalLoan"].forEach(closeModal);
      if (isMobile()) closeDrawer();
    }
  });
}

/* RENDER HELPERS */

function findBook(id) { return state.books.find(b => b.id === id); }
function findMember(id) { return state.members.find(m => m.id === id); }

function badgeStatus(status) {
  if (status === "DIPINJAM") return `<span class="badge badge--muted">⏳ DIPINJAM</span>`;
  return `<span class="badge badge--ok">✅ KEMBALI</span>`;
}

/* SORT ICON + HIGHLIGHT (HEADER)*/
function updateSortIcons(group) {
  const ss = window.sortState || window.SORT_STATE || null;
  if (!ss || !ss[group]) return;

  const { key, dir } = ss[group];

  document.querySelectorAll(`[data-sort-group="${group}"][data-sort-key]`).forEach((btn) => {
    const isActive = btn.dataset.sortKey === key;
    btn.classList.toggle("is-sorted", isActive);

    const ico = btn.querySelector(".sort-ico");
    if (!ico) return;

    if (!isActive) ico.textContent = "↕";
    else ico.textContent = (dir === "asc") ? "▲" : "▼";
  });
}


function renderStats() {
  $("#statTotalBooks").textContent = state.books.length;
  $("#statTotalMembers").textContent = state.members.length;
  $("#statActiveLoans").textContent = state.tx.filter(t => t.status === "DIPINJAM").length;

  const total = state.tx.length;
  const returned = state.tx.filter(t => t.status === "KEMBALI").length;
  const borrowed = total - returned;

  $("#statTxTotal").textContent = total;
  $("#statTxReturned").textContent = returned;
  $("#statTxBorrowed").textContent = borrowed;
}

function renderRecentTx() {
  const tbody = $("#tableRecentTx tbody");
  if (!tbody) return;
  const items = [...state.tx].slice(-8).reverse();
  tbody.innerHTML = items.map(t => {
    const m = findMember(t.memberId);
    const b = findBook(t.bookId);
    return `
      <tr>
        <td>${t.code}</td>
        <td>${m ? escapeHtml(m.name) : "-"}</td>
        <td>${b ? escapeHtml(b.title) : "-"}</td>
        <td>${t.loanDate}</td>
        <td>${t.dueDate}</td>
        <td>${badgeStatus(t.status)}</td>
      </tr>
    `;
  }).join("");
}

function renderBooks() {
  const q = ($("#qBooks")?.value || "").toLowerCase();
  const tbody = $("#tableBooks tbody");
  if (!tbody) return;

  const rows = state.books
    .filter(b => [b.title, b.author, b.category, b.shelf].join(" ").toLowerCase().includes(q))
    .map(b => `
      <tr>
        <td><b>${escapeHtml(b.title)}</b></td>
        <td>${escapeHtml(b.author)}</td>
        <td>${escapeHtml(b.category || "-")}</td>
        <td>${escapeHtml(b.shelf || "-")}</td>
        <td class="right">${b.stock}</td>
        <td>
          <div class="actions">
            <button class="link-btn" data-edit-book="${b.id}">Edit</button>
            <button class="link-btn" data-del-book="${b.id}">Hapus</button>
          </div>
        </td>
      </tr>
    `);

  tbody.innerHTML = rows.join("") || `<tr><td colspan="6" class="muted">Tidak ada data.</td></tr>`;
}

function renderMembers() {
  const q = ($("#qMembers")?.value || "").toLowerCase();
  const tbody = $("#tableMembers tbody");
  if (!tbody) return;

  const rows = state.members
    .slice()
    .sort((a, b) => {
      const ka = classKey(a.className);
      const kb = classKey(b.className);
      for (let i = 0; i < 3; i++) if (ka[i] !== kb[i]) return ka[i] - kb[i];
      return (a.name || "").localeCompare(b.name || "");
    })
    .filter(m => [m.id, m.name, m.nis, m.className, m.contact].join(" ").toLowerCase().includes(q))
    .map(m => `
      <tr>
        <td><b>${escapeHtml(m.name)}</b></td>
        <td>${escapeHtml(m.id || "-")}</td>
        <td>${escapeHtml(normalizeClassName(m.className) || "-")}</td>
        <td>${escapeHtml(m.contact || "-")}</td>
        <td>
          <div class="actions">
            <button class="link-btn" data-edit-member="${m.id}">Edit</button>
            <button class="link-btn" data-del-member="${m.id}">Hapus</button>
          </div>
        </td>
      </tr>
    `);

  tbody.innerHTML = rows.join("") || `<tr><td colspan="5" class="muted">Tidak ada data.</td></tr>`;
}

function renderTx() {
  const q = ($("#qTx")?.value || "").toLowerCase();
  const statusFilter = $("#filterTxStatus")?.value || "";

  const tbody = $("#tableTx tbody");
  if (!tbody) return;

  const rows = [...state.tx].reverse()
    .filter(t => {
      const m = findMember(t.memberId);
      const b = findBook(t.bookId);
      const hay = [t.code, m?.name, m?.id, b?.title].join(" ").toLowerCase();
      const okQ = hay.includes(q);
      const okS = statusFilter ? t.status === statusFilter : true;
      return okQ && okS;
    })
    .map(t => {
      const m = findMember(t.memberId);
      const b = findBook(t.bookId);
      const canReturn = t.status === "DIPINJAM";
      return `
        <tr>
          <td>${t.code}</td>
          <td>${m ? escapeHtml(m.name) : "-"}</td>
          <td>${b ? escapeHtml(b.title) : "-"}</td>
          <td>${t.loanDate}</td>
          <td>${t.dueDate}</td>
          <td>${badgeStatus(t.status)}</td>
          <td>
            <div class="actions">
              ${canReturn ? `<button class="link-btn" data-return="${t.code}">Kembalikan</button>` : `<button class="link-btn" disabled>—</button>`}
              <button class="link-btn" data-del-tx="${t.code}">Hapus</button>
            </div>
          </td>
        </tr>
      `;
    });

  tbody.innerHTML = rows.join("") || `<tr><td colspan="7" class="muted">Belum ada transaksi.</td></tr>`;
}

function renderTopBooks() {
  const map = new Map();
  state.tx.forEach(t => map.set(t.bookId, (map.get(t.bookId) || 0) + 1));

  const items = [...map.entries()]
    .map(([bookId, count]) => ({ bookId, count, book: findBook(bookId) }))
    .filter(x => x.book)
    .sort((a, b) => b.count - a.count)
    .slice(0, 10);

  const tbody = $("#tableTopBooks tbody");
  if (!tbody) return;

  tbody.innerHTML = items.map(x => `
    <tr>
      <td><b>${escapeHtml(x.book.title)}</b></td>
      <td>${escapeHtml(x.book.author)}</td>
      <td class="right">${x.count}</td>
    </tr>
  `).join("") || `<tr><td colspan="3" class="muted">Belum cukup data.</td></tr>`;
}

/* FORMS: BOOK */

function openBookForm(book = null) {
  $("#modalBookTitle").textContent = book ? "Edit Buku" : "Tambah Buku";
  $("#bookId").value = book?.id || "";
  $("#bookTitle").value = book?.title || "";
  $("#bookAuthor").value = book?.author || "";
  $("#bookCategory").value = book?.category || "";
  $("#bookShelf").value = book?.shelf || "";
  $("#bookStock").value = book?.stock ?? 1;
  openModal("modalBook");
}

function bindBook() {
  $("#btnAddBook")?.addEventListener("click", () => openBookForm(null));
  $("#qBooks")?.addEventListener("input", renderBooks);

  $("#formBook")?.addEventListener("submit", (e) => {
    e.preventDefault();
    const id = $("#bookId").value.trim();
    const title = $("#bookTitle").value.trim();
    const author = $("#bookAuthor").value.trim();
    const category = $("#bookCategory").value.trim();
    const shelf = $("#bookShelf").value.trim();
    const stock = Number($("#bookStock").value);

    if (!title || !author || Number.isNaN(stock) || stock < 0) {
      toast("Mohon isi data buku dengan benar.");
      return;
    }

    if (id) {
      const b = findBook(id);
      if (!b) return;
      b.title = title; b.author = author; b.category = category; b.shelf = shelf; b.stock = stock;
      toast("Buku berhasil diupdate.");
    } else {
      const newId = "B" + (state.books.length + 1) + "-" + Math.floor(Math.random() * 1000);
      state.books.push({ id: newId, title, author, category, shelf, stock });
      toast("Buku berhasil ditambahkan.");
    }

    saveState();
    closeModal("modalBook");
    refreshAll();
  });

  $("#tableBooks")?.addEventListener("click", (e) => {
    const btnEdit = e.target.closest("[data-edit-book]");
    const btnDel = e.target.closest("[data-del-book]");

    if (btnEdit) {
      const b = findBook(btnEdit.dataset.editBook);
      if (b) openBookForm(b);
    }

    if (btnDel) {
      const id = btnDel.dataset.delBook;
      const hasActive = state.tx.some(t => t.bookId === id && t.status === "DIPINJAM");
      if (hasActive) { toast("Tidak bisa hapus: buku masih dipinjam."); return; }
      if (confirm("Hapus buku ini?")) {
        state.books = state.books.filter(b => b.id !== id);
        saveState();
        toast("Buku dihapus.");
        refreshAll();
      }
    }
  });
}

/* FORMS MEMBER */

function openMemberForm(member = null) {
  ensureClassDropdownOptions();

  $("#modalMemberTitle").textContent = member ? "Edit Anggota" : "Tambah Anggota";

  const form = $("#formMember");
  if (form) form.dataset.originalMemberId = member?.id || "";

  const idEl = $("#memberId");
  const nameEl = $("#memberName");
  const classEl = $("#memberClass");
  const nisEl = $("#memberNis");
  const contactEl = $("#memberContact");

  if (idEl) idEl.value = member?.id || "";
  if (nameEl) nameEl.value = member?.name || "";

  const cls = normalizeClassName(member?.className || "");
  if (classEl) {
    const options = Array.from(classEl.options || []);
    const exists = options.some(o => o.value === cls);
    if (cls && !exists) {
      const opt = document.createElement("option");
      opt.value = cls;
      opt.textContent = cls + " (lama)";
      classEl.insertBefore(opt, classEl.firstChild);
    }
    classEl.value = cls || (classEl.options[0]?.value || "");
  }

  if (nisEl) nisEl.value = member?.nis || "";
  if (contactEl) contactEl.value = member?.contact || "";

  openModal("modalMember");
}

function bindMember() {
  $("#btnAddMember")?.addEventListener("click", () => openMemberForm(null));
  $("#qMembers")?.addEventListener("input", renderMembers);

  $("#formMember")?.addEventListener("submit", (e) => {
    e.preventDefault();

    ensureClassDropdownOptions();

    const form = $("#formMember");
    const originalId = (form?.dataset.originalMemberId || "").trim();

    const id = ($("#memberId")?.value || "").trim();
    const name = ($("#memberName")?.value || "").trim();
    const className = normalizeClassName(($("#memberClass")?.value || "").trim());

    const nis = $("#memberNis") ? ($("#memberNis").value || "").trim() : "";
    const contact = $("#memberContact") ? ($("#memberContact").value || "").trim() : "";

    if (!id || !name || !className) {
      toast("ID, Nama, dan Kelas wajib diisi.");
      return;
    }

    const idUsedByOther = state.members.some(m => m.id === id && m.id !== originalId);
    if (idUsedByOther) {
      toast("ID anggota sudah dipakai. Pakai ID lain.");
      return;
    }

    if ($("#memberNis") && nis) {
      const nisUsedByOther = state.members.some(m => m.nis === nis && m.id !== originalId);
      if (nisUsedByOther) {
        toast("NIS sudah dipakai anggota lain.");
        return;
      }
    }

    // EDIT
    if (originalId) {
      const m = findMember(originalId);
      if (!m) { toast("Data anggota tidak ditemukan."); return; }

      if (id !== originalId) {
        state.tx.forEach(t => {
          if (t.memberId === originalId) t.memberId = id;
        });
        m.id = id;
      }

      m.name = name;
      m.className = className;
      if ($("#memberNis")) m.nis = nis;
      if ($("#memberContact")) m.contact = contact;

      toast("Anggota berhasil diupdate.");
    } else {
      // ADD
      state.members.push({
        id,
        name,
        className,
        ...( $("#memberNis") ? { nis } : {} ),
        ...( $("#memberContact") ? { contact } : {} ),
      });
      toast("Anggota berhasil ditambahkan.");
    }

    saveState();
    closeModal("modalMember");
    refreshAll();
  });

  $("#tableMembers")?.addEventListener("click", (e) => {
    const btnEdit = e.target.closest("[data-edit-member]");
    const btnDel = e.target.closest("[data-del-member]");

    if (btnEdit) {
      const m = findMember(btnEdit.dataset.editMember);
      if (m) openMemberForm(m);
    }

    if (btnDel) {
      const id = btnDel.dataset.delMember;
      const hasActive = state.tx.some(t => t.memberId === id && t.status === "DIPINJAM");
      if (hasActive) { toast("Tidak bisa hapus: anggota masih punya pinjaman aktif."); return; }
      if (confirm("Hapus anggota ini?")) {
        state.members = state.members.filter(m => m.id !== id);
        saveState();
        toast("Anggota dihapus.");
        refreshAll();
      }
    }
  });
}

/* LOANS/RETURNS */

let loanMode = "borrow"; 

function getLoanEls() {
  const root = $("#modalLoan") || document;

  const tabBorrow =
    $("[data-loan-tab='borrow']", root) ||
    $("[data-tab='borrow']", root) ||
    $("#loanTabBorrow", root);

  const tabReturn =
    $("[data-loan-tab='return']", root) ||
    $("[data-tab='return']", root) ||
    $("#loanTabReturn", root);

  const paneBorrow =
    $("[data-loan-pane='borrow']", root) ||
    $("[data-pane='borrow']", root) ||
    $("#loanPaneBorrow", root);

  const paneReturn =
    $("[data-loan-pane='return']", root) ||
    $("[data-pane='return']", root) ||
    $("#loanPaneReturn", root);

  const selMember = $("#loanMember");
  const selBook = $("#loanBook");
  const loanDate = $("#loanDate");
  const loanDue = $("#loanDue");

  const returnSelect =
    $("#returnTxSelect") ||
    $("#activeTxSelect") ||
    $("#returnTx") ||
    $("[name='activeTx']") ||
    $("[name='returnTx']") ||
    (paneReturn ? $("select", paneReturn) : null);

  return { tabBorrow, tabReturn, paneBorrow, paneReturn, selMember, selBook, loanDate, loanDue, returnSelect };
}

function setLoanMode(mode) {
  loanMode = mode;
  const { tabBorrow, tabReturn, paneBorrow, paneReturn, selMember, selBook, loanDate, loanDue, returnSelect } = getLoanEls();

  if (tabBorrow) tabBorrow.classList.toggle("is-active", mode === "borrow");
  if (tabReturn) tabReturn.classList.toggle("is-active", mode === "return");

  if (paneBorrow) paneBorrow.classList.toggle("is-visible", mode === "borrow");
  if (paneReturn) paneReturn.classList.toggle("is-visible", mode === "return");

  const borrowOn = mode === "borrow";
  if (selMember) selMember.disabled = !borrowOn;
  if (selBook) selBook.disabled = !borrowOn;
  if (loanDate) loanDate.disabled = !borrowOn;
  if (loanDue) loanDue.disabled = !borrowOn;

  if (returnSelect) returnSelect.disabled = borrowOn;
}

function fillLoanSelects() {
  const selMember = $("#loanMember");
  const selBook = $("#loanBook");

  if (selMember) {
    selMember.innerHTML = state.members
      .map(m => {
        const extra = m.nis ? `NIS: ${escapeHtml(m.nis)}` : `ID: ${escapeHtml(m.id)}`;
        return `<option value="${m.id}">${escapeHtml(m.name)} (${extra})</option>`;
      })
      .join("");
  }

  if (selBook) {
    selBook.innerHTML = state.books
      .map(b => `<option value="${b.id}" ${b.stock <= 0 ? "disabled" : ""}>${escapeHtml(b.title)} — stok: ${b.stock}</option>`)
      .join("");

    const firstOk = state.books.find(b => b.stock > 0);
    if (firstOk) selBook.value = firstOk.id;
  }

  const loanDate = $("#loanDate");
  const due = $("#loanDue");

  if (loanDate && due) {
    loanDate.value = todayISO();
    due.value = addDaysISO(loanDate.value, DEFAULT_DUE_DAYS);
    loanDate.onchange = () => { due.value = addDaysISO(loanDate.value, DEFAULT_DUE_DAYS); };
  }

  const hint = $("#loanRulesHint");
  if (hint) hint.textContent = `Aturan: max ${LIMIT_PER_MEMBER} buku/anggota • jatuh tempo default ${DEFAULT_DUE_DAYS} hari`;
}

function fillActiveTxSelect() {
  const { returnSelect } = getLoanEls();
  if (!returnSelect) return;

  const active = state.tx.filter(t => t.status === "DIPINJAM");
  if (active.length === 0) {
    returnSelect.innerHTML = `<option value="" selected>(Tidak ada transaksi aktif)</option>`;
    returnSelect.disabled = (loanMode === "borrow") ? true : false;
    return;
  }

  returnSelect.innerHTML = active.map(t => {
    const m = findMember(t.memberId);
    const b = findBook(t.bookId);
    const label = `${t.code} — ${m ? m.name : "-"} • ${b ? b.title : "-"} (${t.loanDate})`;
    return `<option value="${t.code}">${escapeHtml(label)}</option>`;
  }).join("");

  returnSelect.value = active[0].code;
}

function openLoanForm() {
  if (state.members.length === 0 || state.books.length === 0) {
    toast("Pastikan ada data anggota dan buku dulu.");
    return;
  }
  fillLoanSelects();
  fillActiveTxSelect();
  setLoanMode("borrow");
  openModal("modalLoan");
}

function activeLoansCountByMember(memberId) {
  return state.tx.filter(t => t.memberId === memberId && t.status === "DIPINJAM").length;
}

function bindLoanTabs() {
  const { tabBorrow, tabReturn } = getLoanEls();
  if (tabBorrow) tabBorrow.addEventListener("click", (e) => { e.preventDefault(); fillActiveTxSelect(); setLoanMode("borrow"); });
  if (tabReturn) tabReturn.addEventListener("click", (e) => { e.preventDefault(); fillActiveTxSelect(); setLoanMode("return"); });
}

function bindLoans() {
  $("#btnNewLoan")?.addEventListener("click", openLoanForm);
  $("#btnQuickLoan")?.addEventListener("click", () => { setPage("loans"); openLoanForm(); });

  $("#qTx")?.addEventListener("input", renderTx);
  $("#filterTxStatus")?.addEventListener("change", renderTx);
  
  bindLoanTabs();
  $("#qLoans")?.addEventListener("input", renderTx); // atau renderLoans sesuai nama fungsi render transaksi kamu

  const form = $("#formLoan");
  if (form) {
    form.setAttribute("novalidate", "novalidate");

    form.addEventListener("submit", (e) => {
      e.preventDefault();

      if (loanMode === "borrow") {
        const memberId = $("#loanMember")?.value;
        const bookId = $("#loanBook")?.value;
        const loanDate = $("#loanDate")?.value;
        const dueDate = $("#loanDue")?.value;

        if (!memberId || !bookId || !loanDate || !dueDate) {
          toast("Lengkapi data pinjam (anggota, buku, tanggal).");
          return;
        }

        const book = findBook(bookId);
        if (!book) return;

        if (book.stock <= 0) { toast("Stok buku habis."); return; }

        const activeCount = activeLoansCountByMember(memberId);
        if (activeCount >= LIMIT_PER_MEMBER) {
          toast(`Anggota sudah mencapai limit (${LIMIT_PER_MEMBER}) pinjaman aktif.`);
          return;
        }

        const code = uid("TX");
        state.tx.push({ code, memberId, bookId, loanDate, dueDate, status: "DIPINJAM" });
        book.stock -= 1;

        saveState();
        closeModal("modalLoan");
        toast(`Transaksi dibuat: ${code}`);
        refreshAll();
        return;
      }

      const { returnSelect } = getLoanEls();
      const code = returnSelect ? String(returnSelect.value || "").trim() : "";

      if (!code) {
        toast("Tidak ada transaksi aktif untuk dikembalikan.");
        return;
      }

      doReturn(code);
      closeModal("modalLoan");
    });
  }

  $("#btnReturn")?.addEventListener("click", () => {
    const code = $("#returnCode")?.value.trim();
    if (!code) { toast("Masukkan kode transaksi."); return; }
    doReturn(code);
  });

  $("#tableTx")?.addEventListener("click", (e) => {
    const btnReturn = e.target.closest("[data-return]");
    const btnDel = e.target.closest("[data-del-tx]");

    if (btnReturn) doReturn(btnReturn.dataset.return);

    if (btnDel) {
      const code = btnDel.dataset.delTx;
      const t = state.tx.find(x => x.code === code);
      if (!t) return;

      if (confirm("Hapus transaksi ini?")) {
        if (t.status === "DIPINJAM") {
          const b = findBook(t.bookId);
          if (b) b.stock += 1;
        }
        state.tx = state.tx.filter(x => x.code !== code);
        saveState();
        toast("Transaksi dihapus.");
        refreshAll();
      }
    }
  });
}

function doReturn(code) {
  const t = state.tx.find(x => x.code === code);
  const hint = $("#returnHint");

  if (!t) { if (hint) hint.textContent = "Kode tidak ditemukan."; toast("Kode transaksi tidak ditemukan."); return; }
  if (t.status === "KEMBALI") { if (hint) hint.textContent = "Transaksi ini sudah kembali."; toast("Transaksi sudah kembali."); return; }

  t.status = "KEMBALI";
  const b = findBook(t.bookId);
  if (b) b.stock += 1;

  saveState();
  if ($("#returnCode")) $("#returnCode").value = "";
  if (hint) hint.textContent = `Berhasil mengembalikan ${b ? b.title : "buku"} (kode ${code}).`;
  toast("Pengembalian berhasil.");

  fillActiveTxSelect();
  refreshAll();
}

/* EXPORT CSV */

function bindExport() {
  $("#btnExportCsv")?.addEventListener("click", () => {
    const header = ["code","member","member_id","book","loanDate","dueDate","status"];
    const rows = state.tx.map(t => {
      const m = findMember(t.memberId);
      const b = findBook(t.bookId);
      return [t.code, m?.name || "", m?.id || "", b?.title || "", t.loanDate, t.dueDate, t.status];
    });

    const csv = [header, ...rows]
      .map(r => r.map(cell => `"${String(cell).replaceAll(`"`, `""`)}"`).join(","))
      .join("\n");

    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "laporan_transaksi.csv";
    a.click();
    URL.revokeObjectURL(url);
    toast("CSV berhasil diunduh.");
  });
}

/* RESET DEMO */

function bindReset() {
  const btn = $("#btnDemoReset");
  if (!btn) return;
  btn.addEventListener("click", () => {
    if (!confirm("Reset data demo ke default?")) return;
    state = seed();
    saveState();
    toast("Data demo direset.");
    refreshAll();
    setPage("dashboard");
  });
}

/* UTILS */
function norm(s) {
  return String(s ?? "").toLowerCase().trim();
}

function escapeHtml(s) {
  return String(s ?? "")
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

/* REFRESH ALL */

function refreshAll() {
  renderStats();
  renderRecentTx();
  renderBooks();
  renderMembers();
  renderTx();
  renderTopBooks();
}

/* INIT */

function init() {
  applySidebarPref();
  bindNav();
  bindModalClose();
  bindBook();
  bindMember();
  bindLoans();
  bindExport();
  bindReset();

  state.members.forEach(m => { m.className = normalizeClassName(m.className); });
  saveState();

  refreshAll();
}

init();
