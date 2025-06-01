<?php
// owner_get_restaurant.php
require_once 'owner_db_connection.php';

header('Content-Type: application/json');

$restaurant_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (!$restaurant_id) {
    echo json_encode(['success' => false, 'message' => 'Restaurant ID is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, name, description, owner_name, owner_email, phone, address, image_path FROM restaurants WHERE id = ?");
    $stmt->execute([$restaurant_id]);
    $restaurant = $stmt->fetch();

    if ($restaurant) {
        echo json_encode(['success' => true, 'data' => $restaurant]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Restaurant not found']);
    }
} catch (Exception $e) {
    error_log("Error in owner_get_restaurant.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>