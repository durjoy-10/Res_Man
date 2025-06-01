<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$restaurant_id = $_GET['id'] ?? '';

if (empty($restaurant_id)) {
    echo json_encode(['success' => false, 'message' => 'Restaurant ID is required']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Delete related data
    $stmt = $pdo->prepare("DELETE FROM offers WHERE restaurant_id = ?");
    $stmt->execute([$restaurant_id]);

    $stmt = $pdo->prepare("SELECT id FROM menu_categories WHERE restaurant_id = ?");
    $stmt->execute([$restaurant_id]);
    $category_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($category_ids)) {
        $stmt = $pdo->prepare("DELETE FROM menu_items WHERE category_id IN (" . implode(',', array_fill(0, count($category_ids), '?')) . ")");
        $stmt->execute($category_ids);

        $stmt = $pdo->prepare("DELETE FROM menu_categories WHERE id IN (" . implode(',', array_fill(0, count($category_ids), '?')) . ")");
        $stmt->execute($category_ids);
    }

    $stmt = $pdo->prepare("DELETE FROM restaurants WHERE id = ?");
    $stmt->execute([$restaurant_id]);

    if ($stmt->rowCount() === 0) {
        throw new Exception("Restaurant not found");
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Restaurant deleted successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Error in admin_delete_restaurant.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>