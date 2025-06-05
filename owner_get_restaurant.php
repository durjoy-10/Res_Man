<?php
// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure JSON content type
header('Content-Type: application/json', true);

// Log errors to a file
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

$response = ['success' => false, 'message' => '', 'data' => null];

try {
    // Attempt to include database connection
    if (!file_exists('db_connect.php')) {
        throw new Exception('db_connect.php file not found');
    }
    require_once 'db_connect.php';
    if (!$conn) {
        throw new Exception('Database connection failed: Connection object is null');
    }

    // Retrieve and sanitize restaurant_id from query parameter
    $restaurant_id = filter_input(INPUT_GET, 'restaurant_id', FILTER_SANITIZE_NUMBER_INT);
    error_log("Received restaurant_id: $restaurant_id");

    if (!$restaurant_id || !is_numeric($restaurant_id)) {
        throw new Exception('Invalid or missing restaurant ID');
    }

    // Prepare the query to fetch restaurant by restaurant_id
    $stmt = $conn->prepare("SELECT id, name, description, owner_name, owner_email, phone, address, image_path FROM restaurants WHERE id = ?");
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param("i", $restaurant_id);
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $restaurant = $result->fetch_assoc();
        $response['success'] = true;
        $response['data'] = $restaurant;
        error_log("Restaurant found: " . json_encode($restaurant));
    } else {
        $response['message'] = 'Restaurant not found';
        error_log("No restaurant found for ID: $restaurant_id");
    }

    $stmt->close();
} catch (Exception $e) {
    $response['message'] = 'Error fetching restaurant: ' . $e->getMessage();
    error_log('Error in owner_get_restaurant.php: ' . $e->getMessage());
} finally {
    // Ensure response is always sent as JSON
    echo json_encode($response);
    if (isset($conn)) {
        $conn->close();
    }
}
?>