<?php
header('Content-Type: application/json');
require_once 'db_connection.php';
require_once 'check_auth.php';

$restaurantId = $_GET['restaurant_id'] ?? null;

if (!$restaurantId) {
    echo json_encode(['success' => false, 'message' => 'Restaurant ID is required']);
    exit;
}

try {
    // Get total orders
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_orders FROM orders WHERE restaurant_id = ?");
    $stmt->execute([$restaurantId]);
    $totalOrders = $stmt->fetchColumn();

    // Get pending orders
    $stmt = $pdo->prepare("SELECT COUNT(*) as pending_orders FROM orders WHERE restaurant_id = ? AND status = 'pending'");
    $stmt->execute([$restaurantId]);
    $pendingOrders = $stmt->fetchColumn();

    // Get monthly revenue
    $currentMonth = date('Y-m');
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(total_amount), 0) as monthly_revenue 
        FROM orders 
        WHERE restaurant_id = ? 
        AND status = 'delivered' 
        AND DATE_FORMAT(order_date, '%Y-%m') = ?
    ");
    $stmt->execute([$restaurantId, $currentMonth]);
    $monthlyRevenue = $stmt->fetchColumn();

    // Get total menu items
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as menu_items 
        FROM menu_items mi
        JOIN menu_categories mc ON mi.category_id = mc.id
        WHERE mc.restaurant_id = ?
    ");
    $stmt->execute([$restaurantId]);
    $menuItems = $stmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'total_orders' => (int)$totalOrders,
        'pending_orders' => (int)$pendingOrders,
        'monthly_revenue' => number_format($monthlyRevenue, 2),
        'menu_items' => (int)$menuItems
    ]);

} catch (PDOException $e) {
    error_log("Database error in get_restaurant_stats.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to load statistics'
    ]);
}
?>