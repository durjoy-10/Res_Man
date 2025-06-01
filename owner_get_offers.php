<?php
// owner_get_offers.php
require_once 'owner_db_connection.php';

header('Content-Type: application/json');

$restaurant_id = filter_input(INPUT_GET, 'restaurant_id', FILTER_SANITIZE_NUMBER_INT);

if (!$restaurant_id) {
    echo json_encode(['success' => false, 'message' => 'Restaurant ID is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, description, valid_until FROM offers WHERE restaurant_id = ?");
    $stmt->execute([$restaurant_id]);
    $offers = $stmt->fetchAll();

    echo json_encode(['success' => true, 'data' => $offers]);
} catch (Exception $e) {
    error_log("Error in owner_get_offers.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>