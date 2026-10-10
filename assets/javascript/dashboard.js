// assets/javascript/dashboard.js
//
// Handles: polling api/get_data.php on a timer, updating the DOM
// with the results, the theme toggle, the sound toggle, the
// "new coin" ping + pulse animation, and savings goals CRUD.

const POLL_INTERVAL_MS = 2000;

let lastTotalCoins = null;
let soundOn = localStorage.getItem("alkansya-sound") !== "off";
let audioCtx = null;

// ---------------------------------------------------------
// THEME TOGGLE
// ---------------------------------------------------------
function initTheme() {
  const root = document.documentElement;
  const savedTheme = localStorage.getItem("alkansya-theme") || "dark";
  root.setAttribute("data-theme", savedTheme);

  document.getElementById("theme-toggle").addEventListener("click", () => {
    const nextTheme = root.getAttribute("data-theme") === "dark" ? "light" : "dark";
    root.setAttribute("data-theme", nextTheme);
    localStorage.setItem("alkansya-theme", nextTheme);
  });
}

// ---------------------------------------------------------
// SOUND TOGGLE
// ---------------------------------------------------------
function initSoundToggle() {
  const soundBtn = document.getElementById("sound-toggle");
  updateSoundButtonLabel(soundBtn);

  soundBtn.addEventListener("click", () => {
    soundOn = !soundOn;
    localStorage.setItem("alkansya-sound", soundOn ? "on" : "off");
    updateSoundButtonLabel(soundBtn);
  });
}

function updateSoundButtonLabel(soundBtn) {
  soundBtn.textContent = soundOn ? "🔊 Sound" : "🔇 Muted";
  soundBtn.classList.toggle("active-mute", !soundOn);
}

function playPing() {
  if (!soundOn) return;

  try {
    if (!audioCtx) {
      audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    }

    const oscillator = audioCtx.createOscillator();
    const gain = audioCtx.createGain();

    oscillator.type = "sine";
    oscillator.frequency.setValueAtTime(880, audioCtx.currentTime);
    oscillator.frequency.exponentialRampToValueAtTime(1320, audioCtx.currentTime + 0.12);

    gain.gain.setValueAtTime(0.15, audioCtx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.3);

    oscillator.connect(gain).connect(audioCtx.destination);
    oscillator.start();
    oscillator.stop(audioCtx.currentTime + 0.3);
  } catch (err) {
    console.warn("Audio not available yet:", err);
  }
}

