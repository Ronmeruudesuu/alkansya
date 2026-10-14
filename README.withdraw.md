# Alkansya Withdraw (multi-denomination)

## What it adds
- A new page: `withdraw_page.php`
- A new API: `api/withdraw.php`
- A new front-end: `assets/javascript/withdraw.js`
- Navigation links to the page

## DB tables
See `sql/schema.sql` for the added tables:
- `coin_withdrawals` (header/history row per withdraw event)
- `coin_withdrawal_items` (one row per denomination inside the withdraw event)

## API endpoints
- `GET api/withdraw.php?action=state`
  - returns `{ remaining: { counts, total, total_coins }, withdrawals: [...] }`
- `POST api/withdraw.php?action=submit`
  - body: `{ items: [{ coin_value: 1|5|10|20, coin_count: N }, ...] }`

Withdrawals are validated against *remaining* inventory (inserted coins minus withdrawn coins).
