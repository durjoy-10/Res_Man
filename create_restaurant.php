+33<?php
require 'db_connection.php';

// Set headers first to prevent any output before JSON
header('Content-Type: application/json');

// Disable error display to prevent corrupting JSON output
ini_set('display_errors', 0);
error_reporting(0);

function generateRestaurantPages($restaurant_id, $pdo) {
    $restaurant_dir = "../Restaurant" . $restaurant_id;
    $media_dir = $restaurant_dir . "/static/media";

    // Get restaurant data
    $stmt = $pdo->prepare("SELECT * FROM restaurants WHERE id = ?");
    $stmt->execute([$restaurant_id]);
    $restaurant = $stmt->fetch();

    // Get menu items
    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE restaurant_id = ?");
    $stmt->execute([$restaurant_id]);
    $menu_items = $stmt->fetchAll();

    // Get offers
    $stmt = $pdo->prepare("SELECT * FROM offers WHERE restaurant_id = ?");
    $stmt->execute([$restaurant_id]);
    $offers = $stmt->fetchAll();

    // Generate home page
    $home_page = generateHomePage($restaurant, $menu_items);
    file_put_contents($restaurant_dir . "/home1.html", $home_page);

    // Generate about page
    $about_page = generateAboutPage($restaurant);
    file_put_contents($media_dir . "/about.html", $about_page);

    // Generate contact page
    $contact_page = generateContactPage($restaurant);
    file_put_contents($media_dir . "/contact.html", $contact_page);

    // Generate offers page
    $offers_page = generateOffersPage($restaurant, $offers);
    file_put_contents($media_dir . "/offers.html", $offers_page);

    // Generate order page
    $order_page = generateOrderPage($restaurant_id);
    file_put_contents($media_dir . "/order.html", $order_page);
}

function generateHomePage($restaurant, $menu_items) {
    // Group menu items by category
    $categories = [];
    foreach ($menu_items as $item) {
        if (!isset($categories[$item['category']])) {
            $categories[$item['category']] = [];
        }
        $categories[$item['category']][] = $item;
    }
    
    // Generate menu sections
    $menu_sections = '';
    foreach ($categories as $category => $items) {
        $menu_items_html = '';
        foreach ($items as $item) {
            $menu_items_html .= '
                <div class="menu-item">
                    <img src="' . htmlspecialchars($item['image_path']) . '" alt="' . htmlspecialchars($item['name']) . '">
                    <h3>' . htmlspecialchars($item['name']) . '</h3>
                    <p>' . htmlspecialchars($item['description']) . '</p>
                    <p>Price: $' . number_format($item['price'], 2) . '</p>
                    <a href="static/media/order.html" class="buy-button">Order Now</a>
                </div>
            ';
        }
        
        $menu_sections .= '
            <section class="food-section">
                <h2>' . htmlspecialchars($category) . '</h2>
                <div class="menu-container">
                    ' . $menu_items_html . '
                </div>
            </section>
        ';
    }
    
    return '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . htmlspecialchars($restaurant['name']) . '</title>
        <link rel="stylesheet" href="static/css/styles.css">
    </head>
    <body>
        <header>
            <h1>' . htmlspecialchars($restaurant['name']) . '</h1>
            <nav>
                <a href="home1.html">Home</a>
                <a href="static/media/about.html">About</a>
                <a href="static/media/contact.html">Contact</a>
                <a href="static/media/offers.html">Offers</a>
                <a href="../index.php">Back to Main</a>
            </nav>
        </header>

        <div class="container">
            <h2>Welcome to ' . htmlspecialchars($restaurant['name']) . '</h2>
            ' . $menu_sections . '
        </div>
    </body>
    </html>
    ';
}

function generateAboutPage($restaurant) {
    return '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . htmlspecialchars($restaurant['name']) . ' - About</title>
        <link rel="stylesheet" href="../css/styles.css">
    </head>
    <body>
        <header>
            <h1>' . htmlspecialchars($restaurant['name']) . '</h1>
            <nav>
                <a href="../home1.html">Home</a>
                <a href="about.html">About</a>
                <a href="contact.html">Contact</a>
                <a href="offers.html">Offers</a>
            </nav>
        </header>
        <div class="container">
            <h2>About Us</h2>
            <p>' . nl2br(htmlspecialchars($restaurant['description'])) . '</p>
            <h3>Our Story</h3>
            <p>' . htmlspecialchars($restaurant['owner_name']) . ' founded ' . htmlspecialchars($restaurant['name']) . ' with a passion for great food.</p>
            <h3>Visit Us</h3>
            <p>' . nl2br(htmlspecialchars($restaurant['address'])) . '</p>
        </div>
    </body>
    </html>
    ';
}

function generateContactPage($restaurant) {
    return '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . htmlspecialchars($restaurant['name']) . ' - Contact</title>
        <link rel="stylesheet" href="../css/styles.css">
    </head>
    <body>
        <header>
            <h1>' . htmlspecialchars($restaurant['name']) . '</h1>
            <nav>
                <a href="../home1.html">Home</a>
                <a href="about.html">About</a>
                <a href="contact.html">Contact</a>
                <a href="offers.html">Offers</a>
            </nav>
        </header>
        <div class="container">
            <h2>Contact Us</h2>
            <p><strong>Owner:</strong> ' . htmlspecialchars($restaurant['owner_name']) . '</p>
            <p><strong>Email:</strong> ' . htmlspecialchars($restaurant['owner_email']) . '</p>
            <p><strong>Phone:</strong> ' . htmlspecialchars($restaurant['phone']) . '</p>
            <p><strong>Address:</strong><br>' . nl2br(htmlspecialchars($restaurant['address'])) . '</p>
            <p>We look forward to serving you!</p>
        </div>
    </body>
    </html>
    ';
}

