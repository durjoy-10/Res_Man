<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

function handleFileUpload($file, $uploadDir, $prefix) {
    $baseDir = '/opt/lampp/htdocs/restaurant_management/';
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

$categoryId = $_POST['category_id'] ?? null;
$itemId = $_POST['item_id'] ?? null;
$name = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$price = $_POST['price'] ?? 0;
$stock = $_POST['stock'] ?? 0;
$isNew = isset($_POST['is_new']);

if (!$categoryId || !$name || !$price) {
    echo json_encode(['success' => false, 'message' => 'Required fields are missing']);
    exit;
}

try {
    $imagePath = null;
    if (!empty($_FILES['item_image']['name'])) {
        $imagePath = handleFileUpload($_FILES['item_image'], 'uploads/menu_items/', 'item_');
    }
    
    if ($isNew) {
        // Insert new item
        $stmt = $pdo->prepare("
            INSERT INTO menu_items (category_id, name, description, price, stock, image_path)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$categoryId, $name, $description, $price, $stock, $imagePath]);
    } else {
        // Update existing item
        if ($imagePath) {
            // Update with new image
            $stmt = $pdo->prepare("
                UPDATE menu_items 
                SET name = ?, description = ?, price = ?, stock = ?, image_path = ?
                WHERE id = ?
            ");
            $stmt->execute([$name, $description, $price, $stock, $imagePath, $itemId]);
        } else {
            // Update without changing image
            $stmt = $pdo->prepare("
                UPDATE menu_items 
                SET name = ?, description = ?, price = ?, stock = ?
                WHERE id = ?
            ");
            $stmt->execute([$name, $description, $price, $stock, $itemId]);
        }
    }
    
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>