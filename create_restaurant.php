<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Generate a random password for the owner
function generateRandomPassword($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $password;
}

try {
    $pdo->beginTransaction();

    // Handle file upload for restaurant image
    $restaurantImagePath = null;
    if (isset($_FILES['restaurant_image']) && $_FILES['restaurant_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/restaurants/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $extension = pathinfo($_FILES['restaurant_image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('restaurant_') . '.' . $extension;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['restaurant_image']['tmp_name'], $destination)) {
            $restaurantImagePath = $destination;
        }
    }

    // Create restaurant owner account
    $ownerPassword = generateRandomPassword();
    $hashedPassword = password_hash($ownerPassword, PASSWORD_BCRYPT);

    // Insert restaurant data
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

            // Handle menu items for this category
            if (isset($_POST['item_name'][$index])) {
                foreach ($_POST['item_name'][$index] as $itemIndex => $itemName) {
                    // Handle file upload for item image
                    $itemImagePath = null;
                    if (isset($_FILES['item_image'][$index][$itemIndex]) && 
                        $_FILES['item_image'][$index][$itemIndex]['error'] === UPLOAD_ERR_OK) {
                        
                        $uploadDir = 'uploads/menu_items/';
                        if (!file_exists($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        
                        $extension = pathinfo($_FILES['item_image'][$index][$itemIndex]['name'], PATHINFO_EXTENSION);
                        $filename = uniqid('item_') . '.' . $extension;
                        $destination = $uploadDir . $filename;
                        
                        if (move_uploaded_file($_FILES['item_image'][$index][$itemIndex]['tmp_name'], $destination)) {
                            $itemImagePath = $destination;
                        }
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
        'owner_password' => $ownerPassword
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>