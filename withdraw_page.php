<?php
// withdraw_page.php
//
// UI for multi-denomination withdrawals (opening/take-out coins).
// Shows remaining coins + withdrawal history.

require_once "config/database.php";
require_once "includes/functions.php";

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Withdraw Coins — Alkansya</title>
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
        <a href="goals_page.php" class="nav-item">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          <span>Savings Goals</span>
        </a>
        <a href="withdraw_page.php" class="nav-item active">
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
          <button class="pref-btn" id="sound-toggle" type="button" title="Toggle Sound">Sound</button>
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
          <a href="index.php" class="icon-btn" title="Dashboard">Dashboard</a>
          <a href="history.php" class="icon-btn" title="History">History</a>
          <a href="goals_page.php" class="icon-btn" title="Goals">Goals</a>
          <a href="history_clear.php" class="icon-btn" title="Clear History">Reset</a>
          <button class="icon-btn" id="theme-toggle-mob" type="button">Theme</button>
          <button class="icon-btn" id="sound-toggle-mob" type="button">Sound</button>
        </div>
      </header>

      <div class="bento-container">

        <!-- Withdraw Card -->
        <section class="card span-full hero-card">
          <div class="savings-card">
            <div class="label-badge">Withdrawable Balance</div>
            <div class="amount" id="remaining-total">₱0.00</div>
            <div class="subtext" id="remaining-coins-text">0 coins remaining</div>

            <div class="withdraw-section" style="width:100%; margin-top: 18px; display:flex; flex-direction:column; align-items:center; gap:12px;">

              <div class="section-title" style="width:100%; justify-content:center;">
                <span>Choose coins to withdraw</span>
                <span class="title-pill">Multi-denomination</span>
              </div>

              <div class="withdraw-form" id="withdraw-form">
                <div class="withdraw-items">

                  <div class="withdraw-item" style="background: var(--card-bg-alt); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 14px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                      <div style="font-family: var(--font-mono); font-weight: 900; color: var(--coin1);">₱1</div>
                      <div style="font-size:11px; color: var(--text-muted); font-weight:700;">Remaining: <span id="rem-1">0</span></div>
                    </div>
                    <input id="count-1" type="number" min="0" step="1" value="0" class="goal-input" placeholder="Pieces" style="padding: 10px 12px;" />
                  </div>

                  <div class="withdraw-item" style="background: var(--card-bg-alt); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 14px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                      <div style="font-family: var(--font-mono); font-weight: 900; color: var(--coin5);">₱5</div>
                      <div style="font-size:11px; color: var(--text-muted); font-weight:700;">Remaining: <span id="rem-5">0</span></div>
                    </div>
                    <input id="count-5" type="number" min="0" step="1" value="0" class="goal-input" placeholder="Pieces" style="padding: 10px 12px;" />
                  </div>

                  <div class="withdraw-item" style="background: var(--card-bg-alt); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 14px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                      <div style="font-family: var(--font-mono); font-weight: 900; color: var(--coin10);">₱10</div>
                      <div style="font-size:11px; color: var(--text-muted); font-weight:700;">Remaining: <span id="rem-10">0</span></div>
                    </div>
                    <input id="count-10" type="number" min="0" step="1" value="0" class="goal-input" placeholder="Pieces" style="padding: 10px 12px;" />
                  </div>

                  <div class="withdraw-item" style="background: var(--card-bg-alt); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 14px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                      <div style="font-family: var(--font-mono); font-weight: 900; color: var(--coin20);">₱20</div>
                      <div style="font-size:11px; color: var(--text-muted); font-weight:700;">Remaining: <span id="rem-20">0</span></div>
                    </div>
                    <input id="count-20" type="number" min="0" step="1" value="0" class="goal-input" placeholder="Pieces" style="padding: 10px 12px;" />
                  </div>

                </div>

                <div style="width:100%; display:flex; gap: 8px; justify-content:center; flex-wrap:wrap; margin-top: 14px;">
                  <button type="button" class="btn btn-save" id="withdraw-submit">Withdraw</button>
                  <button type="button" class="btn btn-cancel" id="withdraw-cancel">Clear</button>
                </div>

                <div class="wrong-coin-message" id="withdraw-message" style="margin-top: 12px;"></div>
              </div>

            </div>
          </div>
        </section>

        <!-- Remaining breakdown -->
        <section class="card">
          <div class="section-title">
            <span>Remaining coins</span>
            <span class="title-pill">Inventory</span>
          </div>

          <div class="stats-list">
            <div class="stat-row" data-coin="1">
              <div class="stat-top"><span class="coin">₱1</span><span class="pct">0</span></div>
              <div class="bar-track"><div class="bar-fill" style="width:0%"></div></div>
            </div>
            <div class="stat-row" data-coin="5">
              <div class="stat-top"><span class="coin">₱5</span><span class="pct">0</span></div>
              <div class="bar-track"><div class="bar-fill" style="width:0%"></div></div>
            </div>
            <div class="stat-row" data-coin="10">
              <div class="stat-top"><span class="coin">₱10</span><span class="pct">0</span></div>
              <div class="bar-track"><div class="bar-fill" style="width:0%"></div></div>
            </div>
            <div class="stat-row" data-coin="20">
              <div class="stat-top"><span class="coin">₱20</span><span class="pct">0</span></div>
              <div class="bar-track"><div class="bar-fill" style="width:0%"></div></div>
            </div>
          </div>
        </section>

        <!-- Withdrawal history -->
        <section class="card">
          <div class="section-title">
            <span>Withdrawal History</span>
            <span class="title-pill" id="withdraw-history-count">0</span>
          </div>

          <div class="tx-list" id="withdraw-history">
            <div class="empty">No withdrawals yet</div>
          </div>
        </section>

        <!-- Back link -->
        <section class="card span-full history-footer-card">          <div class="back-link">
            <a href="index.php">&larr; Back to Dashboard</a>
          </div>
        </section>

      </div><!-- /bento-container -->

    </main>

  </div><!-- /app-layout -->

  <script src="assets/javascript/theme.js"></script>
  <script src="assets/javascript/withdraw.js"></script>
</body>
</html>
