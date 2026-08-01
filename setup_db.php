<?php
// 1. Database Connection Settings
$host = 'sql211.infinityfree.com';
$user = 'if0_42550509'; // ඔයාගේ db username එක
$pass = 'JLJ0Hue22Z';     // ඔයාගේ db password එක
$charset = 'utf8mb4';

try {
    // MySQL server එකට connect වීම (Database එක නැත්නම් අලුතින් හදන්න)
    $pdo = new PDO("mysql:host=$host;charset=$charset", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Database එක හැදීම
    $pdo->exec("CREATE DATABASE IF NOT EXISTS ecommerce_analytics");
    $pdo->exec("USE ecommerce_analytics");

    echo "Database created/selected successfully.<br>";

    // 2. Tables නිර්මාණය කිරීම (SQL Schema)
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
        cost_price DECIMAL(10,2),
        selling_price DECIMAL(10,2)
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
    ";

    $pdo->exec($sql);
    echo "Tables created successfully.<br>";

    // 3. Dummy Data Generation ආරම්භය
    // කාර්යක්ෂමතාව වැඩි කරන්න Transaction එකක් පාවිච්චි කරනවා
    $pdo->beginTransaction();

    // -- Users 50ක් හැදීම --
    $firstNames = ['Kasun', 'Amila', 'Nimal', 'Sunil', 'Kamal', 'Ruwan', 'Saman', 'Nuwan', 'Dasun', 'Lahiru'];
    $lastNames = ['Perera', 'Silva', 'Fernando', 'De Silva', 'Kumara', 'Bandara', 'Rathnayake', 'Jayasooriya'];

    $stmtUser = $pdo->prepare("INSERT INTO users (first_name, last_name, email, registration_date) VALUES (?, ?, ?, ?)");
    for ($i = 1; $i <= 50; $i++) {
        $fname = $firstNames[array_rand($firstNames)];
        $lname = $lastNames[array_rand($lastNames)];
        $email = strtolower($fname . "." . $lname . $i . "@example.com");
        // පහුගිය අවුරුදු 2 ඇතුළත random date එකක්
        $regDate = date('Y-m-d H:i:s', strtotime('-' . rand(1, 730) . ' days'));
        $stmtUser->execute([$fname, $lname, $email, $regDate]);
    }

    // -- Products 20ක් හැදීම (Electronics Theme) --
    $categories = ['Smartphones', 'Laptops', 'Accessories', 'Audio'];
    $stmtProd = $pdo->prepare("INSERT INTO products (name, category, cost_price, selling_price) VALUES (?, ?, ?, ?)");

    for ($i = 1; $i <= 20; $i++) {
        $category = $categories[array_rand($categories)];
        $cost = rand(1000, 50000) / 10;
        $sell = $cost * rand(120, 150) / 100; // 20% - 50% Profit margin
        $stmtProd->execute(["Smart Product " . $i, $category, $cost, $sell]);
    }

    // -- Orders 1000ක් සහ Order Items හැදීම --
    $stmtOrder = $pdo->prepare("INSERT INTO orders (user_id, order_date, status) VALUES (?, ?, ?)");
    $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
    $stmtUpdateOrder = $pdo->prepare("UPDATE orders SET total_amount = ? WHERE id = ?");

    $statuses = ['Completed', 'Completed', 'Completed', 'Pending', 'Cancelled']; // Completed වෙන chance එක වැඩියි

    for ($i = 1; $i <= 1000; $i++) {
        $userId = rand(1, 50);
        $orderDate = date('Y-m-d H:i:s', strtotime('-' . rand(1, 365) . ' days')); // පහුගිය අවුරුද්ද ඇතුළත
        $status = $statuses[array_rand($statuses)];

        $stmtOrder->execute([$userId, $orderDate, $status]);
        $orderId = $pdo->lastInsertId();

        $itemCount = rand(1, 4); // එක order එකක products 1 ත් 4 ත් අතර
        $orderTotal = 0;

        for ($j = 0; $j < $itemCount; $j++) {
            $productId = rand(1, 20);

            // Product එකේ price එක ගන්නවා
            $prodQ = $pdo->query("SELECT selling_price FROM products WHERE id = $productId");
            $price = $prodQ->fetchColumn();

            $qty = rand(1, 3);
            $orderTotal += ($price * $qty);

            $stmtItem->execute([$orderId, $productId, $qty, $price]);
        }

        // Order එකේ සම්පූර්ණ ගාණ update කරනවා
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