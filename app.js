const API = "api";

let state = {
  page: 1,
  pageSize: 10,
  genre: "all",
  totalPages: 1,
  total: 0,
  sortBy: "id",
  sortDir: "desc",
};

function setCookie(name, value, days = 365) {
  const maxAge = days * 24 * 60 * 60;
  document.cookie = `${encodeURIComponent(name)}=${encodeURIComponent(value)}; Path=/; Max-Age=${maxAge}; SameSite=Lax`;
}

function getCookie(name) {
  const cookies = document.cookie.split(";").map(s => s.trim());
  for (const c of cookies) {
    if (!c) continue;
    const [k, ...rest] = c.split("=");
    if (decodeURIComponent(k) === name) return decodeURIComponent(rest.join("=") || "");
  }
  return null;
}

function fmtDate(d) {
  if (!d) return "";
  return d; // already YYYY-MM-DD
}

async function fetchJSON(url, options) {
  const res = await fetch(url, options);
  const data = await res.json().catch(() => ({}));
  if (!res.ok) {
    throw new Error(data.error || `Request failed (${res.status})`);
  }
  return data;
}

/* Tabs */
const tabList = document.getElementById("tabList");
const tabStats = document.getElementById("tabStats");
const listView = document.getElementById("listView");
const statsView = document.getElementById("statsView");
const sortBySel = document.getElementById("sortBy");
const sortDirSel = document.getElementById("sortDir");
const statPageSize = document.getElementById("statPageSize");

function showList() {
  tabList.classList.add("active");
  tabStats.classList.remove("active");
  listView.classList.remove("hidden");
  statsView.classList.add("hidden");
}
function showStats() {
  tabStats.classList.add("active");
  tabList.classList.remove("active");
  statsView.classList.remove("hidden");
  listView.classList.add("hidden");
  loadStats().catch(err => alert(err.message));
}

tabList.addEventListener("click", () => showList());
tabStats.addEventListener("click", () => showStats());

/* Controls */
const genreFilter = document.getElementById("genreFilter");
const pageSizeSel = document.getElementById("pageSize");
const btnRefresh = document.getElementById("btnRefresh");
const tbody = document.getElementById("tbody");
const meta = document.getElementById("meta");
const prevBtn = document.getElementById("prev");
const nextBtn = document.getElementById("next");
const pageIndicator = document.getElementById("pageIndicator");

/* Create form */
const createForm = document.getElementById("createForm");
const editIdInput = document.getElementById("editId");
const submitBtn = document.getElementById("submitBtn");
const cancelEditBtn = document.getElementById("cancelEditBtn");
const formModeHint = document.getElementById("formModeHint");

function setFormModeEditing(id) {
  editIdInput.value = String(id);
  submitBtn.textContent = "Save Changes";
  cancelEditBtn.style.display = "inline-block";
  formModeHint.textContent = `Mode: Editing (ID ${id})`;
}

function setFormModeAdding() {
  editIdInput.value = "";
  submitBtn.textContent = "Add";
  cancelEditBtn.style.display = "none";
  formModeHint.textContent = "Mode: Adding";
  createForm.reset();
}

cancelEditBtn.addEventListener("click", () => {
  setFormModeAdding();
});

function applyInitialPageSizeFromCookie() {
  const saved = getCookie("pageSize");
  if (saved && !Number.isNaN(parseInt(saved, 10))) {
    state.pageSize = Math.max(1, Math.min(200, parseInt(saved, 10)));
    pageSizeSel.value = String(state.pageSize);
  } else {
    state.pageSize = parseInt(pageSizeSel.value, 10);
  }
}

async function loadGenres() {
  const data = await fetchJSON(`${API}/genres.php`);
  // reset options except "All"
  const current = genreFilter.value || "all";
  genreFilter.innerHTML = `<option value="all">All</option>`;
  for (const g of data.genres) {
    const opt = document.createElement("option");
    opt.value = g;
    opt.textContent = g;
    genreFilter.appendChild(opt);
  }
  // restore selection if possible
  if ([...genreFilter.options].some(o => o.value === current)) {
    genreFilter.value = current;
  }
}

async function loadList() {
  const params = new URLSearchParams({
  page: String(state.page),
  pageSize: String(state.pageSize),
  genre: state.genre,
  sortBy: state.sortBy,
  sortDir: state.sortDir
});

  const data = await fetchJSON(`${API}/list.php?${params.toString()}`);

  state.total = data.meta.total;
  state.totalPages = data.meta.totalPages;

  tbody.innerHTML = "";
  for (const item of data.items) {
    const tr = document.createElement("tr");

    tr.innerHTML = `
        <td>${item.id}</td>
        <td>
        <img class="cover"
        src="${escapeHtml(item.image_url)}"
        alt="cover"
        onerror="this.onerror=null; this.src='img/placeholder.png';"
        />
        </td>
        <td>${escapeHtml(item.name)}</td>
        <td>${escapeHtml(fmtDate(item.pub_date))}</td>
        <td>${escapeHtml(item.genre)}</td>
        <td>${escapeHtml(item.author)}</td>
        <td class="actions">
        <button class="btn" data-action="edit" data-id="${item.id}" data-image="${escapeHtml(item.image_url)}">Edit</button>
        <button class="btn danger" data-action="del" data-id="${item.id}">Delete</button>
    </td>
    `;
    tbody.appendChild(tr);
  }

  meta.textContent = `Total: ${state.total} • Filter: ${state.genre || "all"} • Page size: ${state.pageSize}`;
  pageIndicator.textContent = `Page ${state.page} / ${Math.max(1, state.totalPages)}`;

  prevBtn.disabled = (state.page <= 1);
  nextBtn.disabled = (state.page >= state.totalPages || state.totalPages === 0);
}

