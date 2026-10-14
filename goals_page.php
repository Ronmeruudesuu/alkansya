<?php
// goals_page.php
//
// Separate dedicated page for Pag-iipunan Savings Goals CRUD.
// Reuses the same database + styling as the rest of the app.

require_once "config/database.php";
require_once "includes/functions.php";

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Savings Goals — Alkansya</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <div class="app-layout">

    <!-- SIDEBAR NAVIGATION -->
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
        <a href="goals_page.php" class="nav-item active">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          <span>Savings Goals</span>
        </a>
        <a href="withdraw_page.php" class="nav-item">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h18M3 17h18"/><path d="M7 7v10"/><path d="M17 7v10"/></svg>
          <span>Withdraw</span>
        </a>
        <a href="history_clear.php" class="nav-item">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
          <span>Clear History</span>
        </a>
      </nav>

      <div class="sidebar-footer">
        <div class="preferences-bar">
          <button class="pref-btn" id="theme-toggle" type="button" title="Toggle Theme">Theme</button>
        </div>
      </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">

      <!-- Mobile Topbar -->
      <header class="mobile-topbar">
        <div class="brand">
          <div class="brand-logo">AV</div>
          <span class="brand-name">Alkansya</span>
        </div>
        <div class="topbar-actions">
          <a href="index.php" class="icon-btn">Dashboard</a>
          <a href="withdraw_page.php" class="icon-btn">Withdraw</a>
          <a href="history_clear.php" class="icon-btn">Reset</a>
          <button class="icon-btn" id="theme-toggle-mob" type="button">Theme</button>
        </div>
      </header>

      <div class="bento-container">

        <section class="card span-full goals-section-card">
          <div class="section-title">
            <span>Pag-iipunan Goals</span>
            <button class="icon-btn action-btn-add" id="add-goal-btn" type="button">+ New Goal</button>
          </div>

          <!-- Add / Edit form (hidden by default) -->
          <div class="goal-form" id="goal-form" style="display:none;">
            <input type="hidden" id="goal-id" value="">
            <div class="goal-form-grid">
              <input type="text" id="goal-name" class="goal-input" placeholder="What are you saving for? (e.g. Dream Laptop)">
              <input type="number" id="goal-target" class="goal-input" placeholder="Target amount (₱)" min="1" step="1">
            </div>
            <div class="goal-form-actions">
              <button class="btn btn-save" id="goal-save-btn" type="button">Save Goal</button>
              <button class="btn btn-cancel" id="goal-cancel-btn" type="button">Cancel</button>
            </div>
          </div>

          <div class="goals-list" id="goals-container">
            <div class="empty">No savings goals yet — create one to start tracking!</div>
          </div>
        </section>

        <!-- Back Link card -->
        <section class="card span-full history-footer-card">
          <div class="back-link">
            <a href="index.php">&larr; Back to Dashboard</a>
          </div>
        </section>

      </div><!-- /bento-container -->

    </main>

  </div><!-- /app-layout -->

  <script src="assets/javascript/goals.js"></script>
  <script src="assets/javascript/theme.js"></script>
</body>
</html>
