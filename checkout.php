<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    die("<div style='font-family:sans-serif; text-align:center; margin-top:50px;'><h2>Your cart is empty.</h2><a href='index.php'>Go back to shop</a></div>");
}

try {
    // 1. Calculate total amount
    $totalAmount = 0.0;
    foreach ($_SESSION['cart'] as $productId => $item) {
        $totalAmount += ($item['price'] * $item['quantity']);
    }

    // 2. Start transaction
    $pdo->beginTransaction();

    // 3. Insert into orders table (Using user_id = 1 as a placeholder since there is no auth)
    $userId = 1; 
    $orderDate = date('Y-m-d H:i:s');
    $status = 'Pending';
    
    $stmtOrder = $pdo->prepare("INSERT INTO orders (user_id, order_date, total_amount, status) VALUES (?, ?, ?, ?)");
    $stmtOrder->execute([$userId, $orderDate, $totalAmount, $status]);
    
    // Get the generated order ID
    $orderId = $pdo->lastInsertId();
    
    // 4. Insert into order_items table
    $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
    
    foreach ($_SESSION['cart'] as $productId => $item) {
        $stmtItem->execute([$orderId, $productId, $item['quantity'], $item['price']]);
    }
    
    // 5. Commit transaction
    $pdo->commit();
    
    // 6. Clear the cart
    $_SESSION['cart'] = [];
    
    echo "<div style='font-family:sans-serif; text-align:center; margin-top:50px;'>";
    echo "<h1 style='color: #4f46e5;'>Order Placed Successfully! 🎉</h1>";
    echo "<p>Your Order ID is: <strong>#" . $orderId . "</strong></p>";
    echo "<p>Total Amount: <strong>$" . number_format($totalAmount, 2) . "</strong></p>";
    echo "<br><a href='index.php' style='background: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Continue Shopping</a>";
    echo "</div>";
    
} catch (PDOException $e) {
    // Rollback on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("<div style='font-family:sans-serif; color: red; text-align:center; margin-top:50px;'><h3>Checkout failed: " . htmlspecialchars($e->getMessage()) . "</h3></div>");
}
?>
