<?php require 'check_auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Management System</title>
    <link rel="stylesheet" href="static/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .restaurant-details {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .restaurant-details img {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .menu-category {
            margin-bottom: 30px;
        }
        
        .menu-category h3 {
            border-bottom: 2px solid #e74c3c;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        
        .menu-item {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 8px;
        }
        
        .menu-item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .menu-item-details {
            flex: 1;
        }
        
        .menu-item-price {
            font-weight: bold;
            color: #e74c3c;
        }
        
        .order-form {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-top: 30px;
        }
        
        .default-image {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f5f5;
            color: #999;
            border-radius: 8px;
        }
        
        .restaurant-default-image {
            width: 100%;
            height: 400px;
            font-size: 3rem;
        }
        
        .menu-item-default-image {
            width: 100px;
            height: 100px;
            font-size: 1.5rem;
        }
        
        .restaurant-card-horizontal img {
            height: 200px;
            width: 100%;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .restaurant-card-default-image {
            height: 200px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f5f5;
            border-radius: 8px;
            font-size: 2rem;
            color: #999;
        }
        
        .order-form h2 {
            margin-bottom: 20px;
        }
        
        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .form-group {
            flex: 1;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .submit-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .submit-btn:hover {
            background: #c0392b;
        }
        
        .search-container {
            padding: 20px;
            background: white;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .search-container input {
            padding: 10px;
            width: 70%;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .search-container button {
            padding: 10px 20px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .restaurants-container {
            margin: 20px 0;
        }
        
        .restaurants-horizontal-scroll {
            display: flex;
            overflow-x: auto;
            gap: 20px;
            padding: 20px 0;
        }
        
        .restaurant-card-horizontal {
            min-width: 300px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .restaurant-card-horizontal h3 {
            padding: 15px;
            margin: 0;
        }
        
        .restaurant-info {
            padding: 0 15px 15px;
        }
        
        .restaurant-info p {
            margin: 5px 0;
            font-size: 0.9rem;
        }
        
        .view-btn {
            display: block;
            text-align: center;
            padding: 10px;
            background: #e74c3c;
            color: white;
            text-decoration: none;
            border-radius: 0 0 8px 8px;
        }
        
        .search-results {
            margin: 20px 0;
        }
        
        .food-results {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .food-item {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .food-image img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .food-details {
            padding: 15px;
        }
        
        .food-details h3 {
            margin: 0 0 10px;
        }
        
        .food-price {
            font-weight: bold;
            color: #e74c3c;
            margin: 10px 0;
        }
        
        .order-btn {
            display: inline-block;
            padding: 8px 15px;
            background: #e74c3c;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <header>
        <h1>Restaurant Management System</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="cooking_video.html">Cooking Videos</a>
            <a href="about.html">About</a>
            <a href="contact.html">Contact</a>
            <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
        </nav>
    </header>
    
    <?php
    require 'db_connection.php';
    
    function getImageUrl($path, $type = 'restaurant') {
        $baseDir = '/opt/lampp/htdocs/restaurant_management/';
        $webRoot = '/restaurant_management/';
        
        $defaults = [
            'restaurant' => $webRoot . 'static/media/default-restaurant.jpeg',
            'food' => $webRoot . 'static/media/default-food.jpeg'
        ];
        
        if (empty($path)) {
            return $defaults[$type];
        }
        
        if (strpos($path, 'http') === 0 || strpos($path, 'https') === 0) {
            return $path;
        }
        
        if (strpos($path, $webRoot) === 0) {
            return $path;
        }
        
        if (strpos($path, $baseDir) === 0) {
            return $webRoot . str_replace($baseDir, '', $path);
        }
        
        $local_path = $baseDir . ltrim($path, '/');
        if (file_exists($local_path)) {
            return $webRoot . ltrim($path, '/');
        }
        
        return $defaults[$type];
    }

    if (isset($_GET['restaurant_id'])) {
        $restaurant_id = $_GET['restaurant_id'];
        
        $stmt = $pdo->prepare("SELECT * FROM restaurants WHERE id = ?");
        $stmt->execute([$restaurant_id]);
        $restaurant = $stmt->fetch();
        
        if ($restaurant) {
            echo '<div class="container">';
            echo '<div class="restaurant-details">';
            echo '<h2>' . htmlspecialchars($restaurant['name']) . '</h2>';
            
            $restaurantImageUrl = getImageUrl($restaurant['image_path'], 'restaurant');
            if (strpos($restaurantImageUrl, 'default-restaurant.jpg') !== false) {
                echo '<div class="restaurant-default-image default-image"><i class="fas fa-store"></i></div>';
            } else {
                echo '<img src="' . htmlspecialchars($restaurantImageUrl) . '" alt="' . htmlspecialchars($restaurant['name']) . '" onerror="this.onerror=null;this.src=\'' . getImageUrl(null, 'restaurant') . '\'">';
            }
            
            echo '<p>' . htmlspecialchars($restaurant['description']) . '</p>';
            echo '<p><i class="fas fa-phone"></i> ' . htmlspecialchars($restaurant['phone']) . '</p>';
            echo '<p><i class="fas fa-map-marker-alt"></i> ' . htmlspecialchars($restaurant['address']) . '</p>';
            
            $stmt = $pdo->prepare("SELECT * FROM menu_categories WHERE restaurant_id = ?");
            $stmt->execute([$restaurant_id]);
            $categories = $stmt->fetchAll();
            
            if ($categories) {
                echo '<h3>Menu</h3>';
                
                foreach ($categories as $category) {
                    echo '<div class="menu-category">';
                    echo '<h3>' . htmlspecialchars($category['name']) . '</h3>';
                    
                    if (!empty($category['description'])) {
                        echo '<p>' . htmlspecialchars($category['description']) . '</p>';
                    }
                    
                    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE category_id = ?");
                    $stmt->execute([$category['id']]);
                    $items = $stmt->fetchAll();
                    
                    if ($items) {
                        foreach ($items as $item) {
                            echo '<div class="menu-item">';
                            
                            $itemImageUrl = getImageUrl($item['image_path'], 'food');
                            if (strpos($itemImageUrl, 'default-food.jpg') !== false) {
                                echo '<div class="menu-item-default-image default-image"><i class="fas fa-utensils"></i></div>';
                            } else {
                                echo '<img src="' . htmlspecialchars($itemImageUrl) . '" alt="' . htmlspecialchars($item['name']) . '" class="menu-item-image" onerror="this.onerror=null;this.src=\'' . getImageUrl(null, 'food') . '\'">';
                            }
                            
                            echo '<div class="menu-item-details">';
                            echo '<h4>' . htmlspecialchars($item['name']) . '</h4>';
                            echo '<p>' . htmlspecialchars($item['description']) . '</p>';
                            echo '<p class="menu-item-price">$' . number_format($item['price'], 2) . '</p>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No items in this category yet.</p>';
                    }
                    
                    echo '</div>';
                }
            } else {
                echo '<p>No menu categories available yet.</p>';
            }
            
            echo '<div class="order-form">';
            echo '<h2>Place Your Order</h2>';
            echo '<form id="order-form" action="process_order.php" method="POST">';
            echo '<input type="hidden" name="restaurant_id" value="' . $restaurant_id . '">';
            
            echo '<div class="form-row">';
            echo '<div class="form-group">';
            echo '<label for="name">Your Name:</label>';
            echo '<input type="text" id="name" name="name" required>';
            echo '</div>';
            
            echo '<div class="form-group">';
            echo '<label for="email">Email:</label>';
            echo '<input type="email" id="email" name="email" required>';
            echo '</div>';
            echo '</div>';
            
            echo '<div class="form-row">';
            echo '<div class="form-group">';
            echo '<label for="phone">Phone:</label>';
            echo '<input type="tel" id="phone" name="phone" required>';
            echo '</div>';
            
            echo '<div class="form-group">';
            echo '<label for="address">Delivery Address:</label>';
            echo '<input type="text" id="address" name="address" required>';
            echo '</div>';
            echo '</div>';
            
            echo '<div class="form-group">';
            echo '<label for="instructions">Special Instructions:</label>';
            echo '<textarea id="instructions" name="instructions" rows="3"></textarea>';
            echo '</div>';
            
            echo '<button type="submit" class="submit-btn">Place Order</button>';
            echo '</form>';
            echo '</div>';
            
            echo '</div>';
            echo '</div>';
        } else {
            echo '<div class="container"><p>Restaurant not found.</p></div>';
        }
    } else {
        ?>
        <div class="search-container">
            <input type="text" id="search" placeholder="Search for food or restaurant...">
            <button onclick="searchFood()"><i class="fas fa-search"></i> Search</button>
        </div>
        
        <div id="search-results" class="search-results"></div>
        
        <div class="restaurants-container">
            <div class="section-title">
                <h2>All Restaurants</h2>
            </div>
            
            <div class="restaurants-horizontal-scroll" id="restaurants-horizontal">
                <?php
                $stmt = $pdo->query("SELECT * FROM restaurants ORDER BY id DESC");
                while ($restaurant = $stmt->fetch()):
                    $image_url = getImageUrl($restaurant['image_path'], 'restaurant');
                ?>
                <div class="restaurant-card-horizontal">
                    <?php if (empty($restaurant['image_path']) || strpos($image_url, 'default-restaurant.jpg') !== false): ?>
                        <div class="restaurant-card-default-image default-image">
                            <i class="fas fa-store"></i>
                        </div>
                    <?php else: ?>
                        <img src="<?= htmlspecialchars($image_url) ?>" 
                             alt="<?= htmlspecialchars($restaurant['name']) ?>"
                             onerror="this.onerror=null;this.src='<?= getImageUrl(null, 'restaurant') ?>'">
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($restaurant['name']) ?></h3>
                    <div class="restaurant-info">
                        <p><i class="fas fa-info-circle"></i> <?= htmlspecialchars($restaurant['description']) ?></p>
                        <p><i class="fas fa-phone"></i> <?= htmlspecialchars($restaurant['phone']) ?></p>
                        <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($restaurant['address']) ?></p>
                    </div>
                    <a href="index.php?restaurant_id=<?= $restaurant['id'] ?>" class="view-btn">View Menu</a>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('img').forEach(img => {
                    img.onerror = function() {
                        const container = document.createElement('div');
                        if (this.classList.contains('menu-item-image')) {
                            container.className = 'menu-item-default-image default-image';
                            container.innerHTML = '<i class="fas fa-utensils"></i>';
                        } else {
                            container.className = 'restaurant-card-default-image default-image';
                            container.innerHTML = '<i class="fas fa-store"></i>';
                        }
                        this.replaceWith(container);
                    };
                });
            });

            function searchFood() {
                const query = document.getElementById('search').value.trim();
                if (query === '') {
                    document.getElementById('search-results').innerHTML = '';
                    return;
                }
                
                fetch(`get_food_search.php?query=${encodeURIComponent(query)}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        const resultsContainer = document.getElementById('search-results');
                        resultsContainer.innerHTML = '';
                        
                        if (data.length === 0) {
                            resultsContainer.innerHTML = '<div class="no-results"><i class="fas fa-utensils"></i> No results found for your search.</div>';
                            return;
                        }
                        
                        const resultsList = document.createElement('div');
                        resultsList.className = 'food-results';
                        
                        data.forEach(item => {
                            const itemImageUrl = item.image_path ? 
                                (item.image_path.startsWith('http') ? item.image_path : '/' + item.image_path) : 
                                '/static/media/default-food.jpg';
                            
                            const resultItem = document.createElement('div');
                            resultItem.className = 'food-item';
                            resultItem.innerHTML = `
                                <div class="food-image">
                                    <img src="${itemImageUrl}" alt="${item.name}" onerror="this.onerror=null;this.src='/static/media/default-food.jpg'">
                                </div>
                                <div class="food-details">
                                    <h3>${item.name}</h3>
                                    <p class="food-description">${item.description}</p>
                                    <p class="food-price"><strong>Price:</strong> $${item.price.toFixed(2)}</p>
                                    <p class="food-restaurant"><i class="fas fa-store"></i> ${item.restaurant_name}</p>
                                    <a href="index.php?restaurant_id=${item.restaurant_id}" class="order-btn"><i class="fas fa-shopping-cart"></i> Order Now</a>
                                </div>
                            `;
                            resultsList.appendChild(resultItem);
                        });
                        
                        resultsContainer.appendChild(resultsList);
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        document.getElementById('search-results').innerHTML = 
                            '<div class="error-message"><i class="fas fa-exclamation-triangle"></i> Error loading search results</div>';
                    });
            }
            
            document.getElementById('search').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    searchFood();
                }
            });
            
            if (document.getElementById('order-form')) {
                document.getElementById('order-form').addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const form = this;
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                    submitBtn.disabled = true;
                    
                    const formData = new FormData(form);
                    
                    fetch('process_order.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Order placed successfully! Order ID: ' + data.order_id);
                            form.reset();
                        } else {
                            throw new Error(data.message || 'Failed to place order');
                        }
                    })
                    .catch(error => {
                        console.error('Order error:', error);
                        alert('Error: ' + error.message);
                    })
                    .finally(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    });
                });
            }
        </script>
        <?php
    }
    ?>
</body>
</html>