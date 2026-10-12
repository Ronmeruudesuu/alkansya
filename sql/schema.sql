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
