<?php
// 1. Database Connection Settings
$host = 'sql211.infinityfree.com';
$user = 'if0_42550509'; // Your db username
$pass = 'JLJ0Hue22Z';     // Your db password
$charset = 'utf8mb4';

try {
    // Connect to MySQL server (Create database if it doesn't exist)
    $pdo = new PDO("mysql:host=$host;charset=$charset", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Select Database
    $pdo->exec("USE if0_42550509_ecommerce_analytics");

    echo "Database created/selected successfully.<br>";

    // 2. Create Tables (SQL Schema)
    $sql = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50),
        last_name VARCHAR(50),
        email VARCHAR(100) UNIQUE,
        registration_date DATETIME,
        status ENUM('Active', 'Inactive') DEFAULT 'Active'
    );

    CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100),
        category VARCHAR(50),
        image_path VARCHAR(255) DEFAULT NULL,
        description TEXT,
        cost_price DECIMAL(10,2),
        selling_price DECIMAL(10,2),
        discount_percentage INT DEFAULT 0
    );

    CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        order_date DATETIME,
        total_amount DECIMAL(10,2) DEFAULT 0.00,
        status ENUM('Completed', 'Pending', 'Cancelled', 'Refunded') DEFAULT 'Completed',
        FOREIGN KEY (user_id) REFERENCES users(id)
    );

    CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT,
        product_id INT,
        quantity INT,
        unit_price DECIMAL(10,2),
        FOREIGN KEY (order_id) REFERENCES orders(id),
        FOREIGN KEY (product_id) REFERENCES products(id)
    );

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
    echo "Tables created successfully.<br>";

    // 3. Start Dummy Data Generation
    // Use a transaction for better performance
    $pdo->beginTransaction();

    // -- Create 50 Users --
    $firstNames = ['Kasun', 'Amila', 'Nimal', 'Sunil', 'Kamal', 'Ruwan', 'Saman', 'Nuwan', 'Dasun', 'Lahiru'];
    $lastNames = ['Perera', 'Silva', 'Fernando', 'De Silva', 'Kumara', 'Bandara', 'Rathnayake', 'Jayasooriya'];

    $stmtUser = $pdo->prepare("INSERT INTO users (first_name, last_name, email, registration_date) VALUES (?, ?, ?, ?)");
    for ($i = 1; $i <= 50; $i++) {
        $fname = $firstNames[array_rand($firstNames)];
        $lname = $lastNames[array_rand($lastNames)];
        $email = strtolower($fname . "." . $lname . $i . "@example.com");
        // Random date within the last 2 years
        $regDate = date('Y-m-d H:i:s', strtotime('-' . rand(1, 730) . ' days'));
        $stmtUser->execute([$fname, $lname, $email, $regDate]);
    }

    // -- Create 20 Products (Electronics Theme) --
    $categories = ['Smartphones', 'Laptops', 'Accessories', 'Audio'];
    $stmtProd = $pdo->prepare("INSERT INTO products (name, category, cost_price, selling_price) VALUES (?, ?, ?, ?)");

    for ($i = 1; $i <= 20; $i++) {
        $category = $categories[array_rand($categories)];
        $cost = rand(1000, 50000) / 10;
        $sell = $cost * rand(120, 150) / 100; // 20% - 50% Profit margin
        $stmtProd->execute(["Smart Product " . $i, $category, $cost, $sell]);
    }

    // -- Create 1000 Orders and Order Items --
    $stmtOrder = $pdo->prepare("INSERT INTO orders (user_id, order_date, status) VALUES (?, ?, ?)");
    $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
    $stmtUpdateOrder = $pdo->prepare("UPDATE orders SET total_amount = ? WHERE id = ?");

    $statuses = ['Completed', 'Completed', 'Completed', 'Pending', 'Cancelled']; // Higher chance for 'Completed'

    for ($i = 1; $i <= 1000; $i++) {
        $userId = rand(1, 50);
        $orderDate = date('Y-m-d H:i:s', strtotime('-' . rand(1, 365) . ' days')); // Within the last year
        $status = $statuses[array_rand($statuses)];

        $stmtOrder->execute([$userId, $orderDate, $status]);
        $orderId = $pdo->lastInsertId();

        $itemCount = rand(1, 4); // 1 to 4 products per order
        $orderTotal = 0;

        for ($j = 0; $j < $itemCount; $j++) {
            $productId = rand(1, 20);

            // Get product price
            $prodQ = $pdo->query("SELECT selling_price FROM products WHERE id = $productId");
            $price = $prodQ->fetchColumn();

            $qty = rand(1, 3);
            $orderTotal += ($price * $qty);

            $stmtItem->execute([$orderId, $productId, $qty, $price]);
        }

        // Update total order amount
        $stmtUpdateOrder->execute([$orderTotal, $orderId]);
    }

    $pdo->commit();
    echo "Successfully generated 50 Users, 20 Products, 1000 Orders, and related Order Items!";

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Database Error: " . $e->getMessage());
}
?>
