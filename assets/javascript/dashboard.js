// assets/javascript/dashboard.js
//
// Handles polling api/get_data.php on a timer, updating the DOM
// with results, theme/sound toggles, and the wrong-coin correction widget.

const POLL_INTERVAL_MS = 2000;

let lastTotalCoins = null;
let soundOn = localStorage.getItem("alkansya-sound") !== "off";
let audioCtx = null;

function initTheme() {
  const root = document.documentElement;
  const savedTheme = localStorage.getItem("alkansya-theme") || "dark";
  root.setAttribute("data-theme", savedTheme);

  const toggleHandler = () => {
    const nextTheme = root.getAttribute("data-theme") === "dark" ? "light" : "dark";
    root.setAttribute("data-theme", nextTheme);
    localStorage.setItem("alkansya-theme", nextTheme);
  };

  document.getElementById("theme-toggle")?.addEventListener("click", toggleHandler);
  document.getElementById("theme-toggle-mob")?.addEventListener("click", toggleHandler);
}

function initSoundToggle() {
  const soundBtn = document.getElementById("sound-toggle");
  const soundMob = document.getElementById("sound-toggle-mob");
  updateSoundButtonLabels();

  const toggleSound = () => {
    soundOn = !soundOn;
    localStorage.setItem("alkansya-sound", soundOn ? "on" : "off");
    updateSoundButtonLabels();
  };

  soundBtn?.addEventListener("click", toggleSound);
  soundMob?.addEventListener("click", toggleSound);
}

