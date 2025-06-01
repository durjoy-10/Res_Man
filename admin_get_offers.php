<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

$restaurant_id = $_GET['restaurant_id'] ?? '';

if (empty($restaurant_id)) {
    echo json_encode(['success' => false, 'message' => 'Restaurant ID is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, description, valid_until FROM offers WHERE restaurant_id = ?");
    $stmt->execute([$restaurant_id]);
    $offers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $offers]);
} catch (Exception $e) {
    error_log("Error in admin_get_offers.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>