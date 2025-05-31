<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

$restaurantId = $_GET['restaurant_id'] ?? null;

if (!$restaurantId) {
    echo json_encode(['success' => false, 'message' => 'Restaurant ID is required']);
    exit;
}

try {
    // Today's orders
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM orders 
        WHERE restaurant_id = ? 
        AND DATE(order_date) = CURDATE()
    ");
    $stmt->execute([$restaurantId]);
    $todayOrders = $stmt->fetchColumn();

    // Pending orders
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM orders 
        WHERE restaurant_id = ? 
        AND status = 'pending'
    ");
    $stmt->execute([$restaurantId]);
    $pendingOrders = $stmt->fetchColumn();

    // Monthly revenue
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(total_amount), 0) as total 
        FROM orders 
        WHERE restaurant_id = ? 
        AND MONTH(order_date) = MONTH(CURDATE()) 
        AND YEAR(order_date) = YEAR(CURDATE())
        AND status = 'delivered'
    ");
    $stmt->execute([$restaurantId]);
    $monthlyRevenue = $stmt->fetchColumn();

    // Menu items count
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM menu_items mi
        JOIN menu_categories mc ON mi.category_id = mc.id
        WHERE mc.restaurant_id = ?
    ");
    $stmt->execute([$restaurantId]);
    $menuItemsCount = $stmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'today_orders' => $todayOrders,
        'pending_orders' => $pendingOrders,
        'monthly_revenue' => $monthlyRevenue,
        'menu_items' => $menuItemsCount
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>