function escapeHtml(s) {
  return String(s ?? "")
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

prevBtn.addEventListener("click", async () => {
  if (state.page > 1) {
    state.page--;
    await loadList();
  }
});
nextBtn.addEventListener("click", async () => {
  if (state.page < state.totalPages) {
    state.page++;
    await loadList();
  }
});

btnRefresh.addEventListener("click", async () => {
  await loadGenres();
  await loadList();
});

genreFilter.addEventListener("change", async () => {
  state.genre = genreFilter.value;
  state.page = 1;
  await loadList();
});

pageSizeSel.addEventListener("change", async () => {
  state.pageSize = parseInt(pageSizeSel.value, 10);
  setCookie("pageSize", String(state.pageSize));
  state.page = 1;
  await loadList();
});

/* Edit/Delete (event delegation) */
tbody.addEventListener("click", async (e) => {
  const btn = e.target.closest("button");
  if (!btn) return;

  const action = btn.dataset.action;
  const id = parseInt(btn.dataset.id, 10);

  if (action === "del") {
    const ok = confirm(`Delete book #${id}? This cannot be undone.`);
    if (!ok) return;

    try {
      await fetchJSON(`${API}/delete.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id })
      });

      if (state.page > 1 && (state.total - 1) <= (state.page - 1) * state.pageSize) {
        state.page--;
      }

      // If you were editing this item, cancel edit mode
      if (parseInt(editIdInput.value || "0", 10) === id) {
        setFormModeAdding();
      }

      await loadGenres();
      await loadList();
    } catch (err) {
      alert(err.message);
    }
  }

  if (action === "edit") {
    // Fill the form with the row’s values
    const tr = btn.closest("tr");
    const cells = tr.querySelectorAll("td");

    // cells mapping (based on your table):
    // 0=id, 1=cover img, 2=name, 3=date, 4=genre, 5=author, 6=actions
    const name = cells[2].textContent.trim();
    const pub_date = cells[3].textContent.trim();
    const genre = cells[4].textContent.trim();
    const author = cells[5].textContent.trim();

    const img = cells[1].querySelector("img");
    const image_url = (img && img.getAttribute("src")) ? img.getAttribute("src") : "";

    document.getElementById("name").value = name;
    document.getElementById("date").value = pub_date;
    document.getElementById("genre").value = genre;
    document.getElementById("author").value = author;
    document.getElementById("image_url").value = image_url;

    setFormModeEditing(id);
    window.scrollTo({ top: 0, behavior: "smooth" });
  }
});

sortBySel.addEventListener("change", async () => {
  state.sortBy = sortBySel.value;
  state.page = 1;
  await loadList();
});

sortDirSel.addEventListener("change", async () => {
  state.sortDir = sortDirSel.value;
  state.page = 1;
  await loadList();
});

createForm.addEventListener("submit", async (e) => {
  e.preventDefault();

  const payload = {
    name: document.getElementById("name").value.trim(),
    pub_date: document.getElementById("date").value.trim(),
    genre: document.getElementById("genre").value.trim(),
    author: document.getElementById("author").value.trim(),
    image_url: document.getElementById("image_url").value.trim(),
  };

  const editingId = parseInt(editIdInput.value || "0", 10);

  try {
    if (editingId > 0) {
      // UPDATE
      await fetchJSON(`${API}/update.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: editingId, ...payload })
      });
      setFormModeAdding();
    } else {
      // CREATE
      await fetchJSON(`${API}/create.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });
      createForm.reset();
    }

    await loadGenres();
    state.page = 1;
    await loadList();
  } catch (err) {
    alert(err.message);
  }
});

/* Stats */
const statTotal = document.getElementById("statTotal");
const statGenre = document.getElementById("statGenre");
const statGenreCount = document.getElementById("statGenreCount");
const btnReloadStats = document.getElementById("btnReloadStats");

async function loadStats() {
  const data = await fetchJSON(`${API}/stats.php`);

  statTotal.textContent = String(data.total);
  statGenre.textContent = data.mostPopularGenre ?? "—";
  statGenreCount.textContent = data.mostPopularGenre
    ? `${data.mostPopularGenreCount} book(s)`
    : "—";

  // NEW: show current page size from frontend state
  statPageSize.textContent = String(state.pageSize);
}

btnReloadStats.addEventListener("click", () => {
  loadStats().catch(err => alert(err.message));
});

/* Init */
(async function init() {
  applyInitialPageSizeFromCookie();

  // defaults
  sortBySel.value = state.sortBy;
  sortDirSel.value = state.sortDir;

  await loadGenres();
  state.genre = genreFilter.value || "all";
  await loadList();
})();