// ---------------------------------------------------------
// FORMATTING
// ---------------------------------------------------------
function formatPeso(amount) {
  return "₱" + Number(amount).toLocaleString("en-PH", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
}

// ---------------------------------------------------------
// FETCH + RENDER (dashboard data)
// ---------------------------------------------------------
async function fetchDashboardData() {
  try {
    const response = await fetch("api/get_data.php", { cache: "no-store" });

    if (!response.ok) {
      throw new Error("Server responded with " + response.status);
    }

    const data = await response.json();
    renderDashboard(data);

  } catch (err) {
    console.error("Failed to fetch dashboard data:", err);
    showConnectionError();
  }
}

function renderDashboard(data) {
  renderTotal(data);
  renderBreakdown(data.counts);
  renderStatistics(data.counts, data.total_coins);
  renderDeviceStatus(data.device_online, data.last_seen);
  renderRecentTransactions(data.recent);
}

function renderTotal(data) {
  const totalEl = document.getElementById("total");
  totalEl.textContent = formatPeso(data.total);

  const isFirstLoad = lastTotalCoins === null;
  const gotNewCoin = !isFirstLoad && data.total_coins > lastTotalCoins;

  if (gotNewCoin) {
    totalEl.classList.remove("pulse");
    void totalEl.offsetWidth;
    totalEl.classList.add("pulse");
    playPing();
  }

  lastTotalCoins = data.total_coins;

  const label = data.total_coins + " coin" + (data.total_coins === 1 ? "" : "s") + " inserted";
  document.getElementById("coin-count-text").textContent = label;
}

function renderBreakdown(counts) {
  [1, 5, 10, 20].forEach((value) => {
    document.getElementById("count-" + value).textContent = counts[value] ?? 0;
  });
}

function renderStatistics(counts, totalCoins) {
  document.querySelectorAll(".stat-row").forEach((row) => {
    const coin = row.dataset.coin;
    const count = counts[coin] ?? 0;
    const percent = totalCoins > 0 ? Math.round((count / totalCoins) * 100) : 0;

    row.querySelector(".pct").textContent = percent + "% (" + count + ")";
    row.querySelector(".bar-fill").style.width = percent + "%";
  });
}

function renderDeviceStatus(isOnline, lastSeen) {
  const dot = document.getElementById("status-dot");
  const statusText = document.getElementById("status-text");

  dot.className = "dot " + (isOnline ? "online" : "offline");
  statusText.textContent = (isOnline ? "Device online" : "Device offline") + " — last seen " + lastSeen;
}

function renderRecentTransactions(transactions) {
  const container = document.getElementById("tx-container");

  if (!transactions || transactions.length === 0) {
    container.innerHTML = '<div class="empty">No transactions yet</div>';
    return;
  }

  container.innerHTML = transactions.map((tx) => `
    <div class="tx-row">
      <span class="tx-coin">₱${tx.coin_value}</span>
      <span class="tx-time">${tx.created_at}</span>
    </div>
  `).join("");
}

function showConnectionError() {
  document.getElementById("status-dot").className = "dot offline";
  document.getElementById("status-text").textContent = "Connection error";
}

// ---------------------------------------------------------
// SAVINGS GOALS / PAG-IIPUNAN — full CRUD
// ---------------------------------------------------------
function initGoals() {
  const addBtn = document.getElementById("add-goal-btn");
  const form = document.getElementById("goal-form");
  const saveBtn = document.getElementById("goal-save-btn");
  const cancelBtn = document.getElementById("goal-cancel-btn");

  addBtn.addEventListener("click", () => {
    document.getElementById("goal-id").value = "";
    document.getElementById("goal-name").value = "";
    document.getElementById("goal-target").value = "";
    form.style.display = "flex";
    document.getElementById("goal-name").focus();
  });

  cancelBtn.addEventListener("click", () => {
    form.style.display = "none";
  });

  saveBtn.addEventListener("click", () => {
    const id = document.getElementById("goal-id").value;
    const name = document.getElementById("goal-name").value.trim();
    const target = document.getElementById("goal-target").value.trim();

    if (!name) { alert("Please enter a goal name."); return; }
    if (!target || Number(target) <= 0) { alert("Please enter a valid target amount."); return; }

    const payload = { name: name, target_amount: Number(target) };

    if (id) {
      payload.id = Number(id);
      fetch("api/goals.php?action=update", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      }).then(() => { form.style.display = "none"; fetchGoals(); });
    } else {
      fetch("api/goals.php?action=add", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      }).then(() => { form.style.display = "none"; fetchGoals(); });
    }
  });

  fetchGoals();
}

async function fetchGoals() {
  try {
    const response = await fetch("api/goals.php?action=list", { cache: "no-store" });
    const goals = await response.json();
    renderGoals(goals);
  } catch (err) {
    console.error("Failed to fetch goals:", err);
  }
}

function renderGoals(goals) {
  const container = document.getElementById("goals-container");

  if (!goals || goals.length === 0) {
    container.innerHTML = '<div class="empty">No savings goals yet — add one!</div>';
    return;
  }

  container.innerHTML = goals.map((g) => `
    <div class="goal-row">
      <div class="goal-info">
        <div class="goal-name">${escapeHtml(g.name)}</div>
        <div class="goal-target">Target: ${formatPeso(g.target_amount)}</div>
      </div>
      <div class="goal-actions">
        <button onclick="editGoal(${g.id}, '${escapeAttr(g.name)}', ${g.target_amount})">✏️</button>
        <button class="btn-delete" onclick="deleteGoal(${g.id})">🗑️</button>
      </div>
    </div>
  `).join("");
}

function editGoal(id, name, target) {
  document.getElementById("goal-id").value = id;
  document.getElementById("goal-name").value = name;
  document.getElementById("goal-target").value = target;
  document.getElementById("goal-form").style.display = "flex";
  document.getElementById("goal-name").focus();
}

function deleteGoal(id) {
  if (!confirm("Delete this goal?")) return;

  fetch("api/goals.php?action=delete", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id: id }),
  }).then(() => fetchGoals());
}

function escapeHtml(str) {
  const div = document.createElement("div");
  div.textContent = str;
  return div.innerHTML;
}

function escapeAttr(str) {
  return str.replace(/\\/g, "\\\\").replace(/'/g, "\\'").replace(/"/g, '\\"');
}

// ---------------------------------------------------------
// START
// ---------------------------------------------------------
document.addEventListener("DOMContentLoaded", () => {
  initTheme();
  initSoundToggle();
  initGoals();

  fetchDashboardData();
  setInterval(fetchDashboardData, POLL_INTERVAL_MS);
});
