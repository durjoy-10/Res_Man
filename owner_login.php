<?php
// owner_login.php
require_once 'owner_db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Invalid request method: {$_SERVER['REQUEST_METHOD']}");
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$email = filter_input(INPUT_POST, 'owner_email', FILTER_SANITIZE_EMAIL);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

if (empty($email) || empty($password)) {
    error_log("Missing email or password - Email: " . ($email ?? 'null') . ", Password provided: " . (empty($password) ? 'No' : 'Yes'));
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    exit;
}

try {
    error_log("Owner login attempt - Email: $email");
    $stmt = $pdo->prepare("SELECT id, owner_password FROM restaurants WHERE owner_email = ?");
    if (!$stmt) {
        throw new PDOException("Prepare failed: " . $pdo->errorInfo()[2]);
    }
    $stmt->execute([$email]);
    $restaurant = $stmt->fetch();

    if ($restaurant) {
        error_log("Restaurant found for email: $email, ID: {$restaurant['id']}");
        if (password_verify($password, $restaurant['owner_password'])) {
            if (!is_numeric($restaurant['id']) || $restaurant['id'] <= 0) {
                error_log("Invalid restaurant ID for email: $email, ID: {$restaurant['id']}");
                echo json_encode(['success' => false, 'message' => 'Invalid restaurant ID in database']);
                exit;
            }
            error_log("Owner login successful for email: $email, restaurant_id: {$restaurant['id']}");
            echo json_encode(['success' => true, 'restaurant_id' => (int)$restaurant['id']]);
        } else {
            error_log("Password verification failed for email: $email");
            echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        }
    } else {
        error_log("No restaurant found for email: $email");
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    }
} catch (PDOException $e) {
    error_log("Database error in owner_login.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred during login: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("Error in owner_login.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred during login: ' . $e->getMessage()]);
}
?>