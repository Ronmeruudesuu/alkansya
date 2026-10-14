<?php
// history.php
//
// Full transaction log, paginated and filterable by coin.
// Reuses the same connection + functions as every other page.

require_once "config/database.php";
require_once "includes/functions.php";

$perPage = 20;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

$coinFilter = intval($_GET['coin'] ?? 0);
if (!isValidCoin($coinFilter)) {
    $coinFilter = 0; // 0 means "no filter, show everything"
}

$totalRows = getTransactionCount($conn, $coinFilter);
$totalPages = max(1, (int) ceil($totalRows / $perPage));

$transactions = getTransactionsPage($conn, $perPage, $offset, $coinFilter);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transaction History — Alkansya</title>
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
        <a href="history.php" class="nav-item active">
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
          <a href="goals_page.php" class="icon-btn">Goals</a>
          <a href="withdraw_page.php" class="icon-btn">Withdraw</a>
          <a href="history_clear.php" class="icon-btn">Reset</a>
          <button class="icon-btn" id="theme-toggle-mob" type="button">Theme</button>
        </div>
      </header>

      <div class="bento-container">

        <section class="card span-full">
          <div class="section-title">
            <span>Complete Transaction History</span>
            <span class="title-pill"><?= number_format($totalRows) ?> total</span>
          </div>

          <!-- Filters -->
          <div class="filters">
            <a href="history.php" class="<?= $coinFilter === 0 ? 'active' : '' ?>">All Coins</a>
            <?php foreach ([1, 5, 10, 20] as $c): ?>
              <a href="history.php?coin=<?= $c ?>" class="<?= $coinFilter === $c ? 'active' : '' ?>">₱<?= $c ?></a>
            <?php endforeach; ?>
          </div>

          <!-- Transaction items -->
          <div class="tx-list" style="max-height: none;">
            <?php if (empty($transactions)): ?>
              <div class="empty">No transactions found for this filter.</div>
            <?php else: ?>
              <?php foreach ($transactions as $tx): ?>
                <div class="tx-row">
                  <span class="tx-coin">₱<?= $tx['coin_value'] ?></span>
                  <span class="tx-time"><?= $tx['created_at'] ?></span>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </section>

        <!-- Pagination and Back Link in a single clean footer card -->
        <section class="card span-full history-footer-card">
          <?php if ($totalPages > 1): ?>
            <div class="pagination">
              <?php $coinParam = $coinFilter ? "&coin=$coinFilter" : ""; ?>
              <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <?php if ($p === $page): ?>
                  <span class="current"><?= $p ?></span>
                <?php else: ?>
                  <a href="history.php?page=<?= $p ?><?= $coinParam ?>"><?= $p ?></a>
                <?php endif; ?>
              <?php endfor; ?>
            </div>
          <?php endif; ?>

          <div class="back-link">
            <a href="index.php">&larr; Back to Dashboard</a>
          </div>
        </section>

      </div><!-- /bento-container -->

    </main>

  </div><!-- /app-layout -->

  <script src="assets/javascript/theme.js"></script>
</body>
</html>
