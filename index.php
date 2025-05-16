<?php require 'check_auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Management System</title>
    <link rel="stylesheet" href="static/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <h1>Restaurant Management System</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="cooking_video.html">Cooking Videos</a>
            <a href="about.html">About</a>
            <a href="contact.html">Contact</a>
            <button class="logout-btn" onclick="logout()">Logout</button>
        </nav>
    </header>
    
    <div class="search-container">
        <input type="text" id="search" placeholder="Search for food or restaurant...">
        <button onclick="searchFood()"><i class="fas fa-search"></i> Search</button>
    </div>
    
    <div id="search-results" class="search-results"></div>
    
    <div class="restaurants-container">
        <h2>Featured Restaurants</h2>
        <div class="restaurants-grid" id="restaurants-grid">
            <!-- Restaurants will be loaded here -->
            <?php
            require 'db_connection.php';
            $stmt = $pdo->query("SELECT * FROM restaurants ORDER BY id DESC");
            while ($restaurant = $stmt->fetch()):
                $image_path = !empty($restaurant['image_path']) ? $restaurant['image_path'] : 'static/media/default-restaurant.jpg';
            ?>
            <div class="restaurant-card">
                <img src="<?= htmlspecialchars($image_path) ?>" alt="<?= htmlspecialchars($restaurant['name']) ?>">
                <h3><?= htmlspecialchars($restaurant['name']) ?></h3>
                <p><?= htmlspecialchars(substr($restaurant['description'], 0, 100)) ?>...</p>
                <p><i class="fas fa-phone"></i> <?= htmlspecialchars($restaurant['phone']) ?></p>
                <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars(substr($restaurant['address'], 0, 50)) ?></p>
                <a href="Restaurant<?= $restaurant['id'] ?>/home1.html" class="view-btn">View Restaurant</a>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    
    <script>
        // Enhanced search function
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
                        const resultItem = document.createElement('div');
                        resultItem.className = 'food-item';
                        resultItem.innerHTML = `
                            <div class="food-image">
                                <img src="${item.image_path || 'static/media/default-food.jpg'}" alt="${item.name}">
                            </div>
                            <div class="food-details">
                                <h3>${item.name}</h3>
                                <p class="food-description">${item.description}</p>
                                <p class="food-price"><strong>Price:</strong> $${item.price.toFixed(2)}</p>
                                <p class="food-restaurant"><i class="fas fa-store"></i> ${item.restaurant_name}</p>
                                <a href="Restaurant${item.restaurant_id}/home1.html" class="order-btn"><i class="fas fa-shopping-cart"></i> Order Now</a>
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
        
        // Handle Enter key in search
        document.getElementById('search').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchFood();
            }
        });
        
        function logout() {
            fetch('logout.php')
                .then(response => {
                    if (!response.ok) throw new Error('Logout failed');
                    return response.json();
                })
                .then(data => {
                    if(data.success) {
                        window.location.href = 'login.html';
                    }
                })
                .catch(error => {
                    console.error('Logout error:', error);
                    alert('Logout failed. Please try again.');
                });
        }
        
        // Refresh restaurants every 30 seconds (optional)
        setInterval(() => {
            fetch('get_restaurant.php')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('restaurants-grid');
                    let currentHTML = container.innerHTML;
                    let newHTML = '';
                    
                    data.forEach(restaurant => {
                        newHTML += `
                            <div class="restaurant-card">
                                <img src="${restaurant.image_path || 'static/media/default-restaurant.jpg'}" alt="${restaurant.name}">
                                <h3>${restaurant.name}</h3>
                                <p>${restaurant.description.substring(0, 100)}...</p>
                                <p><i class="fas fa-phone"></i> ${restaurant.phone}</p>
                                <a href="Restaurant${restaurant.id}/home1.html" class="view-btn">View Restaurant</a>
                            </div>
                        `;
                    });
                    
                    if (newHTML !== currentHTML) {
                        container.innerHTML = newHTML;
                    }
                });
        }, 30000);
    </script>
</body>
</html>