function generateOffersPage($restaurant, $offers) {
    $offers_list = '';
    
    if (empty($offers)) {
        $offers_list = '<p>Check back soon for special offers!</p>';
    } else {
        $offers_list = '<ul class="offers-list">';
        foreach ($offers as $offer) {
            $valid_until = $offer['valid_until'] ? ' (Valid until ' . date('F j, Y', strtotime($offer['valid_until'])) . ')' : '';
            $offers_list .= '<li>' . htmlspecialchars($offer['description']) . $valid_until . '</li>';
        }
        $offers_list .= '</ul>';
    }
    
    return '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . htmlspecialchars($restaurant['name']) . ' - Offers</title>
        <link rel="stylesheet" href="../css/styles.css">
    </head>
    <body>
        <header>
            <h1>' . htmlspecialchars($restaurant['name']) . '</h1>
            <nav>
                <a href="../home1.html">Home</a>
                <a href="about.html">About</a>
                <a href="contact.html">Contact</a>
                <a href="offers.html">Offers</a>
            </nav>
        </header>
        <div class="container">
            <h2>Current Offers</h2>
            ' . $offers_list . '
        </div>
    </body>
    </html>
    ';
}

function generateOrderPage($restaurant_id) {
    return '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Place Your Order</title>
        <link rel="stylesheet" href="../css/styles.css">
    </head>
    <body>
        <header>
            <nav>
                <a href="../home1.html">Home</a>
                <a href="about.html">About</a>
                <a href="contact.html">Contact</a>
                <a href="offers.html">Offers</a>
            </nav>
        </header>
        <div class="container">
            <h2>Place Your Order</h2>
            <form action="process_order.php" method="POST">
                <input type="hidden" name="restaurant_id" value="' . $restaurant_id . '">
                <div class="form-group">
                    <label for="name">Full Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="address">Delivery Address:</label>
                    <textarea id="address" name="address" required></textarea>
                </div>
                <div class="form-group">
                    <label for="notes">Special Instructions:</label>
                    <textarea id="notes" name="notes"></textarea>
                </div>
                <button type="submit" class="submit-btn">Place Order</button>
            </form>
        </div>
    </body>
    </html>
    ';
}

try {
    // Validate required fields
    $required = ['name', 'description', 'owner_name', 'owner_email', 'phone', 'address'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Create restaurant directory structure
    $restaurant_dir = "../Restaurant";
    if (!file_exists($restaurant_dir)) {
        mkdir($restaurant_dir, 0755, true);
    }

    // Start database transaction
    $pdo->beginTransaction();

    // Insert restaurant
    $stmt = $pdo->prepare("INSERT INTO restaurants (name, description, owner_name, owner_email, phone, address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['name'],
        $_POST['description'],
        $_POST['owner_name'],
        $_POST['owner_email'],
        $_POST['phone'],
        $_POST['address']
    ]);
    $restaurant_id = $pdo->lastInsertId();

    // Create restaurant-specific directories
    $restaurant_dir = "../Restaurant" . $restaurant_id;
    $static_dir = $restaurant_dir . "/static";
    $css_dir = $static_dir . "/css";
    $media_dir = $static_dir . "/media";

    mkdir($restaurant_dir, 0755, true);
    mkdir($static_dir, 0755, true);
    mkdir($css_dir, 0755, true);
    mkdir($media_dir, 0755, true);

    // Copy template CSS
    copy("templates/styles.css", $css_dir . "/styles.css");

    // Process menu items
    if (isset($_POST['menu_name'])) {
        for ($i = 0; $i < count($_POST['menu_name']); $i++) {
            if (empty($_POST['menu_name'][$i])) continue;

            $image_path = '';
            if (!empty($_FILES['menu_image']['name'][$i])) {
                $target_file = $media_dir . '/' . basename($_FILES['menu_image']['name'][$i]);
                if (move_uploaded_file($_FILES['menu_image']['tmp_name'][$i], $target_file)) {
                    $image_path = "static/media/" . $_FILES['menu_image']['name'][$i];
                }
            }

            $stmt = $pdo->prepare("INSERT INTO menu_items (restaurant_id, category, name, description, price, image_path) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $restaurant_id,
                $_POST['menu_category'][$i] ?? 'Uncategorized',
                $_POST['menu_name'][$i],
                $_POST['menu_description'][$i] ?? '',
                $_POST['menu_price'][$i],
                $image_path
            ]);
        }
    }

    // Process offers
    if (isset($_POST['offer_description'])) {
        for ($i = 0; $i < count($_POST['offer_description']); $i++) {
            if (empty($_POST['offer_description'][$i])) continue;

            $valid_until = !empty($_POST['offer_valid_until'][$i]) ? $_POST['offer_valid_until'][$i] : null;
            
            $stmt = $pdo->prepare("INSERT INTO offers (restaurant_id, description, valid_until) VALUES (?, ?, ?)");
            $stmt->execute([
                $restaurant_id,
                $_POST['offer_description'][$i],
                $valid_until
            ]);
        }
    }

    // Generate all HTML pages
    generateRestaurantPages($restaurant_id, $pdo);

    // Commit transaction
    $pdo->commit();

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Restaurant created successfully',
        'restaurant_id' => $restaurant_id,
        'redirect_url' => "Restaurant{$restaurant_id}/home1.html"
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>