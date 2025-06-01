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

    // Validate inputs
    $requiredFields = ['name', 'description', 'owner_name', 'owner_email', 'owner_password', 'phone', 'address'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception(ucfirst($field) . " is required");
        }
    }

    // Validate email uniqueness
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM restaurants WHERE owner_email = ?");
    $stmt->execute([$_POST['owner_email']]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Owner email already exists");
    }

    // Handle restaurant image
    $restaurantImagePath = null;
    if (!empty($_FILES['restaurant_image']['name'])) {
        $restaurantImagePath = handleFileUpload(
            $_FILES['restaurant_image'], 
            'uploads/restaurants/', 
            'restaurant_'
        );
    }

    // Hash the password
    $hashedPassword = password_hash($_POST['owner_password'], PASSWORD_BCRYPT);

    // Insert restaurant
    $stmt = $pdo->prepare("INSERT INTO restaurants 
        (name, description, owner_name, owner_email, owner_password, phone, address, image_path) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $_POST['name'],
        $_POST['description'],
        $_POST['owner_name'],
        $_POST['owner_email'],
        $hashedPassword,
        $_POST['phone'],
        $_POST['address'],
        $restaurantImagePath
    ]);
    
    $restaurantId = $pdo->lastInsertId();

    // Handle menu categories and items
    if (isset($_POST['category_name'])) {
        foreach ($_POST['category_name'] as $index => $categoryName) {
            if (empty($categoryName)) continue;
            $stmt = $pdo->prepare("INSERT INTO menu_categories 
                (restaurant_id, name, description) 
                VALUES (?, ?, ?)");
            $stmt->execute([
                $restaurantId,
                $categoryName,
                $_POST['category_description'][$index] ?? null
            ]);
            $categoryId = $pdo->lastInsertId();

            if (isset($_POST['item_name'][$index])) {
                foreach ($_POST['item_name'][$index] as $itemIndex => $itemName) {
                    if (empty($itemName)) continue;
                    $itemImagePath = null;
                    if (!empty($_FILES['item_image']['name'][$index][$itemIndex])) {
                        $file = [
                            'name' => $_FILES['item_image']['name'][$index][$itemIndex],
                            'type' => $_FILES['item_image']['type'][$index][$itemIndex],
                            'tmp_name' => $_FILES['item_image']['tmp_name'][$index][$itemIndex],
                            'error' => $_FILES['item_image']['error'][$index][$itemIndex],
                            'size' => $_FILES['item_image']['size'][$index][$itemIndex]
                        ];
                        $itemImagePath = handleFileUpload(
                            $file,
                            'uploads/menu_items/',
                            'item_'
                        );
                    }

                    $stmt = $pdo->prepare("INSERT INTO menu_items 
                        (category_id, name, description, price, stock, image_path) 
                        VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $categoryId,
                        $itemName,
                        $_POST['item_description'][$index][$itemIndex] ?? null,
                        $_POST['item_price'][$index][$itemIndex] ?? 0,
                        $_POST['item_stock'][$index][$itemIndex] ?? 0,
                        $itemImagePath
                    ]);
                }
            }
        }
    }

    // Handle offers
    if (isset($_POST['offer_description'])) {
        foreach ($_POST['offer_description'] as $index => $offerDesc) {
            if (empty($offerDesc)) continue;
            $validUntil = !empty($_POST['offer_valid_until'][$index]) ? 
                $_POST['offer_valid_until'][$index] : null;
            $stmt = $pdo->prepare("INSERT INTO offers 
                (restaurant_id, description, valid_until) 
                VALUES (?, ?, ?)");
            $stmt->execute([
                $restaurantId,
                $offerDesc,
                $validUntil
            ]);
        }
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'restaurant_id' => $restaurantId,
        'owner_email' => $_POST['owner_email'],
        'owner_password' => $_POST['owner_password'] // Return plain password for display
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Error in create_restaurant.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>