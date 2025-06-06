<?php
require_once 'owner_db_connection.php';

header('Content-Type: application/json');

$restaurant_id = filter_input(INPUT_GET, 'restaurant_id', FILTER_VALIDATE_INT);

if (!$restaurant_id || $restaurant_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid restaurant ID']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, description, valid_until FROM offers WHERE restaurant_id = ?");
    $stmt->execute([$restaurant_id]);
    $offers = $stmt->fetchAll();

    echo json_encode(['success' => true, 'data' => $offers]);
} catch (Exception $e) {
    error_log("Error in owner_get_offers.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to load offers']);
}
?>