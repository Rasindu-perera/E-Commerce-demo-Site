<?php
require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = $_POST['name'] ?? '';
    $category = $_POST['category'] ?? '';
    $cost_price = (float)($_POST['cost_price'] ?? 0);
    $selling_price = (float)($_POST['selling_price'] ?? 0);
    $discount_percentage = (int)($_POST['discount_percentage'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $existing_image = $_POST['existing_image'] ?? '';

    if (empty($name) || empty($category) || $cost_price <= 0 || $selling_price <= 0 || $discount_percentage < 0 || $discount_percentage > 100) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields correctly.']);
        exit;
    }

    $imagePath = $existing_image; // Default to existing if not updated

    // Handle File Upload
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../public/images/';
        
        // Ensure dir exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid() . '_' . basename($_FILES['product_image']['name']);
        $targetFile = $uploadDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        $validExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (in_array($imageFileType, $validExtensions)) {
            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetFile)) {
                $imagePath = 'public/images/' . $fileName;
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file.']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid image format.']);
            exit;
        }
    }

    try {
        if ($id > 0) {
            // Update existing product
            $stmt = $pdo->prepare("UPDATE products SET name = ?, category = ?, description = ?, image_path = ?, cost_price = ?, selling_price = ?, discount_percentage = ? WHERE id = ?");
            $stmt->execute([$name, $category, $description, $imagePath, $cost_price, $selling_price, $discount_percentage, $id]);
            echo json_encode(['success' => true, 'message' => 'Product updated successfully.']);
        } else {
            // Add new product
            $stmt = $pdo->prepare("INSERT INTO products (name, category, description, image_path, cost_price, selling_price, discount_percentage) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $category, $description, $imagePath, $cost_price, $selling_price, $discount_percentage]);
            echo json_encode(['success' => true, 'message' => 'Product added successfully.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
