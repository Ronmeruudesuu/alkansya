// assets/javascript/goals.js
//
// Handles CRUD operations and rendering for the dedicated Savings Goals page.

document.addEventListener("DOMContentLoaded", () => {
  const addBtn = document.getElementById("add-goal-btn");
  const form = document.getElementById("goal-form");
  const saveBtn = document.getElementById("goal-save-btn");
  const cancelBtn = document.getElementById("goal-cancel-btn");

  if (addBtn && form && saveBtn && cancelBtn) {
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
      const endpoint = id ? "api/goals.php?action=update" : "api/goals.php?action=add";
      if (id) payload.id = Number(id);

      fetch(endpoint, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      }).then(() => {
        form.style.display = "none";
        fetchGoals();
      });
    });

    fetchGoals();
  }
});

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
  if (!container) return;

  if (!goals || goals.length === 0) {
    container.innerHTML = '<div class="empty">No savings goals yet — create one to start tracking!</div>';
    return;
  }

  container.innerHTML = goals.map((g) => `
    <div class="goal-row">
      <div class="goal-info">
        <div class="goal-name">${escapeHtml(g.name)}</div>
        <div class="goal-target">Target: ${formatPeso(g.target_amount)}</div>
      </div>
      <div class="goal-actions">
        <button type="button" onclick="editGoal(${g.id}, '${escapeAttr(g.name)}', ${g.target_amount})">Edit</button>
        <button type="button" class="btn-delete" onclick="deleteGoal(${g.id})">Delete</button>
      </div>
    </div>
  `).join("");
}

window.editGoal = function(id, name, target) {
  document.getElementById("goal-id").value = id;
  document.getElementById("goal-name").value = name;
  document.getElementById("goal-target").value = target;
  document.getElementById("goal-form").style.display = "flex";
  document.getElementById("goal-name").focus();
};

window.deleteGoal = function(id) {
  if (!confirm("Delete this goal?")) return;

  fetch("api/goals.php?action=delete", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id: id }),
  }).then(() => fetchGoals());
};

function formatPeso(amount) {
  return "₱" + Number(amount).toLocaleString("en-PH", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
}

function escapeHtml(str) {
  const div = document.createElement("div");
  div.textContent = str;
  return div.innerHTML;
}

function escapeAttr(str) {
  return str.replace(/\\/g, "\\\\").replace(/'/g, "\\'").replace(/"/g, '\\"');
}
