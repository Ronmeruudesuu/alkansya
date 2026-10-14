# Alkansya Smart Coin Counter & Savings Monitoring System

Alkansya is an IoT-based smart piggy bank that connects physical coin saving with a local web dashboard. Instead of opening your piggy bank and counting coins by hand, every deposit you make is automatically tracked, logged, and displayed on your screen in real time.

---

## What the Project Does

Traditional piggy banks make saving easy, but they don't give you any visibility into how much you've saved unless you break open or empty the container. Alkansya solves this by combining physical hardware with a local web application:

1. **Physical Coin Vault:** You press a button for the coin value you are dropping in, wait for the OLED screen prompt, and drop the coin through an infrared sensor slot.
2. **Instant Sync:** The microcontroller communicates directly with your laptop via Wi-Fi, recording each deposit into a database.
3. **Web Dashboard:** A clean bento-grid interface shows your total accumulated savings, coin counts per denomination, percentage breakdown, and recent transaction history.

---

## How It Works (System Workflow)

### 1. Hardware Interaction
The coin counter runs on an ESP32 microcontroller connected to:
- **Four push buttons** for selecting coin values: ₱1, ₱5, ₱10, and ₱20.
- **An SSD1306 OLED display** that prompts you to insert your coin.
- **An infrared (IR) obstacle sensor** that detects when the coin physically drops past the slot.

When you want to deposit a coin:
1. Press the button matching the coin's value.
2. The OLED screen displays an `INSERT PX` prompt (you have a 10-second window).
3. Drop the coin into the vault. The IR sensor detects the drop and signals the ESP32.
4. The ESP32 sends an HTTP request to your local PHP server, logging the transaction into MySQL.
5. The OLED confirms the deposit with `RECEIVED!`, and your browser dashboard updates immediately.

### 2. Wi-Fi & Network Setup
To keep things local and independent of your home router, the ESP32 creates its own wireless network named **`Alkansya-CoinCounter`**. Connect your laptop or phone to this Wi-Fi network so the device can send deposit data directly to your local XAMPP server (hosted at `192.168.4.2`).

### 3. Web Dashboard & Features
The web interface runs locally via XAMPP (Apache and MySQL/MariaDB) and includes several key pages:
- **Dashboard (`index.php`):** Shows your total savings, coin piece counts, denomination share bars, and recent activity logs.
- **Wrong Coin Correction Tool:** If you accidentally press the wrong button during a deposit, you can use the dashboard correction tool to fix your most recent entry.
- **Savings Goals (`goals_page.php`):** Add, edit, and track your Pag-iipunan savings targets.
- **Transaction History (`history.php`):** Review complete transaction logs with denomination filters and pagination.
- **Withdrawal System (`withdraw_page.php`):** Manage and log coin withdrawals against your current inventory.
- **Preferences:** Toggle sound effects or switch between dark and light themes.

---

## Hardware Wiring Reference

The project is built using parts from standard ESP32 starter kits (such as the LAFVIN ESP32 kit):

| Component | Pin / Terminal | ESP32 Connection |
| :--- | :--- | :--- |
| **OLED Display (SSD1306)** | SDA | D21 |
| **OLED Display (SSD1306)** | SCL | D22 |
| **IR Obstacle Sensor** | OUT | D18 |
| **₱1 Button** | Signal | D13 |
| **₱5 Button** | Signal | D12 |
| **₱10 Button** | Signal | D14 |
| **₱20 Button** | Signal | D27 |
| **All Buttons** | Other Terminal | GND |

*Note: All buttons use internal pull-up resistors (`INPUT_PULLUP`), so external resistors are not required.*

---

## Database Setup & Schema Reference Guide

The system uses a MySQL / MariaDB database named `alkansya` managed through XAMPP. Below is the complete database schema and setup guide.

### 1. Database Schema (`sql/schema.sql`)

- **`transactions`**: Records every individual coin deposit.
  ```sql
  CREATE TABLE transactions (
      id INT(11) NOT NULL AUTO_INCREMENT,
      coin_value INT(11) NOT NULL,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (id)
  );
  ```

- **`device_status`**: Tracks the ESP32 device online/offline heartbeat status.
  ```sql
  CREATE TABLE device_status (
      id INT(11) NOT NULL DEFAULT 1,
      last_seen DATETIME DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (id)
  );
  ```

- **`savings_goals`**: Stores Pag-iipunan savings targets.
  ```sql
  CREATE TABLE savings_goals (
      id INT(11) NOT NULL AUTO_INCREMENT,
      name VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
      target_amount DECIMAL(10,2) NOT NULL,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (id)
  );
  ```

- **`coin_withdrawals`**: Header records for withdrawal events.
  ```sql
  CREATE TABLE coin_withdrawals (
      id INT(11) NOT NULL AUTO_INCREMENT,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (id)
  );
  ```

- **`coin_withdrawal_items`**: Line items for multi-denomination withdrawals linked to `coin_withdrawals`.
  ```sql
  CREATE TABLE coin_withdrawal_items (
      id INT(11) NOT NULL AUTO_INCREMENT,
      withdrawal_id INT(11) NOT NULL,
      coin_value INT(11) NOT NULL,
      coin_count INT(11) NOT NULL,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (id),
      INDEX (withdrawal_id),
      INDEX (coin_value),
      CONSTRAINT fk_withdrawal_items_withdrawals
          FOREIGN KEY (withdrawal_id) REFERENCES coin_withdrawals(id)
          ON DELETE CASCADE
  );
  ```

### 2. Step-by-Step Database Setup Guide
1. Open XAMPP Control Panel and start **Apache** and **MySQL**.
2. Open your browser and go to `http://localhost/phpmyadmin`.
3. Create a new database named `alkansya` (or run `CREATE DATABASE IF NOT EXISTS alkansya;`).
4. Select the `alkansya` database, go to the **SQL** tab, paste the complete schema script from `sql/schema.sql`, and click **Go**.
5. Verify that all five tables (`transactions`, `device_status`, `savings_goals`, `coin_withdrawals`, and `coin_withdrawal_items`) are successfully created.

---

## Getting Started & Local Setup

1. **Install XAMPP:** Ensure Apache and MySQL/MariaDB are running.
2. **Database Setup:** Follow the database guide above to initialize your `alkansya` database.
3. **Firmware Upload:** Flash the ESP32 code using the Arduino IDE, making sure your Wi-Fi AP settings and server endpoint match your local IP (`http://192.168.4.2/alkansya/api/save_coin.php`).
4. **Run the App:** Connect your PC to the `Alkansya-CoinCounter` Wi-Fi network, open your browser, and navigate to your local project folder to start tracking your savings!

---

## Developers

- **Rommel C. Daganio Jr.**
- **Edrian Jay Bautista**
- **Course / Section:** BSIT 3 Sec 2
- **Adviser:** Jay Paul T. Badenas
- **Date:** July 2026
