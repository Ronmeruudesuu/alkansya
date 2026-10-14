// assets/javascript/withdraw.js
//
// Handles multi-denomination withdrawals + inventory + withdrawal history.
//
// Theme + sound label toggles are handled by assets/javascript/theme.js
// (loaded by withdraw_page.php), so this file focuses only on withdrawal logic.

const WITHDRAW_COINS = [1, 5, 10, 20];
const POLL_INTERVAL_MS = 2000;

document.addEventListener("DOMContentLoaded", () => {
  const submitBtn = document.getElementById("withdraw-submit");
  const cancelBtn = document.getElementById("withdraw-cancel");

  if (!submitBtn) return;

  submitBtn.addEventListener("click", submitWithdrawal);
  cancelBtn?.addEventListener("click", clearCounts);

  // Keep inputs in sync with remaining counts
  WITHDRAW_COINS.forEach((c) => {
    const input = document.getElementById(`count-${c}`);
    input?.addEventListener("input", () => clampInput(c));
  });

  fetchWithdrawalState();
  setInterval(fetchWithdrawalState, POLL_INTERVAL_MS);
});

function clampInput(coin) {
  const input = document.getElementById(`count-${coin}`);
  const remEl = document.getElementById(`rem-${coin}`);
  if (!input || !remEl) return;

  const remaining = Number(remEl.textContent) || 0;
  let v = Number(input.value);
  if (!Number.isFinite(v) || v < 0) v = 0;
  if (v > remaining) v = remaining;

  input.value = String(v);
}

function clearCounts() {
  WITHDRAW_COINS.forEach((c) => {
    const input = document.getElementById(`count-${c}`);
    if (input) input.value = "0";
  });

  showMessage("", "");
}

function showMessage(message, variant) {
  const msgEl = document.getElementById("withdraw-message");
  if (!msgEl) return;

  msgEl.textContent = message;
  msgEl.className = "wrong-coin-message";

  if (variant === "success") msgEl.classList.add("success");
  if (variant === "error") msgEl.classList.add("error");
}

async function fetchWithdrawalState() {
  try {
    const response = await fetch("api/withdraw.php?action=state", { cache: "no-store" });
    const data = await response.json();

    if (!response.ok || !data || (data.status && data.status !== "success")) {
      throw new Error(data?.message || "Failed to load withdrawal state");
    }

    renderWithdrawalState(data);
  } catch (err) {
    console.error("Failed to fetch withdrawal state:", err);
    const coinsTextEl = document.getElementById("remaining-coins-text");
    if (coinsTextEl) coinsTextEl.textContent = "Error loading state (check connection)";
  }
}

function renderWithdrawalState(data) {
  const remaining = data.remaining || {};
  const counts = remaining.counts || {};
  const totalCoins = remaining.total_coins ?? 0;

  const totalEl = document.getElementById("remaining-total");
  const coinsTextEl = document.getElementById("remaining-coins-text");

  if (totalEl) totalEl.textContent = formatPeso(remaining.total ?? 0);
  if (coinsTextEl) coinsTextEl.textContent = `${totalCoins} coins remaining`;

  WITHDRAW_COINS.forEach((c) => {
    const remEl = document.getElementById(`rem-${c}`);
    if (remEl) remEl.textContent = String(counts[c] ?? 0);

    // Clamp current input value to new remaining
    const input = document.getElementById(`count-${c}`);
    if (input) {
      const v = Number(input.value);
      if (!Number.isFinite(v)) input.value = "0";
      input.value = String(Math.max(0, Math.min(v || 0, Number(counts[c] ?? 0))));
    }
  });

  renderRemainingStatistics(counts, totalCoins);
  renderWithdrawHistory(data.withdrawals || []);
}

function renderRemainingStatistics(counts, totalCoins) {
  document.querySelectorAll(".stat-row").forEach((row) => {
    const coin = row.dataset.coin;
    if (!coin) return;

    const count = counts[coin] ?? 0;
    const percent = totalCoins > 0 ? Math.round((count / totalCoins) * 100) : 0;

    const pctEl = row.querySelector(".pct");
    const fillEl = row.querySelector(".bar-fill");

    if (pctEl) pctEl.textContent = percent + "% (" + count + ")";
    if (fillEl) fillEl.style.width = percent + "%";
  });
}

function renderWithdrawHistory(withdrawals) {
  const container = document.getElementById("withdraw-history");
  const countEl = document.getElementById("withdraw-history-count");

  if (countEl) countEl.textContent = String(withdrawals.length || 0);
  if (!container) return;

  if (!withdrawals || withdrawals.length === 0) {
    container.innerHTML = '<div class="empty">No withdrawals yet</div>';
    return;
  }

  container.innerHTML = withdrawals
    .map((w) => {
      const parts = (w.items || [])
        .map((it) => ` ${it.coin_count}×₱${it.coin_value}`)
        .join(" ");

      const coinSummary = (w.items && w.items.length) ? parts : "";

      return `
        <div class="tx-row">
          <span class="tx-coin">- ₱${w.total_amount ?? 0}</span>
          <span class="tx-time">${w.created_at}${coinSummary}</span>
        </div>
      `;
    })
    .join("");
}

async function submitWithdrawal() {
  const items = [];

  for (const c of WITHDRAW_COINS) {
    const input = document.getElementById(`count-${c}`);
    if (!input) continue;

    const cnt = Number(input.value);
    if (!Number.isFinite(cnt) || cnt <= 0) continue;

    items.push({ coin_value: c, coin_count: Math.floor(cnt) });
  }

  if (items.length === 0) {
    showMessage("Select at least one coin piece to withdraw.", "error");
    return;
  }

  submitBtnState(true);
  showMessage("Submitting withdrawal…", "");

  try {
    const response = await fetch("api/withdraw.php?action=submit", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ items }),
      cache: "no-store",
    });

    const data = await response.json().catch(() => null);

    if (!response.ok || !data || data.status !== "success") {
      const msg = data && data.message ? data.message : "Withdrawal failed";
      showMessage(msg, "error");
      return;
    }

    showMessage("Withdrawal saved successfully.", "success");

    clearCounts();
    await fetchWithdrawalState();
  } catch (err) {
    console.error("Withdrawal submit failed:", err);
    showMessage("Connection error while withdrawing coins.", "error");
  } finally {
    submitBtnState(false);
  }
}

function submitBtnState(isSubmitting) {
  const submitBtn = document.getElementById("withdraw-submit");
  if (!submitBtn) return;

  submitBtn.disabled = isSubmitting;
  submitBtn.textContent = isSubmitting ? "Withdrawing…" : "Withdraw";
}

function formatPeso(amount) {
  return "₱" + Number(amount).toLocaleString("en-PH", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
}
