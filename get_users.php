<?php
header('Content-Type: application/json');
require_once 'db_connection.php';
require_once 'check_auth.php';

// Get query parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Build base query
$query = "SELECT id, username, name, email, phone, address, 
          profile_photo, created_at FROM users WHERE 1=1";
$params = [];

// Add search conditions if provided
if (!empty($search)) {
    $query .= " AND (username LIKE :search OR name LIKE :search OR email LIKE :search)";
    $params[':search'] = "%$search%";
}

// Add sorting
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
    default: // 'newest'
        $query .= " ORDER BY created_at DESC";
        break;
}

try {
    $stmt = $pdo->prepare($query);
    
    // Bind parameters if needed
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format data for frontend with proper photo paths
    $result = [];
    foreach ($users as $user) {
        // Determine the profile photo path
        $profilePhoto = null;
        if (!empty($user['profile_photo'])) {
            // Check if the file exists and is readable
            if (file_exists($user['profile_photo']) && is_readable($user['profile_photo'])) {
                $profilePhoto = $user['profile_photo'];
            } else {
                // Log missing photos for debugging
                error_log("Profile photo not found or not readable: " . $user['profile_photo']);
            }
        }
        
        $result[] = [
            'id' => $user['id'],
            'username' => htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'),
            'name' => htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8'),
            'email' => htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'),
            'phone' => htmlspecialchars($user['phone'] ?? '', ENT_QUOTES, 'UTF-8'),
            'address' => htmlspecialchars($user['address'] ?? '', ENT_QUOTES, 'UTF-8'),
            'profile_photo' => $profilePhoto, // Will be null if no photo or photo not found
            'created_at' => date('Y-m-d H:i:s', strtotime($user['created_at']))
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $result
    ]);
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to load users. Please try again later.'
    ]);
}
?>