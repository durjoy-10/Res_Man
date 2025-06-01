<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$user_id = $_GET['id'] ?? '';

if (empty($user_id)) {
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Delete related data (e.g., orders, order items)
    $stmt = $pdo->prepare("SELECT id FROM orders WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $order_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($order_ids)) {
        $stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id IN (" . implode(',', array_fill(0, count($order_ids), '?')) . ")");
        $stmt->execute($order_ids);
    }

    $stmt = $pdo->prepare("DELETE FROM orders WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // Delete user
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);

    if ($stmt->rowCount() === 0) {
        throw new Exception("User not found");
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Error in admin_delete_user.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>