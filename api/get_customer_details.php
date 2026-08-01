<?php
require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (isset($_GET['id'])) {
    $userId = (int)$_GET['id'];
    try {
        // Fetch User Details
        $stmtUser = $pdo->prepare("SELECT id, first_name, last_name, email, registration_date, status FROM users WHERE id = ?");
        $stmtUser->execute([$userId]);
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }

        // Fetch Order History
        $stmtOrders = $pdo->prepare("SELECT id, order_date, total_amount, status FROM orders WHERE user_id = ? ORDER BY id DESC LIMIT 50");
        $stmtOrders->execute([$userId]);
        $orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'user' => $user, 'orders' => $orders]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing customer ID']);
}
?>
