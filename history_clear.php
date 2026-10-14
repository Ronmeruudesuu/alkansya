<?php
// history_clear.php
// Dedicated page to clear insert + withdrawal history.

require_once "config/database.php";
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Clear History — Alkansya</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <div class="app-layout">

    <aside class="sidebar">
      <div class="brand">
        <div class="brand-logo">AV</div>
        <div class="brand-info">
          <span class="brand-name">Alkansya</span>
          <span class="brand-tag">Smart Vault</span>
        </div>
      </div>

      <nav class="nav-links">
        <a href="index.php" class="nav-item">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
          <span>Dashboard</span>
        </a>
        <a href="history.php" class="nav-item">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <span>History Log</span>
        </a>
        <a href="goals_page.php" class="nav-item">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          <span>Savings Goals</span>
        </a>
        <a href="withdraw_page.php" class="nav-item">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h18M3 17h18"/><path d="M7 7v10"/><path d="M17 7v10"/></svg>
          <span>Withdraw</span>
        </a>
        <a href="history_clear.php" class="nav-item active">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
          <span>Clear History</span>
        </a>
      </nav>

      <div class="sidebar-footer">
        <div class="preferences-bar">
          <button class="pref-btn" id="theme-toggle" type="button" title="Toggle Theme">Theme</button>
          <button class="pref-btn" id="sound-toggle" type="button" title="Toggle Sound">Sound</button>
        </div>
      </div>
    </aside>

    <main class="main-content">

      <header class="mobile-topbar">
        <div class="brand">
          <div class="brand-logo">AV</div>
          <span class="brand-name">Alkansya</span>
        </div>
        <div class="topbar-actions">
          <a href="index.php" class="icon-btn">Dashboard</a>
          <a href="history.php" class="icon-btn">History</a>
          <a href="withdraw_page.php" class="icon-btn">Withdraw</a>
          <a href="goals_page.php" class="icon-btn">Goals</a>
          <button class="icon-btn" id="theme-toggle-mob" type="button">Theme</button>
          <button class="icon-btn" id="sound-toggle-mob" type="button">Sound</button>
        </div>
      </header>

      <div class="bento-container">

        <section class="card span-full">
          <div class="section-title">
            <span>Danger Zone</span>
            <span class="title-pill">Clear insert/withdraw history</span>
          </div>

          <p style="color: var(--text-muted); font-weight:700; font-size:13px; margin-top:8px; text-align:center;">
            This will delete all <b>transaction history</b> and <b>withdrawal history</b> from the server.
            After clearing, remaining coins become 0.
          </p>

          <div style="display:flex; justify-content:center; margin-top:14px;">
            <a href="history.php" class="icon-btn" style="text-decoration:none;">View history log</a>
            <a href="withdraw_page.php" class="icon-btn" style="text-decoration:none; margin-left:10px;">View withdrawals</a>
          </div>

          <div style="margin-top:16px;">
            <div style="text-align:center; color: var(--text-muted); font-size:12px; font-weight:800; margin-bottom:10px;">
              Type <b>CLEAR</b> to enable deletion
            </div>
            <div style="display:flex; justify-content:center;">
              <input id="confirm-input" class="goal-input" type="text" placeholder="Type CLEAR" style="max-width:320px; text-align:center;" />
            </div>
          </div>

          <div style="display:flex; justify-content:center; margin-top:18px;">
            <button class="btn btn-cancel" id="btn-back">Cancel</button>
            <button class="btn btn-wrong" id="btn-clear" style="margin-left:10px;" disabled>Type CLEAR</button>
          </div>

          <div class="wrong-coin-message" id="clear-message" style="margin-top:14px;"></div>
        </section>

      </div>

    </main>

  </div>

  <script src="assets/javascript/theme.js"></script>
  <script src="assets/javascript/clear_history.js"></script>
</body>
</html>
