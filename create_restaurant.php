<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

function generateRandomPassword($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $password;
}

function handleFileUpload($file, $uploadDir, $prefix) {
    // Define base directory
    $baseDir = '/opt/lampp/htdocs/restaurant_management/';
    $fullUploadDir = $baseDir . ltrim($uploadDir, '/');
    
    // Create directory if needed
    if (!file_exists($fullUploadDir)) {
        if (!mkdir($fullUploadDir, 0755, true)) {
            throw new Exception("Failed to create upload directory: $fullUploadDir");
        }
    }
    
    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("File upload error: " . $file['error']);
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception("Invalid file type. Only images are allowed.");
    }
    
    // Generate filename and move file
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $prefix . uniqid() . '.' . strtolower($extension);
    $destination = $fullUploadDir . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception("Failed to move uploaded file.");
    }
    
    // Return relative path
    return $uploadDir . $filename;
}

try {
    $pdo->beginTransaction();

    // Handle restaurant image
    $restaurantImagePath = null;
    if (!empty($_FILES['restaurant_image']['name'])) {
        $restaurantImagePath = handleFileUpload(
            $_FILES['restaurant_image'], 
            'uploads/restaurants/', 
            'restaurant_'
        );
    }

    // Create owner account
    $ownerPassword = generateRandomPassword();
    $hashedPassword = password_hash($ownerPassword, PASSWORD_BCRYPT);

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
            // Insert category
            $stmt = $pdo->prepare("INSERT INTO menu_categories 
                (restaurant_id, name, description) 
                VALUES (?, ?, ?)");
            
            $stmt->execute([
                $restaurantId,
                $categoryName,
                $_POST['category_description'][$index] ?? null
            ]);
            
            $categoryId = $pdo->lastInsertId();

            // Handle menu items
            if (isset($_POST['item_name'][$index])) {
                foreach ($_POST['item_name'][$index] as $itemIndex => $itemName) {
                    // Handle item image
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

                    // Insert menu item
                    $stmt = $pdo->prepare("INSERT INTO menu_items 
                        (category_id, name, description, price, image_path) 
                        VALUES (?, ?, ?, ?, ?)");
                    
                    $stmt->execute([
                        $categoryId,
                        $itemName,
                        $_POST['item_description'][$index][$itemIndex] ?? null,
                        $_POST['item_price'][$index][$itemIndex],
                        $itemImagePath
                    ]);
                }
            }
        }
    }

    // Handle offers
    if (isset($_POST['offer_description'])) {
        foreach ($_POST['offer_description'] as $index => $offerDesc) {
            if (!empty($offerDesc)) {
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
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'restaurant_id' => $restaurantId,
        'owner_email' => $_POST['owner_email'],
        'owner_password' => $ownerPassword,
        'image_url' => $restaurantImagePath ? convertPathToUrl($restaurantImagePath) : null
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Error in create_restaurant.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function convertPathToUrl($path) {
    $basePath = '/opt/lampp/htdocs/restaurant_management/';
    $relativePath = str_replace($basePath, '', $path);
    
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
    
    return "{$protocol}://{$host}{$baseUrl}/{$relativePath}";
}
?>