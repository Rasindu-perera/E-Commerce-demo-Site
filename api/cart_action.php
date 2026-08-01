<?php
session_start();
header('Content-Type: application/json');

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['action'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

if (!isset($_SESSION['user_id']) && in_array($data['action'], ['add', 'update', 'remove', 'clear'])) {
    echo json_encode(['success' => false, 'require_login' => true, 'message' => 'Please log in to manage your cart']);
    exit;
}

$action = $data['action'];
$productId = isset($data['product_id']) ? (int)$data['product_id'] : 0;
$quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;
$price = isset($data['price']) ? (float)$data['price'] : 0.0;

switch ($action) {
    case 'add':
        if ($productId > 0) {
            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$productId] = [
                    'quantity' => $quantity,
                    'price' => $price
                ];
            }
        }
        break;
        
    case 'update':
        if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
            if ($quantity > 0) {
                $_SESSION['cart'][$productId]['quantity'] = $quantity;
            } else {
                unset($_SESSION['cart'][$productId]);
            }
        }
        break;
        
    case 'remove':
        if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }
        break;
        
    case 'clear':
        $_SESSION['cart'] = [];
        break;
        
    case 'get':
        // Just return the cart state
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
        exit;
}

// Calculate totals
$totalItems = 0;
$totalPrice = 0.0;

foreach ($_SESSION['cart'] as $id => $item) {
    $totalItems += $item['quantity'];
    $totalPrice += ($item['price'] * $item['quantity']);
}

echo json_encode([
    'success' => true,
    'cart' => $_SESSION['cart'],
    'totalItems' => $totalItems,
    'totalPrice' => number_format($totalPrice, 2, '.', '')
]);
?>
