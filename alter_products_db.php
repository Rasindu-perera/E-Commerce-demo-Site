<?php
require_once 'config/db.php';

try {
    // Add image_path column if it doesn't exist
    $pdo->exec("ALTER TABLE products ADD COLUMN image_path VARCHAR(255) DEFAULT NULL AFTER category");
    echo "Added image_path column to products table.<br>";
} catch (PDOException $e) {
    echo "Column might already exist or error: " . $e->getMessage() . "<br>";
}
?>
