<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

$restaurant_id = $_GET['id'] ?? '';

try {
    if ($restaurant_id) {
        $stmt = $pdo->prepare("SELECT id, name, description, owner_name, owner_email, phone, address, image_path FROM restaurants WHERE id = ?");
        $stmt->execute([$restaurant_id]);
        $restaurant = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $restaurant ?: []]);
    } else {
        $stmt = $pdo->prepare("SELECT id, name, description, owner_name, owner_email, phone, address, image_path FROM restaurants");
        $stmt->execute();
        $restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $restaurants]);
    }
} catch (Exception $e) {
    error_log("Error in admin_get_restaurant.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>