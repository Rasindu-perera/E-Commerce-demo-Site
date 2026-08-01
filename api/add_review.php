<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to post a review.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $review_text = trim($_POST['review_text'] ?? '');
    $user_id = $_SESSION['user_id'];

    if ($product_id <= 0 || $rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Invalid product or rating.']);
        exit;
    }

    try {
        // Check if user already reviewed this product
        $check = $pdo->prepare("SELECT id FROM reviews WHERE product_id = ? AND user_id = ?");
        $check->execute([$product_id, $user_id]);
        if ($check->fetch()) {
            echo json_encode(['success' => false, 'message' => 'You have already reviewed this product.']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO reviews (product_id, user_id, rating, review_text) VALUES (?, ?, ?, ?)");
        $stmt->execute([$product_id, $user_id, $rating, $review_text]);
        
        echo json_encode(['success' => true, 'message' => 'Review submitted successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
