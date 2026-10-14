CREATE DATABASE IF NOT EXISTS alkansya;
USE alkansya;

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coin_value INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE device_status (
    id INT PRIMARY KEY DEFAULT 1,
    last_seen DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE savings_goals (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  target_amount decimal(10,2) NOT NULL,
  created_at datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Withdrawal header rows (one per withdraw action)
CREATE TABLE IF NOT EXISTS coin_withdrawals (
  id INT AUTO_INCREMENT PRIMARY KEY,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Withdrawal detail rows (multi-denomination support)
CREATE TABLE IF NOT EXISTS coin_withdrawal_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  withdrawal_id INT NOT NULL,
  coin_value INT NOT NULL,      -- 1, 5, 10, 20
  coin_count INT NOT NULL,     -- number of pieces
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

  INDEX idx_withdrawal_items_withdrawal_id (withdrawal_id),
  INDEX idx_withdrawal_items_coin_value (coin_value),

  CONSTRAINT fk_withdrawal_items_withdrawals
    FOREIGN KEY (withdrawal_id) REFERENCES coin_withdrawals(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
