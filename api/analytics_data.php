<?php
require_once '../config/db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
ini_set('display_errors', 0);
error_reporting(0);

try {
    $data = [];

    // 1) Total Sales and Total Orders for the current month
    // We only consider 'Completed' orders for revenue and successful sales
    $stmt1 = $pdo->query("
        SELECT 
            COUNT(id) as total_orders,
            COALESCE(SUM(total_amount), 0) as total_sales
        FROM orders 
        WHERE MONTH(order_date) = MONTH(CURRENT_DATE()) 
        AND YEAR(order_date) = YEAR(CURRENT_DATE())
        AND status = 'Completed'
    ");
    $data['current_month_summary'] = $stmt1->fetch(PDO::FETCH_ASSOC);

    // 2) Monthly revenue trend for the last 12 months
    $stmt2 = $pdo->query("
        SELECT 
            DATE_FORMAT(order_date, '%Y-%m') as month,
            COALESCE(SUM(total_amount), 0) as revenue
        FROM orders
        WHERE order_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 12 MONTH)
        AND status = 'Completed'
        GROUP BY DATE_FORMAT(order_date, '%Y-%m')
        ORDER BY month ASC
    ");
    $data['monthly_revenue_trend'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // 2.5) Daily revenue trend for the last 30 days
    $stmt2_5 = $pdo->query("
        SELECT 
            DATE_FORMAT(order_date, '%Y-%m-%d') as date,
            COALESCE(SUM(total_amount), 0) as revenue
        FROM orders
        WHERE order_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY)
        AND status = 'Completed'
        GROUP BY DATE_FORMAT(order_date, '%Y-%m-%d')
        ORDER BY date ASC
    ");
    $data['daily_revenue_trend'] = $stmt2_5->fetchAll(PDO::FETCH_ASSOC);

    // 3) Top 5 best-selling products by volume
    $stmt3 = $pdo->query("
        SELECT 
            p.id, 
            p.name, 
            p.category, 
            SUM(oi.quantity) as total_sold
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        JOIN orders o ON oi.order_id = o.id
        WHERE o.status = 'Completed'
        GROUP BY p.id, p.name, p.category
        ORDER BY total_sold DESC
        LIMIT 5
    ");
    $data['top_products'] = $stmt3->fetchAll(PDO::FETCH_ASSOC);

    // 4) Top 5 highest spending customers
    $stmt4 = $pdo->query("
        SELECT 
            u.id, 
            u.first_name, 
            u.last_name, 
            u.email,
            SUM(o.total_amount) as total_spent
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.status = 'Completed'
        GROUP BY u.id, u.first_name, u.last_name, u.email
        ORDER BY total_spent DESC
        LIMIT 5
    ");
    $data['top_customers'] = $stmt4->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $data
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
