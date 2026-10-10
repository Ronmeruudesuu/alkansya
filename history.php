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

<div class="container">

  <div class="topbar">
    <button class="icon-btn" id="theme-toggle">🌓 Theme</button>
  </div>

  <div class="card">
    <div class="header"><h1>Transaction History</h1></div>

    <div class="filters">
      <a href="history.php" class="<?= $coinFilter === 0 ? 'active' : '' ?>">All</a>
      <?php foreach ([1, 5, 10, 20] as $c): ?>
        <a href="history.php?coin=<?= $c ?>" class="<?= $coinFilter === $c ? 'active' : '' ?>">₱<?= $c ?></a>
      <?php endforeach; ?>
    </div>

    <div class="tx-list">
      <?php if (empty($transactions)): ?>
        <div class="empty">No transactions found</div>
      <?php else: ?>
        <?php foreach ($transactions as $tx): ?>
          <div class="tx-row">
            <span class="tx-coin">₱<?= $tx['coin_value'] ?></span>
            <span class="tx-time"><?= $tx['created_at'] ?></span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

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

</div>

<script>
  // Just the theme toggle — this page doesn't poll for live data,
  // so it doesn't need the rest of dashboard.js.
  const root = document.documentElement;
  root.setAttribute("data-theme", localStorage.getItem("alkansya-theme") || "dark");

  document.getElementById("theme-toggle").addEventListener("click", () => {
    const next = root.getAttribute("data-theme") === "dark" ? "light" : "dark";
    root.setAttribute("data-theme", next);
    localStorage.setItem("alkansya-theme", next);
  });
</script>
</body>
</html>
