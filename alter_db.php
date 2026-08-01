<?php
require_once 'config/db.php';
try {
    $pdo->exec("ALTER TABLE products ADD COLUMN discount_percentage INT DEFAULT 0 AFTER selling_price");
    echo "Column discount_percentage added.";
} catch (PDOException $e) {
    echo "Error (or column exists): " . $e->getMessage();
}
?>
