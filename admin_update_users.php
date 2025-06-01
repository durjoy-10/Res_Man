<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

function handleFileUpload($file, $uploadDir, $prefix) {
    $baseDir = __DIR__ . '/';
    $fullUploadDir = $baseDir . ltrim($uploadDir, '/');
    
    if (!file_exists($fullUploadDir)) {
        if (!mkdir($fullUploadDir, 0755, true)) {
            throw new Exception("Failed to create upload directory: $fullUploadDir");
        }
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("File upload error: " . $file['error']);
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception("Invalid file type. Only images are allowed.");
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $prefix . uniqid() . '.' . strtolower($extension);
    $destination = $fullUploadDir . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception("Failed to move uploaded file.");
    }
    
    return $uploadDir . $filename;
}

try {
    $pdo->beginTransaction();

    $user_id = $_POST['id'] ?? '';
    if (empty($user_id)) {
        throw new Exception('User ID is required');
    }

    // Validate email and username uniqueness
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$_POST['email'], $user_id]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Email already exists");
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND id != ?");
    $stmt->execute([$_POST['username'], $user_id]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Username already exists");
    }

    // Handle profile photo
    $profilePhotoPath = null;
    if (!empty($_FILES['profile_photo']['name'])) {
        $profilePhotoPath = handleFileUpload(
            $_FILES['profile_photo'], 
            'uploads/users/', 
            'user_'
        );
    }

    // Update user details
    $query = "UPDATE users SET name = ?, username = ?, email = ?, phone = ?, address = ?";
    $params = [
        $_POST['name'],
        $_POST['username'],
        $_POST['email'],
        $_POST['phone'] ?: null,
        $_POST['address'] ?: null
    ];
    if ($profilePhotoPath) {
        $query .= ", profile_photo = ?";
        $params[] = $profilePhotoPath;
    }
    if (!empty($_POST['password'])) {
        $query .= ", password = ?";
        $params[] = password_hash($_POST['password'], PASSWORD_BCRYPT);
    }
    $query .= " WHERE id = ?";
    $params[] = $user_id;

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    if ($stmt->rowCount() === 0) {
        throw new Exception("User not found or no changes made");
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'User updated successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Error in admin_update_user.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>