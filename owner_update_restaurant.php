<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Log errors to a file
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

require_once 'db_connect.php';

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    // Retrieve restaurant_id from the form
    $restaurant_id = filter_input(INPUT_POST, 'restaurant_id', FILTER_SANITIZE_NUMBER_INT);
    if (!$restaurant_id || !is_numeric($restaurant_id)) {
        throw new Exception('Invalid or missing restaurant ID');
    }

    // Retrieve other form fields
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $owner_name = filter_input(INPUT_POST, 'owner_name', FILTER_SANITIZE_STRING);
    $owner_email = filter_input(INPUT_POST, 'owner_email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);

    if (empty($name) || empty($owner_name) || empty($owner_email) || empty($phone) || empty($address)) {
        throw new Exception('Required fields are missing');
    }

    // Handle image upload if provided
    $image_path = null;
    if (isset($_FILES['restaurant_image']) && $_FILES['restaurant_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/restaurants/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $image_name = time() . '_' . basename($_FILES['restaurant_image']['name']);
        $image_path = $upload_dir . $image_name;
        if (!move_uploaded_file($_FILES['restaurant_image']['tmp_name'], $image_path)) {
            throw new Exception('Failed to upload image');
        }
    }

    // Update restaurant data
    $query = "UPDATE restaurants SET name = ?, description = ?, owner_name = ?, owner_email = ?, phone = ?, address = ?" . ($image_path ? ", image_path = ?" : "") . " WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }

    $params = [$name, $description, $owner_name, $owner_email, $phone, $address];
    $types = "ssssss";
    if ($image_path) {
        $params[] = $image_path;
        $types .= "s";
    }
    $params[] = $restaurant_id;
    $types .= "i";

    $stmt->bind_param($types, ...$params);
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }

    $response['success'] = true;
    $response['message'] = 'Restaurant updated successfully';

    $stmt->close();
} catch (Exception $e) {
    $response['message'] = 'Error updating restaurant: ' . $e->getMessage();
    error_log('Error in owner_update_restaurant.php: ' . $e->getMessage());
}

echo json_encode($response);
$conn->close();
?>