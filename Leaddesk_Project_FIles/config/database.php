<?php
/**
 * LeadDesk Mini - Database Configuration & Connection
 * PDO connection setup with error handling and dynamic table creation backup.
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'u306822835_mini_user');
define('DB_PASS', 'Soumya@#97780');
define('DB_NAME', 'u306822835_mini');
define('DB_PORT', 3306);

function getDBConnection() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            // First attempt to connect directly to the database
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            // Fallback: If DB does not exist, connect to server and create DB & tables
            try {
                $rootDsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4";
                $rootPdo = new PDO($rootDsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]);
                $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
                
                // Reconnect to newly created database
                $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);

                // Run structural setup
                initializeDatabaseSchema($pdo);

            } catch (PDOException $ex) {
                die("<div style='padding:20px; font-family:sans-serif; background:#fee2e2; color:#991b1b; border-radius:8px; margin:20px;'>
                    <h2>Database Connection Error</h2>
                    <p>Unable to connect to MySQL database server. Please ensure MySQL is running in XAMPP.</p>
                    <small>Error: " . htmlspecialchars($ex->getMessage()) . "</small>
                </div>");
            }
        }
    }
    return $pdo;
}

/**
 * Ensures schema structure exists if not initialized.
 */
function initializeDatabaseSchema($pdo) {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `admins` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `email` VARCHAR(255) NOT NULL UNIQUE,
          `password` VARCHAR(255) NOT NULL,
          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        CREATE TABLE IF NOT EXISTS `leads` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `name` VARCHAR(100) NOT NULL,
          `email` VARCHAR(255) NOT NULL,
          `budget` VARCHAR(100) NOT NULL,
          `message` TEXT NOT NULL,
          `status` ENUM('New', 'Contacted', 'Closed') DEFAULT 'New',
          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          INDEX `idx_status` (`status`),
          INDEX `idx_email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Insert admin if not exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE email = ?");
    $stmt->execute(['admin@leaddesk.com']);
    if ($stmt->fetchColumn() == 0) {
        $passHash = password_hash('admin123', PASSWORD_BCRYPT);
        $stmtIns = $pdo->prepare("INSERT INTO admins (email, password) VALUES (?, ?)");
        $stmtIns->execute(['admin@leaddesk.com', $passHash]);
    }
}