function updateSoundButtonLabels() {
  const soundBtn = document.getElementById("sound-toggle");
  const soundMob = document.getElementById("sound-toggle-mob");

  const text = soundOn ? "Sound: On" : "Sound: Off";

  if (soundBtn) {
    soundBtn.textContent = text;
    soundBtn.classList.toggle("active-mute", !soundOn);
  }
  if (soundMob) {
    soundMob.textContent = text;
    soundMob.classList.toggle("active-mute", !soundOn);
  }
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

function formatPeso(amount) {
  return "₱" + Number(amount).toLocaleString("en-PH", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
}

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
  renderLastInsertedCoin(data.total_coins, data.recent);
  renderBreakdown(data.counts);
  renderStatistics(data.counts, data.total_coins);
  renderDeviceStatus(data.device_online, data.last_seen);
  renderRecentTransactions(data.recent);
}

function renderTotal(data) {
  const totalEl = document.getElementById("total");
  if (!totalEl) return;
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

  const label = data.total_coins + " coin" + (data.total_coins === 1 ? "" : "s") + " securely inserted";
  const countTextEl = document.getElementById("coin-count-text");
  if (countTextEl) countTextEl.textContent = label;
}

function renderBreakdown(counts) {
  [1, 5, 10, 20].forEach((value) => {
    const el = document.getElementById("count-" + value);
    if (el) el.textContent = counts[value] ?? 0;
  });
}

function renderStatistics(counts, totalCoins) {
  document.querySelectorAll(".stat-row").forEach((row) => {
    const coin = row.dataset.coin;
    const count = counts[coin] ?? 0;
    const percent = totalCoins > 0 ? Math.round((count / totalCoins) * 100) : 0;

    const pctEl = row.querySelector(".pct");
    const fillEl = row.querySelector(".bar-fill");
    if (pctEl) pctEl.textContent = percent + "% (" + count + ")";
    if (fillEl) fillEl.style.width = percent + "%";
  });
}

function renderDeviceStatus(isOnline, lastSeen) {
  const dot = document.getElementById("status-dot");
  const statusText = document.getElementById("status-text");

  if (dot) dot.className = "dot " + (isOnline ? "online" : "offline");
  if (statusText) statusText.textContent = (isOnline ? "Device online" : "Device offline") + " — last seen " + lastSeen;
}

function renderLastInsertedCoin(totalCoins, recentTransactions) {
  const btn = document.getElementById("wrong-coin-btn");
  const hint = document.getElementById("wrong-coin-last-insert");
  const form = document.getElementById("wrong-coin-form");
  const confirmBtn = document.getElementById("wrong-coin-confirm");
  const msgEl = document.getElementById("wrong-coin-message");

  if (!btn || !hint) return;

  if (totalCoins <= 0) {
    btn.disabled = true;
    hint.textContent = "Most recent inserted: — (vault empty)";

    form?.classList.remove("is-open");
    if (confirmBtn) {
      confirmBtn.disabled = true;
      confirmBtn.textContent = "Apply Correction";
    }
    if (msgEl) msgEl.textContent = "";
    return;
  }

  btn.disabled = false;

  const mostRecent = Array.isArray(recentTransactions) && recentTransactions.length
    ? recentTransactions[0]
    : null;

  const label = mostRecent ? formatPeso(mostRecent.coin_value) : "—";
  hint.textContent = `Most recent inserted: ${label}`;

  // If the correction form is not open, re-enable confirm button
  if (confirmBtn && form && !form.classList.contains("is-open")) {
    confirmBtn.disabled = false;
    confirmBtn.textContent = "Apply Correction";
  }
}

function renderRecentTransactions(transactions) {
  const container = document.getElementById("tx-container");
  if (!container) return;

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
  const dot = document.getElementById("status-dot");
  const statusText = document.getElementById("status-text");
  if (dot) dot.className = "dot offline";
  if (statusText) statusText.textContent = "Connection error";
}

function initWrongCoinCorrection() {
  const btn = document.getElementById("wrong-coin-btn");
  const form = document.getElementById("wrong-coin-form");
  const cancelBtn = document.getElementById("wrong-coin-cancel");
  const confirmBtn = document.getElementById("wrong-coin-confirm");
  const optionsWrap = document.getElementById("wrong-coin-options");
  const msgEl = document.getElementById("wrong-coin-message");

  if (!btn || !form || !cancelBtn || !confirmBtn || !optionsWrap || !msgEl) {
    return;
  }

  // Prevent user confusion before the first poll updates the real state
  btn.disabled = true;
  confirmBtn.disabled = true;
  form.classList.remove("is-open");
  confirmBtn.textContent = "Apply Correction";

  const optionButtons = Array.from(optionsWrap.querySelectorAll(".wrong-coin-option"));
  let selectedCoin = null;

  function setSelectedCoin(coin) {
    selectedCoin = coin;
    optionButtons.forEach((b) => {
      const isSelected = Number(b.dataset.coin) === coin;
      b.classList.toggle("selected", isSelected);
    });
  }

  function clearSelection() {
    setSelectedCoin(null);
  }

  function showMessage(message, variant) {
    msgEl.textContent = message;
    msgEl.className = "wrong-coin-message";
    if (variant) msgEl.classList.add(variant);
  }

  btn.addEventListener("click", () => {
    if (btn.disabled) return;
    showMessage("");
    form.classList.add("is-open");
    clearSelection();
  });

  cancelBtn.addEventListener("click", () => {
    form.classList.remove("is-open");
    showMessage("");
    clearSelection();

    // If vault is empty, keep confirm disabled
    if (btn.disabled && confirmBtn) {
      confirmBtn.disabled = true;
      confirmBtn.textContent = "Apply Correction";
    }
  });

  optionsWrap.addEventListener("click", (e) => {
    const optionBtn = e.target.closest(".wrong-coin-option");
    if (!optionBtn || !optionsWrap.contains(optionBtn)) return;

    const coin = Number(optionBtn.dataset.coin);
    if (!coin) return;

    setSelectedCoin(coin);
  });

  confirmBtn.addEventListener("click", async () => {
    const coin = selectedCoin;
    if (!coin) {
      showMessage("Please select which coin you actually inserted.", "error");
      return;
    }

    confirmBtn.disabled = true;
    confirmBtn.textContent = "Correcting...";

    try {
      const response = await fetch("api/correct_last_coin.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ coin: coin }),
        cache: "no-store",
      });

      const data = await response.json().catch(() => null);

      if (!response.ok || !data || data.status !== "success") {
        const errMsg = data && data.message ? data.message : "Failed to correct last coin";
        showMessage(errMsg, "error");
        return;
      }

      const oldCoin = data.old_coin;
      const newCoin = data.new_coin;

      if (oldCoin === newCoin) {
        showMessage(`Last coin is already ${formatPeso(newCoin)}.`, "success");
      } else {
        showMessage(`Corrected: ${formatPeso(oldCoin)} → ${formatPeso(newCoin)}`, "success");
      }

      form.classList.remove("is-open");
      fetchDashboardData();
    } catch (err) {
      console.error("Failed to correct last coin:", err);
      showMessage("Connection error while correcting coin.", "error");
    } finally {
      confirmBtn.disabled = false;
      confirmBtn.textContent = "Apply Correction";
    }
  });
}

document.addEventListener("DOMContentLoaded", () => {
  initTheme();
  initSoundToggle();
  initWrongCoinCorrection();

  fetchDashboardData();
  setInterval(fetchDashboardData, POLL_INTERVAL_MS);
});
