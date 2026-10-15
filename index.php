<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Alkansya — Smart Coin Vault & Savings Tracker</title>
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
        <a href="index.php" class="nav-item active">
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
        <a href="history_clear.php" class="nav-item">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
          <span>Clear History</span>
        </a>
      </nav>

      <div class="sidebar-footer">
        <div class="device-status-box">
          <div class="status-inline">
            <span class="dot" id="status-dot"></span>
            <span id="status-text">Checking device…</span>
          </div>
        </div>
        <div class="preferences-bar">
          <button class="pref-btn" id="sound-toggle" type="button" title="Toggle Sound">Sound</button>
          <button class="pref-btn" id="theme-toggle" type="button" title="Toggle Theme">Theme</button>
        </div>
      </div>
    </aside>

    <!-- Mobile Navigation Drawer / Overlay -->
    <div class="mobile-nav-overlay" id="mobile-nav-overlay">
      <div class="mobile-nav-drawer" id="mobile-nav-drawer">
        <div class="mobile-drawer-header">
          <div class="brand">
            <div class="brand-logo">AV</div>
            <div class="brand-info">
              <span class="brand-name">Alkansya</span>
              <span class="brand-tag">Smart Vault</span>
            </div>
          </div>
          <button class="icon-btn" id="mobile-menu-close" type="button" aria-label="Close Menu">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
          </button>
        </div>
        <nav class="mobile-drawer-links">
          <a href="index.php" class="nav-item active">
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
          <a href="history_clear.php" class="nav-item">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
            <span>Clear History</span>
          </a>
        </nav>
        <div class="mobile-drawer-footer">
          <div class="device-status-box">
            <div class="status-inline">
              <span class="dot" id="status-dot-mob"></span>
              <span id="status-text-mob">Alkansya Vault</span>
            </div>
          </div>
          <div class="preferences-bar">
            <button class="pref-btn" id="theme-toggle-drawer" type="button">Theme</button>
            <button class="pref-btn" id="sound-toggle-drawer" type="button">Sound</button>
          </div>
        </div>
      </div>
    </div>

    <!-- MAIN CONTENT AREA -->
    <main class="main-content">

      <!-- Mobile Topbar header -->
      <header class="mobile-topbar">
        <div class="brand">
          <div class="brand-logo">AV</div>
          <div class="brand-info">
            <span class="brand-name">Alkansya</span>
            <span class="brand-tag">Smart Vault</span>
          </div>
        </div>
        <div class="mobile-topbar-actions">
          <button class="icon-btn menu-toggle-btn" id="mobile-menu-toggle" type="button" aria-label="Open menu">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
          </button>
        </div>
      </header>

      <div class="bento-container">

        <!-- ── HERO SAVINGS CARD (Spans full) ── -->
        <section class="card span-full hero-card">
          <div class="savings-card">
            <div class="label-badge">Total Accumulated Savings</div>
            <div class="amount" id="total">₱0.00</div>
            <div class="subtext" id="coin-count-text">0 coins securely inserted</div>

            <!-- Wrong coin correction widget -->
            <div class="wrong-coin-section">
              <button class="btn btn-wrong" id="wrong-coin-btn" type="button">
                Wrong coin inserted?
              </button>

              <div class="wrong-coin-form" id="wrong-coin-form">
                <div class="wrong-coin-title">Correct Most Recent Insert</div>
                <div class="wrong-coin-hint" id="wrong-coin-last-insert">Most recent inserted: —</div>
                <div class="wrong-coin-hint">Select what you actually dropped into the vault:</div>
                <div class="wrong-coin-options" id="wrong-coin-options">
                  <button type="button" class="wrong-coin-option" data-coin="1">₱1</button>
                  <button type="button" class="wrong-coin-option" data-coin="5">₱5</button>
                  <button type="button" class="wrong-coin-option" data-coin="10">₱10</button>
                  <button type="button" class="wrong-coin-option" data-coin="20">₱20</button>
                </div>

                <div class="wrong-coin-actions">
                  <button class="btn btn-save" id="wrong-coin-confirm" type="button">Apply Correction</button>
                  <button class="btn btn-cancel" id="wrong-coin-cancel" type="button">Cancel</button>
                </div>
              </div>

              <div class="wrong-coin-message" id="wrong-coin-message"></div>

          </div>
        </section>

        <!-- ── COIN DENOMINATION BREAKDOWN (4 BENTO TILES) ── -->
        <div class="coin-tile" data-coin-tile="1">
          <div class="coin-top-row">
            <span class="coin-label">₱1 Coin</span>
            <span class="coin-icon-mini">PHP</span>
          </div>
          <div class="coin-count" id="count-1">0</div>
          <div class="coin-count-label">pieces counted</div>
        </div>

        <div class="coin-tile" data-coin-tile="5">
          <div class="coin-top-row">
            <span class="coin-label">₱5 Coin</span>
            <span class="coin-icon-mini">PHP</span>
          </div>
          <div class="coin-count" id="count-5">0</div>
          <div class="coin-count-label">pieces counted</div>
        </div>

        <div class="coin-tile" data-coin-tile="10">
          <div class="coin-top-row">
            <span class="coin-label">₱10 Coin</span>
            <span class="coin-icon-mini">PHP</span>
          </div>
          <div class="coin-count" id="count-10">0</div>
          <div class="coin-count-label">pieces counted</div>
        </div>

        <div class="coin-tile" data-coin-tile="20">
          <div class="coin-top-row">
            <span class="coin-label">₱20 Coin</span>
            <span class="coin-icon-mini">PHP</span>
          </div>
          <div class="coin-count" id="count-20">0</div>
          <div class="coin-count-label">pieces counted</div>
        </div>

        <!-- ── STATISTICS BREAKDOWN CARD ── -->
        <section class="card">
          <div class="section-title">
            <span>Denomination Share</span>
            <span class="title-pill">Analytics</span>
          </div>
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
        </section>

        <!-- ── RECENT TRANSACTIONS CARD ── -->
        <section class="card">
          <div class="section-title">
            <span>Recent Activity</span>
            <a href="history.php">All &rarr;</a>
          </div>
          <div class="tx-list" id="tx-container">
            <div class="empty">No transactions yet</div>
          </div>
        </section>

      </div><!-- /bento-container -->

    </main>

  </div><!-- /app-layout -->

  <script src="assets/javascript/theme.js"></script>
  <script src="assets/javascript/dashboard.js"></script>
</body>
</html>
