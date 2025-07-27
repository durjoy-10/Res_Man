<?php require 'check_auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Management System</title>
    <link rel="stylesheet" href="static/css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="static/css/cart.css">
    <style>
        .offers-section {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .offers-section.open {
            display: block;
        }
        .offer-card {
            background: white;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }
        .offer-card:hover {
            transform: translateY(-5px);
        }
        .offer-card h4 {
            color: #2c3e50;
            font-size: 1.2rem;
            margin-bottom: 10px;
        }
        .offer-card p {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        .offer-card .valid-until {
            color: #e74c3c;
            font-weight: bold;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <header>
        <h1>Food Zone Barishal</h1>
        <div class="search-container">
            <input type="text" id="restaurant-search" placeholder="Search restaurants by name..." 
                   oninput="handleSearchInput()" onkeydown="clearSearchIfEmpty(event)">
        </div>
        <nav>
            <a href="index.php">Home</a>
            <a href="cooking_video.html">Cooking Videos</a>
            <a href="about.html">About</a>
            <a href="contact.html">Contact</a>
            <a href="reviews.php">Reviews</a>
            <?php if (is_logged_in()): ?>
                <div class="profile-dropdown">
                    <div class="profile-icon" onclick="toggleProfileDropdown()">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="dropdown-content" id="profile-dropdown">
                        <div class="dropdown-header">
                            <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                            <p><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                        <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
                        <a href="order_history.php"><i class="fas fa-history"></i> Order History</a>
                        <button class="logout-btn" onclick="window.location.href='logout.php'">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.html" class="login-btn">Login</a>
                <a href="signup.html" class="signup-btn">Sign Up</a>
            <?php endif; ?>
        </nav>
    </header>

    <div class="cart-icon" onclick="toggleCart()">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-count" id="cart-count">0</span>
    </div>
    
    <div class="cart-container" id="cart-container">
        <div class="cart-header">
            <h3>Your Order</h3>
            <i class="fas fa-times" onclick="toggleCart()" style="cursor:pointer;"></i>
        </div>
        <div class="cart-items" id="cart-items"></div>
        <div class="cart-total">
            Total: ৳<span id="cart-total">0.00</span>
        </div>
        <button class="checkout-btn" onclick="showCheckoutForm()">Proceed to Checkout</button>
    </div>
    
    <div id="checkout-form-container" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:2000; justify-content:center; align-items:center;">
        <div style="background:white; padding:20px; border-radius:10px; width:500px; max-width:90%;">
            <h2>Checkout</h2>
            <form id="checkout-form">
                <input type="hidden" name="restaurant_id" id="checkout-restaurant-id">
                <div class="form-group">
                    <label>Delivery Address:</label>
                    <textarea name="delivery_address" required style="width:100%; padding:10px;"></textarea>
                </div>
                <div class="form-group">
                    <label>Special Instructions (optional):</label>
                    <textarea name="special_instructions" style="width:100%; padding:10px;"></textarea>
                </div>
                <div class="form-group payment-options">
                    <label>Payment Method:</label>
                    <select name="payment_method" id="payment-method" onchange="togglePaymentFields()">
                        <option value="cash_on_delivery">Cash on Delivery</option>
                        <option value="bkash">bKash</option>
                        <option value="nagad">Nagad</option>
                    </select>
                </div>
                <div class="form-group payment-fields" id="payment-fields">
                    <label>Payment Number:</label>
                    <input type="text" name="payment_number" placeholder="Enter payment number" required>
                    <label>Transaction ID:</label>
                    <input type="text" name="transaction_id" placeholder="Enter transaction ID" required>
                </div>
                <div style="display:flex; justify-content:space-between; margin-top:20px;">
                    <button type="button" onclick="hideCheckoutForm()" style="padding:10px 20px; background:#ccc; border:none; border-radius:5px; cursor:pointer;">Cancel</button>
                    <button type="submit" style="padding:10px 20px; background:#5f27cd; color:white; border:none; border-radius:5px; cursor:pointer;">Place Order</button>
                </div>
            </form>
        </div>
    </div>

    <?php
    require 'db_connection.php';
    
    $user_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $user_stmt->execute([$_SESSION['user_id']]);
    $user = $user_stmt->fetch();
    
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

            // Sidebar with menu sections
            echo '<div class="sidebar" id="menu-sidebar">';
            echo '<h3 style="text-align: center; padding: 15px; color: rgb(219, 101, 21); border-bottom: 1px solid #ddd;">Menu</h3>';
            
            $stmt = $pdo->prepare("SELECT * FROM menu_categories WHERE restaurant_id = ?");
            $stmt->execute([$restaurant_id]);
            $categories = $stmt->fetchAll();
            
            if ($categories) {
                foreach ($categories as $category) {
                    echo '<div class="sidebar-category" onclick="scrollToCategory(' . $category['id'] . ')">';
                    echo htmlspecialchars($category['name']);
                    echo '</div>';
                }
            } else {
                echo '<div style="padding: 15px;">No categories available</div>';
            }
            echo '</div>';

            // Offers sidebar
            echo '<div class="sidebar" id="offers-sidebar">';
            echo '<h3 style="text-align: center; padding: 15px; color: rgb(219, 101, 21); border-bottom: 1px solid #ddd;">Offers</h3>';
            
            $stmt = $pdo->prepare("SELECT * FROM offers WHERE restaurant_id = ?");
            $stmt->execute([$restaurant_id]);
            $offers = $stmt->fetchAll();
            
            if ($offers) {
                foreach ($offers as $offer) {
                    echo '<div class="sidebar-offer" onclick="scrollToOffer(' . $offer['id'] . ')">';
                    echo htmlspecialchars($offer['description']);
                    echo '<span class="valid-until">Valid until: ' . htmlspecialchars($offer['valid_until']) . '</span>';
                    echo '</div>';
                }
            } else {
                echo '<div style="padding: 15px;">No offers available</div>';
            }
            echo '</div>';
            echo '</div>';

            // Main restaurant content
            echo '<div class="restaurant-container">';
            echo '<div class="restaurant-content">';
            
            $stmt = $pdo->prepare("SELECT * FROM menu_categories WHERE restaurant_id = ?");
            $stmt->execute([$restaurant_id]);
            $categories = $stmt->fetchAll();
                 
            if ($categories) {
                echo '<h3 style="background: linear-gradient(45deg, #ff6b6b, #5f27cd, #1dd1a1); background-clip: text; color: transparent; font-size: 2rem; font-weight: 700; text-align: left;">Menu</h3>';

                echo '<div style="display: flex; gap: 10px; margin-bottom: 20px;">';
                
                echo '<button class="view-menu-btn" onclick="toggleSidebar()" style="
                    border: none; 
                    background-color: rgb(199, 68, 133); 
                    padding: 10px 15px; 
                    cursor: pointer; 
                    width: auto; 
                    text-align: center; 
                    border-radius: 8px;
                    transition: background-color 0.3s;
                    font-family: Arial, sans-serif;
                    display: flex;
                    align-items: center;
                    gap: 10px;">';
                
                echo '<i class="fas fa-bars" style="font-size: 18px; color: #5f27cd; transition: transform 0.3s;"></i>';
                echo '<span style="color: #333; font-size: 16px;">Menu Sections</span>';
                echo '</button>';

                echo '<button class="view-offers-btn" onclick="toggleOffersSection()" style="
                    border: none; 
                    background-color: rgb(199, 68, 133); 
                    padding: 10px 15px; 
                    cursor: pointer; 
                    width: auto; 
                    text-align: center; 
                    border-radius: 8px;
                    transition: background-color 0.3s;
                    font-family: Arial, sans-serif;
                    display: flex;
                    align-items: center;
                    gap: 10px;">';
                
                echo '<i class="fas fa-gift" style="font-size: 18px; color: #5f27cd; transition: transform 0.3s;"></i>';
                echo '<span style="color: #333; font-size: 16px;">View Offers</span>';
                echo '</button>';

                echo '</div>';

                // Offers section below the button
                echo '<div class="offers-section" id="offers-section">';
                $stmt = $pdo->prepare("SELECT * FROM offers WHERE restaurant_id = ?");
                $stmt->execute([$restaurant_id]);
                $offers = $stmt->fetchAll();
                if ($offers) {
                    foreach ($offers as $offer) {
                        echo '<div class="offer-card">';
                        echo '<h4>' . htmlspecialchars($offer['description']) . '</h4>';
                        if (isset($offer['discount']) && $offer['discount'] !== null && $offer['discount'] !== '') {
                            echo '<p>Discount: ' . htmlspecialchars($offer['discount']) . '%</p>';
                        }
                        echo '<div class="valid-until">Valid until: ' . htmlspecialchars($offer['valid_until']) . '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p style="text-align: center; color: #7f8c8d;">No offers available at the moment.</p>';
                }
                echo '</div>';

                foreach ($categories as $index => $category) {
                    $colorClass = ['primary', 'success', 'warning', 'danger', 'purple', 'teal'][$index % 6];
                    echo '<div class="menu-category">';
                    echo '<div class="section-name ' . $colorClass . '" data-category-id="' . $category['id'] . '">' . htmlspecialchars($category['name']) . '</div>';
                    
                    if (!empty($category['description'])) {
                        echo '<p>' . htmlspecialchars($category['description']) . '</p>';
                    }
                    
                    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE category_id = ?");
                    $stmt->execute([$category['id']]);
                    $items = $stmt->fetchAll();
                    
                    if ($items) {
                        echo '<div class="menu-items-grid">';
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
                            echo '<p class="menu-item-price"> ৳' . number_format($item['price'], 2) . '</p>';
                            
                            echo '<p class="menu-item-stock">';
                            if ($item['stock'] > 0) {
                                echo '<i class="fas fa-box"></i> In Stock: ' . $item['stock'];
                            } else {
                                echo '<i class="fas fa-exclamation-circle"></i> Items out of stock';
                            }
                            echo '</p>';
                            
                            if ($item['stock'] > 0) {
                                echo '<button class="add-to-cart-btn" onclick="addToCart(' . $item['id'] . ', \'' . htmlspecialchars(addslashes($item['name'])) . '\', ' . $item['price'] . ', ' . $item['stock'] . ')">';
                                echo '<i class="fas fa-plus"></i> Add to Order';
                                echo '</button>';
                            } else {
                                echo '<button class="add-to-cart-btn disabled" disabled>';
                                echo '<i class="fas fa-ban"></i> Out of Stock';
                                echo '</button>';
                            }
                            
                            echo '</div>';
                            echo '</div>';
                        }
                        echo '</div>';
                    } else {
                        echo '<p>No items in this category yet.</p>';
                    }
                    
                    echo '</div>';
                }
            } else {
                echo '<p>No menu categories available yet.</p>';
            }
            
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            
            echo '<input type="hidden" id="restaurant-id" value="' . $restaurant_id . '">';
            echo '<input type="hidden" id="restaurant-name" value="' . htmlspecialchars($restaurant['name']) . '">';
        } else {
            echo '<div class="container"><p>Restaurant not found.</p></div>';
        }
    } else {
        ?>
        <div class="container">
            <div class="restaurants-container">
                <div class="section-title">
                    <h2>The List of Restaurants</h2>
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
        </div>
        <?php
    }
    ?>
    
    <script>
        let originalRestaurants = [];
        let restaurantCards = [];
        let searchTimeout;
        
        function initializeSearch() {
            const restaurantContainer = document.querySelector('.restaurants-horizontal-scroll');
            if (restaurantContainer) {
                restaurantCards = Array.from(restaurantContainer.querySelectorAll('.restaurant-card-horizontal'));
                originalRestaurants = restaurantCards.map(card => card.cloneNode(true));
            }
            
            const searchInput = document.getElementById('restaurant-search');
            if (searchInput) {
                searchInput.addEventListener('input', handleSearchInput);
                searchInput.addEventListener('keydown', clearSearchIfEmpty);
            }
        }
        
        function handleSearchInput() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchTerm = document.getElementById('restaurant-search').value.trim().toLowerCase();
                if (document.getElementById('restaurant-id')) {
                    searchMenuItems(searchTerm);
                } else {
                    searchRestaurants(searchTerm);
                }
            }, 300);
        }
        
        function clearSearchIfEmpty(event) {
            if ((event.key === 'Backspace' || event.key === 'Delete') && 
                document.getElementById('restaurant-search').value.trim() === '') {
                clearTimeout(searchTimeout);
                handleSearchInput();
            }
        }
        
        function searchRestaurants(searchTerm) {
            const restaurantContainer = document.querySelector('.restaurants-horizontal-scroll');
            if (!restaurantContainer) return;
        
            const existingMessage = restaurantContainer.querySelector('.no-results');
            if (existingMessage) {
                existingMessage.remove();
            }
        
            if (!searchTerm) {
                restaurantContainer.innerHTML = '';
                originalRestaurants.forEach(card => {
                    restaurantContainer.appendChild(card.cloneNode(true));
                });
                restaurantCards = Array.from(restaurantContainer.querySelectorAll('.restaurant-card-horizontal'));
                return;
            }
        
            let found = false;
            restaurantCards.forEach(card => {
                const restaurantName = card.querySelector('h3').textContent.toLowerCase();
                if (restaurantName.includes(searchTerm)) {
                    card.style.display = 'block';
                    found = true;
                } else {
                    card.style.display = 'none';
                }
            });
        
            if (!found) {
                restaurantContainer.innerHTML = '<p class="no-results">No restaurants found matching your search.</p>';
            }
        }
        
        function searchMenuItems(searchTerm) {
            const menuItems = document.querySelectorAll('.menu-item');
            if (!menuItems.length) return;
        
            const existingMessage = document.querySelector('.no-results');
            if (existingMessage) {
                existingMessage.remove();
            }
        
            if (!searchTerm) {
                menuItems.forEach(item => {
                    item.style.display = 'flex';
                });
                return;
            }
        
            let found = false;
            menuItems.forEach(item => {
                const itemName = item.querySelector('h4').textContent.toLowerCase();
                if (itemName.includes(searchTerm)) {
                    item.style.display = 'flex';
                    found = true;
                } else {
                    item.style.display = 'none';
                }
            });
        
            if (!found) {
                const menuContainer = document.querySelector('.restaurant-content') || document.querySelector('.menu-items-grid');
                if (menuContainer) {
                    const noResults = document.createElement('p');
                    noResults.className = 'no-results';
                    noResults.textContent = 'No menu items found matching your search.';
                    menuContainer.appendChild(noResults);
                }
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            initializeSearch();
            if (restaurantId) {
                fetchStockData();
            }
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
        
        let cart = [];
        const restaurantId = document.getElementById('restaurant-id') ? document.getElementById('restaurant-id').value : null;
        const restaurantName = document.getElementById('restaurant-name') ? document.getElementById('restaurant-name').value : 'Unknown Restaurant';
        let stockData = {};
        
        function fetchStockData() {
            fetch('get_menu_items_stock.php?restaurant_id=' + restaurantId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        stockData = data.data.reduce((acc, item) => {
                            acc[item.id] = item.stock;
                            return acc;
                        }, {});
                    }
                })
                .catch(error => {
                    console.error('Error fetching stock data:', error);
                });
        }
        
        function toggleCart() {
            const cartContainer = document.getElementById('cart-container');
            cartContainer.style.display = cartContainer.style.display === 'block' ? 'none' : 'block';
            updateCartDisplay();
        }
        
        function addToCart(itemId, itemName, itemPrice, stock) {
            <?php if (!is_logged_in()): ?>
                if (confirm('You need to login to add items to cart. Would you like to login now?')) {
                    window.location.href = 'login.html';
                }
                return;
            <?php endif; ?>
            
            const currentStock = stockData[itemId] !== undefined ? stockData[itemId] : stock;
            if (currentStock <= 0) {
                alert('This item is out of stock!');
                return;
            }

            const existingItem = cart.find(item => item.id === itemId);
            
            if (existingItem) {
                if (existingItem.quantity + 1 > currentStock) {
                    alert('Cannot add more of this item. Stock limit reached!');
                    return;
                }
                existingItem.quantity += 1;
            } else {
                cart.push({
                    id: itemId,
                    name: itemName,
                    price: itemPrice,
                    quantity: 1
                });
            }
            
            updateCartDisplay();
            toggleCart();
        }
        
        function updateCartItem(itemId, change) {
            const itemIndex = cart.findIndex(item => item.id === itemId);
            
            if (itemIndex !== -1) {
                const newQuantity = cart[itemIndex].quantity + change;
                const currentStock = stockData[itemId] !== undefined ? stockData[itemId] : 0;

                if (newQuantity > currentStock) {
                    alert('Cannot add more of this item. Stock limit reached!');
                    return;
                }

                cart[itemIndex].quantity = newQuantity;
                
                if (cart[itemIndex].quantity <= 0) {
                    cart.splice(itemIndex, 1);
                }
            }
            
            updateCartDisplay();
        }
        
        function updateCartDisplay() {
            const cartItemsContainer = document.getElementById('cart-items');
            const cartCountElement = document.getElementById('cart-count');
            const cartTotalElement = document.getElementById('cart-total');
            
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            cartCountElement.textContent = totalItems;
            
            cartItemsContainer.innerHTML = '';
            
            if (cart.length === 0) {
                cartItemsContainer.innerHTML = '<p>Your cart is empty</p>';
                cartTotalElement.textContent = '0.00';
                return;
            }
            
            let total = 0;
            
            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;
                
                const itemElement = document.createElement('div');
                itemElement.className = 'cart-item';
                itemElement.innerHTML = `
                    <div>
                        <strong>${item.name}</strong>
                        <div>৳${item.price.toFixed(2)} x ${item.quantity}</div>
                    </div>
                    <div class="cart-item-controls">
                        <button onclick="updateCartItem(${item.id}, -1)">-</button>
                        <span class="cart-item-quantity">${item.quantity}</span>
                        <button onclick="updateCartItem(${item.id}, 1)">+</button>
                    </div>
                `;
                cartItemsContainer.appendChild(itemElement);
            });
            
            cartTotalElement.textContent = total.toFixed(2);
        }
        
        function showCheckoutForm() {
            <?php if (!is_logged_in()): ?>
                if (confirm('You need to login to checkout. Would you like to login now?')) {
                    window.location.href = 'login.html';
                }
                return;
            <?php endif; ?>
            
            if (cart.length === 0) {
                alert('Your cart is empty!');
                return;
            }
            
            let stockCheckPassed = true;
            let stockCheckMessage = '';
            cart.forEach(item => {
                const currentStock = stockData[item.id] !== undefined ? stockData[item.id] : 0;
                if (item.quantity > currentStock) {
                    stockCheckPassed = false;
                    stockCheckMessage += `${item.name} (Requested: ${item.quantity}, Available: ${currentStock})\n`;
                }
            });

            if (!stockCheckPassed) {
                alert('Cannot proceed to checkout due to insufficient stock:\n' + stockCheckMessage);
                return;
            }

            document.getElementById('checkout-form-container').style.display = 'flex';
            document.getElementById('checkout-restaurant-id').value = restaurantId;
            togglePaymentFields();
        }
        
        function hideCheckoutForm() {
            document.getElementById('checkout-form-container').style.display = 'none';
        }
        
        function togglePaymentFields() {
            const paymentMethod = document.getElementById('payment-method').value;
            const paymentFields = document.getElementById('payment-fields');
            const paymentNumber = document.querySelector('#payment-fields input[name="payment_number"]');
            const transactionId = document.querySelector('#payment-fields input[name="transaction_id"]');

            if (paymentMethod === 'cash_on_delivery') {
                paymentFields.classList.remove('active');
                paymentNumber.removeAttribute('required');
                transactionId.removeAttribute('required');
            } else {
                paymentFields.classList.add('active');
                paymentNumber.setAttribute('required', 'required');
                transactionId.setAttribute('required', 'required');
            }
        }

        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const orderData = {
                restaurant_id: restaurantId,
                restaurant_name: restaurantName,
                items: cart,
                delivery_address: formData.get('delivery_address'),
                special_instructions: formData.get('special_instructions'),
                payment_method: formData.get('payment_method'),
                payment_number: formData.get('payment_method') === 'cash_on_delivery' ? null : formData.get('payment_number'),
                transaction_id: formData.get('payment_method') === 'cash_on_delivery' ? null : formData.get('transaction_id'),
                user_email: '<?php echo $user["email"]; ?>',
                user_name: '<?php echo $user["name"]; ?>',
                total: parseFloat(document.getElementById('cart-total').textContent)
            };

            fetch('process_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Order placed successfully! Order ID: ' + data.order_id);
                    cart.forEach(item => {
                        if (stockData[item.id] !== undefined) {
                            stockData[item.id] -= item.quantity;
                        }
                    });
                    cart = [];
                    updateCartDisplay();
                    hideCheckoutForm();
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Failed to place order');
                }
            })
            .catch(error => {
                console.error('Order error:', error);
                alert('Error: ' + error.message);
            });
        });
        
        function toggleSidebar() {
            const menuSidebar = document.getElementById('menu-sidebar');
            if (menuSidebar) {
                menuSidebar.classList.toggle('open');
            }
        }
        
        function scrollToCategory(categoryId) {
            const categoryElement = document.querySelector(`[data-category-id="${categoryId}"]`);
            if (categoryElement) {
                categoryElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                document.querySelectorAll('.sidebar-category').forEach(el => el.classList.remove('active'));
                document.querySelector(`.sidebar-category[onclick="scrollToCategory(${categoryId})"]`).classList.add('active');
            }
        }
        
        function toggleOffersSidebar() {
            const offersSidebar = document.getElementById('offers-sidebar');
            if (offersSidebar) {
                offersSidebar.classList.toggle('open');
            }
        }
        
        function scrollToOffer(offerId) {
            const offerElement = document.querySelector(`[data-offer-id="${offerId}"]`);
            if (offerElement) {
                offerElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                document.querySelectorAll('.sidebar-offer').forEach(el => el.classList.remove('active'));
                document.querySelector(`.sidebar-offer[onclick="scrollToOffer(${offerId})"]`).classList.add('active');
            }
            toggleOffersSidebar();
        }

        function toggleOffersSection() {
            const offersSection = document.getElementById('offers-section');
            if (offersSection) {
                offersSection.classList.toggle('open');
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            if (restaurantId) {
                fetchStockData();
            }
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
            if (window.innerWidth <= 768) {
                document.addEventListener('click', function(event) {
                    const menuSidebar = document.getElementById('menu-sidebar');
                    const offersSidebar = document.getElementById('offers-sidebar');
                    const viewMenuBtn = document.querySelector('.view-menu-btn');
                    const viewOffersBtn = document.querySelector('.view-offers-btn');
                    if ((!menuSidebar.contains(event.target) && event.target !== viewMenuBtn && !viewMenuBtn.contains(event.target)) &&
                        (!offersSidebar.contains(event.target) && event.target !== viewOffersBtn && !viewOffersBtn.contains(event.target))) {
                        menuSidebar.classList.remove('open');
                        offersSidebar.classList.remove('open');
                    }
                });
            }
        });

        function toggleProfileDropdown() {
            document.getElementById('profile-dropdown').classList.toggle('show');
        }
        
        window.onclick = function(event) {
            if (!event.target.matches('.profile-icon') && !event.target.closest('.profile-icon')) {
                const dropdowns = document.getElementsByClassName('dropdown-content');
                for (let i = 0; i < dropdowns.length; i++) {
                    const openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>
</html>