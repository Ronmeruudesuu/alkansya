// assets/javascript/clear_history.js
//
// Logic for history clearing UI. Requires elements:
// - #btn-back
// - #btn-clear
// - #confirm-input
// - #clear-message

document.addEventListener("DOMContentLoaded", () => {
  const backBtn = document.getElementById("btn-back");
  const clearBtn = document.getElementById("btn-clear");
  const confirmInput = document.getElementById("confirm-input");
  const msgEl = document.getElementById("clear-message");

  const REQUIRED = "CLEAR";

  function syncClearState() {
    const v = (confirmInput && confirmInput.value)
      ? confirmInput.value.trim().toUpperCase()
      : "";

    const shouldEnable = v === REQUIRED;

    if (clearBtn) {
      clearBtn.disabled = !shouldEnable;
      clearBtn.textContent = shouldEnable ? "Delete History" : "Type CLEAR";
    }
  }

  backBtn?.addEventListener("click", () => {
    window.location.href = "history.php";
  });

  confirmInput?.addEventListener("input", () => {
    if (msgEl) {
      msgEl.textContent = "";
      msgEl.className = "wrong-coin-message";
    }
    syncClearState();
  });

  syncClearState();

  clearBtn?.addEventListener("click", async () => {
    if (clearBtn.disabled) return;

    const ok = confirm("Delete ALL insert + withdrawal history? This cannot be undone. Proceed?");
    if (!ok) return;

    clearBtn.disabled = true;
    clearBtn.textContent = "Deleting…";
    if (msgEl) msgEl.textContent = "Deleting history...";

    try {
      const response = await fetch("api/clear_history.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "clear_all" }),
        cache: "no-store",
      });

      const data = await response.json().catch(() => null);

      if (!response.ok || !data || data.status !== "success") {
        throw new Error((data && data.message) ? data.message : "Failed to clear history");
      }

      if (msgEl) {
        msgEl.textContent = "History cleared successfully.";
        msgEl.classList.add("success");
      }

      setTimeout(() => {
        window.location.href = "index.php";
      }, 800);

    } catch (err) {
      console.error(err);
      if (msgEl) {
        msgEl.textContent = "Error: " + err.message;
        msgEl.classList.add("error");
      }
      clearBtn.disabled = false;
      clearBtn.textContent = "Delete History";
    }
  });
});
