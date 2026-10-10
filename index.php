<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Alkansya Coin Counter</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container">

  <!-- ── Top bar: status left, buttons right ── -->
  <div class="topbar">
    <div class="status-inline">
      <span class="dot" id="status-dot"></span>
      <span id="status-text">Checking device…</span>
    </div>
    <div class="topbar-actions">
      <button class="icon-btn" id="sound-toggle">🔊 Sound</button>
      <button class="icon-btn" id="theme-toggle">🌓 Theme</button>
    </div>
  </div>

  <div class="bento-grid">

    <!-- ── Hero: total savings (spans full) ── -->
    <div class="card span-full">
      <div class="savings-card">
        <div class="label">Total Savings</div>
        <div class="amount" id="total">₱0.00</div>
        <div class="subtext" id="coin-count-text">0 coins inserted</div>
      </div>
    </div>

    <!-- ── Coin breakdown: 4 individual bento tiles ── -->
    <div class="coin-tile" data-coin-tile="1">
      <div class="coin-label">₱1</div>
      <div class="coin-count" id="count-1">0</div>
      <div class="coin-count-label">coins</div>
    </div>
    <div class="coin-tile" data-coin-tile="5">
      <div class="coin-label">₱5</div>
      <div class="coin-count" id="count-5">0</div>
      <div class="coin-count-label">coins</div>
    </div>
    <div class="coin-tile" data-coin-tile="10">
      <div class="coin-label">₱10</div>
      <div class="coin-count" id="count-10">0</div>
      <div class="coin-count-label">coins</div>
    </div>
    <div class="coin-tile" data-coin-tile="20">
      <div class="coin-label">₱20</div>
      <div class="coin-count" id="count-20">0</div>
      <div class="coin-count-label">coins</div>
    </div>

    <!-- ── Coin statistics (left column) ── -->
    <div class="card">
      <div class="section-title">Statistics</div>
      <div class="stats-list">
        <div class="stat-row" data-coin="1">
          <div class="stat-top"><span class="coin">₱1</span><span class="pct">0%</span></div>
          <div class="bar-track"><div class="bar-fill"></div></div>
        </div>
        <div class="stat-row" data-coin="5">
          <div class="stat-top"><span class="coin">₱5</span><span class="pct">0%</span></div>
          <div class="bar-track"><div class="bar-fill"></div></div>
        </div>
        <div class="stat-row" data-coin="10">
          <div class="stat-top"><span class="coin">₱10</span><span class="pct">0%</span></div>
          <div class="bar-track"><div class="bar-fill"></div></div>
        </div>
        <div class="stat-row" data-coin="20">
          <div class="stat-top"><span class="coin">₱20</span><span class="pct">0%</span></div>
          <div class="bar-track"><div class="bar-fill"></div></div>
        </div>
      </div>
    </div>

    <!-- ── Recent transactions (right column) ── -->
    <div class="card">
      <div class="section-title">
        Recent Transactions
        <a href="history.php">View all &rarr;</a>
      </div>
      <div class="tx-list" id="tx-container">
        <div class="empty">No transactions yet</div>
      </div>
    </div>

    <!-- ── Savings Goals / Pag-iipunan (spans full) ── -->
    <div class="card span-full">
      <div class="section-title">
        🎯 Pag-iipunan
        <button class="icon-btn" id="add-goal-btn">+ Add Goal</button>
      </div>

      <!-- Add / Edit form (hidden by default) -->
      <div class="goal-form" id="goal-form" style="display:none;">
        <input type="hidden" id="goal-id" value="">
        <input type="text" id="goal-name" class="goal-input" placeholder="What are you saving for? (e.g. iPhone)">
        <input type="number" id="goal-target" class="goal-input" placeholder="Target amount (₱)" min="1" step="1">
        <div class="goal-form-actions">
          <button class="btn btn-save" id="goal-save-btn">Save</button>
          <button class="btn btn-cancel" id="goal-cancel-btn">Cancel</button>
        </div>
      </div>

      <div class="goals-list" id="goals-container">
        <div class="empty">No savings goals yet — add one!</div>
      </div>
    </div>

  </div><!-- /bento-grid -->

</div>

<script src="assets/javascript/dashboard.js"></script>
<script>
// Debug: Log when script loads
console.log('✅ Dashboard script loaded');
console.log('🔍 Checking elements:', {
  'add-goal-btn': document.getElementById('add-goal-btn'),
  'goal-form': document.getElementById('goal-form'),
  'goals-container': document.getElementById('goals-container')
});
console.log('🔍 Checking functions:', {
  'initGoals': typeof initGoals,
  'fetchGoals': typeof fetchGoals
});
</script>
</body>
</html>
