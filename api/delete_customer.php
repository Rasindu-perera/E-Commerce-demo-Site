<?php
require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = (int)($data['user_id'] ?? 0);
    
    if ($userId > 0) {
        try {
            // Soft delete: set status to Inactive instead of deleting, to preserve foreign keys in orders
            $stmt = $pdo->prepare("UPDATE users SET status = 'Inactive' WHERE id = ?");
            if ($stmt->execute([$userId])) {
                echo json_encode(['success' => true, 'message' => 'Customer deactivated']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database update failed']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
