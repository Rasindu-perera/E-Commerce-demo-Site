<?php
require_once 'config/db.php';
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN password VARCHAR(255) NULL AFTER email");
    echo "Column added.";
} catch (PDOException $e) {
    echo "Error (or column exists): " . $e->getMessage();
}
?>
