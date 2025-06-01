<?php
// owner_login.php
require_once 'owner_db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$email = filter_input(INPUT_POST, 'owner_email', FILTER_SANITIZE_EMAIL);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    exit;
}

try {
    error_log("Owner login attempt - Email: $email");
    $stmt = $pdo->prepare("SELECT id, owner_password FROM restaurants WHERE owner_email = ?");
    $stmt->execute([$email]);
    $restaurant = $stmt->fetch();

    if ($restaurant && password_verify($password, $restaurant['owner_password'])) {
        error_log("Owner login successful for email: $email, restaurant_id: {$restaurant['id']}");
        echo json_encode(['success' => true, 'restaurant_id' => $restaurant['id']]);
    } else {
        error_log("Owner login failed for email: $email");
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    }
} catch (Exception $e) {
    error_log("Error in owner_login.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
?>