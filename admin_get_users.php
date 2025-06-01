<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'newest';

try {
    $query = "SELECT id, name, username, email, phone, address, profile_photo, created_at FROM users";
    $params = [];

    if (!empty($search)) {
        $query .= " WHERE name LIKE ? OR username LIKE ? OR email LIKE ?";
        $searchParam = "%$search%";
        $params = [$searchParam, $searchParam, $searchParam];
    }

    switch ($sort) {
        case 'oldest':
            $query .= " ORDER BY created_at ASC";
            break;
        case 'name_asc':
            $query .= " ORDER BY name ASC";
            break;
        case 'name_desc':
            $query .= " ORDER BY name DESC";
            break;
        default: // newest
            $query .= " ORDER BY created_at DESC";
            break;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $users]);
} catch (Exception $e) {
    error_log("Error in admin_get_users.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>