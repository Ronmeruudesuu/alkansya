# Graph Report - alkansya  (2026-10-16)

## Corpus Check
- Corpus is ~11,401 words - fits in a single context window. You may not need a graph.

## Summary
- 75 nodes · 89 edges · 23 communities (5 shown, 18 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 5 edges (avg confidence: 0.87)
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- Dashboard Interface
- API Data Fetching
- Withdrawal Logic
- Documentation & Schemas
- Savings Goals Management
- UI Design & Manual
- UI Polish & Bug Fixes

## God Nodes (most connected - your core abstractions)
1. `renderDashboard()` - 8 edges
2. `initWrongCoinCorrection()` - 6 edges
3. `renderGoals()` - 5 edges
4. `renderWithdrawalState()` - 5 edges
5. `submitWithdrawal()` - 5 edges
6. `formatPeso()` - 4 edges
7. `fetchDashboardData()` - 4 edges
8. `renderTotal()` - 4 edges
9. `Alkansya Smart Coin Counter & Savings Monitoring System` - 4 edges
10. `renderLastInsertedCoin()` - 3 edges

## Surprising Connections (you probably didn't know these)
- `Responsive Layout Separation` --references--> `Web Dashboard Features`  [INFERRED]
  CHANGELOG.md → README.md
- `Global Mobile Navigation Across All Pages` --references--> `Web Dashboard Features`  [INFERRED]
  CHANGELOG.md → README.md
- `Alkansya Tarpaulin Banner` --conceptually_related_to--> `Alkansya Smart Coin Counter & Savings Monitoring System`  [INFERRED]
  alkansya_tarpaulin.html → README.md

## Import Cycles
- None detected.

## Communities (23 total, 18 thin omitted)

### Community 0 - "Dashboard Interface"
Cohesion: 0.24
Nodes (14): fetchDashboardData(), formatPeso(), initWrongCoinCorrection(), clearSelection(), setSelectedCoin(), playPing(), renderBreakdown(), renderDashboard() (+6 more)

### Community 1 - "API Data Fetching"
Cohesion: 0.25
Nodes (11): getCoinCounts(), getDeviceStatus(), getRecentTransactions(), getRecentWithdrawals(), getTotalCoinCount(), getTotalSavings(), getTransactionCount(), getTransactionsPage() (+3 more)

### Community 2 - "Withdrawal Logic"
Cohesion: 0.30
Nodes (10): clearCounts(), fetchWithdrawalState(), formatPeso(), renderRemainingStatistics(), renderWithdrawalState(), renderWithdrawHistory(), showMessage(), submitBtnState() (+2 more)

### Community 3 - "Documentation & Schemas"
Cohesion: 0.29
Nodes (7): Alkansya Tarpaulin Banner, Global Mobile Navigation Across All Pages, Responsive Layout Separation, Database Schema Reference Guide, Hardware Interaction System Workflow, Alkansya Smart Coin Counter & Savings Monitoring System, Web Dashboard Features

### Community 4 - "Savings Goals Management"
Cohesion: 0.60
Nodes (5): escapeAttr(), escapeHtml(), fetchGoals(), formatPeso(), renderGoals()

## Knowledge Gaps
- **7 isolated node(s):** `Responsive Layout Separation`, `Global Mobile Navigation Across All Pages`, `UI Polish and Bug Fixes`, `Hardware Interaction System Workflow`, `Database Schema Reference Guide` (+2 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 29 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **18 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **What connects `Responsive Layout Separation`, `Global Mobile Navigation Across All Pages`, `UI Polish and Bug Fixes` to the rest of the system?**
  _7 weakly-connected nodes found - possible documentation gaps or missing edges._