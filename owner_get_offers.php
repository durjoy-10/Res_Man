<?php
header('Content-Type: application/json');

// Database connection
$conn = new mysqli("localhost", "root", "", "food_delivery");
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit();
}

// Get restaurant ID from query parameter
$restaurant_id = filter_input(INPUT_GET, 'restaurant_id', FILTER_SANITIZE_NUMBER_INT);
if ($restaurant_id <= 0) {
    error_log("Invalid restaurant ID: $restaurant_id");
    echo json_encode(["success" => false, "message" => "Invalid restaurant ID"]);
    $conn->close();
    exit();
}

// Fetch offers
$stmt = $conn->prepare("SELECT id, description, valid_until FROM offers WHERE restaurant_id = ?");
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    echo json_encode(["success" => false, "message" => "Database query preparation failed"]);
    $conn->close();
    exit();
}
$stmt->bind_param("i", $restaurant_id);
$stmt->execute();
$result = $stmt->get_result();

$offers = [];
while ($offer = $result->fetch_assoc()) {
    $offers[] = [
        "id" => $offer['id'],
        "description" => $offer['description'] ?? '',
        "valid_until" => $offer['valid_until'] ?? ''
    ];
}

echo json_encode(["success" => true, "data" => $offers]);

$stmt->close();
$conn->close();
?>