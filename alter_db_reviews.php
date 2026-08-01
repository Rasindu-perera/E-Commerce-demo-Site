<?php
require_once 'config/db.php';

try {
    // 1. Add description to products
    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN description TEXT AFTER category");
        echo "Column 'description' added to products.<br>";
    } catch (PDOException $e) {
        // Might already exist
        echo "Note: 'description' column might already exist.<br>";
    }

    // 2. Create reviews table
    $sql = "
    CREATE TABLE IF NOT EXISTS reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT,
        user_id INT,
        rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
        review_text TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );
    ";
    
    $pdo->exec($sql);
    echo "Table 'reviews' created successfully.<br>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
