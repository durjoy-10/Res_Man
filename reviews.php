<?php require 'check_auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Reviews</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="static/css/review.css">
    <link rel="stylesheet" href="static/css/Index.css">
</head>
<body>
    <header>
        <h1>Reviews & Ratings</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="cooking_video.html">Cooking Videos</a>
            <a href="about.html">About</a>
            <a href="contact.html">Contact</a>
            <a href="reviews.php">Reviews</a>
        </nav>
    </header>

    <div class="container">
        <!-- Sidebar with restaurant list -->
        <div class="sidebar" id="sidebar">
            <h3>Restaurants</h3>
            <div class="restaurant-list" id="restaurant-list">
                <!-- Dynamically populated -->
            </div>
        </div>

        <!-- Main content area -->
        <div class="main-content">
            <!-- Filter Section -->
            <div class="filter-section">
                <label for="filter-restaurant">Filter by Restaurant:</label>
                <select id="filter-restaurant">
                    <option value="all">All Restaurants</option>
                    <?php
                    require 'db_connection.php';
                    $stmt = $pdo->query("SELECT id, name FROM restaurants ORDER BY name");
                    while ($restaurant = $stmt->fetch()) {
                        echo '<option value="' . $restaurant['id'] . '">' . htmlspecialchars($restaurant['name']) . '</option>';
                    }
                    ?>
                </select>
                <button class="reset-filter" onclick="resetFilter()">Reset Filter</button>
            </div>

            <!-- Review Form -->
            <div class="review-form-section">
                <h2>Submit Your Review</h2>
                <form id="review-form">
                    <div class="form-group">
                        <label for="restaurant-select">Select Restaurant:</label>
                        <select id="restaurant-select" name="restaurant_id" required>
                            <option value="">-- Select a Restaurant --</option>
                            <?php
                            $stmt = $pdo->query("SELECT id, name FROM restaurants ORDER BY name");
                            while ($restaurant = $stmt->fetch()) {
                                echo '<option value="' . $restaurant['id'] . '">' . htmlspecialchars($restaurant['name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Your Rating:</label>
                        <div class="star-rating">
                            <input type="radio" id="star5" name="rating" value="5" checked>
                            <label for="star5"><i class="fas fa-star"></i></label>
                            <input type="radio" id="star4" name="rating" value="4">
                            <label for="star4"><i class="fas fa-star"></i></label>
                            <input type="radio" id="star3" name="rating" value="3">
                            <label for="star3"><i class="fas fa-star"></i></label>
                            <input type="radio" id="star2" name="rating" value="2">
                            <label for="star2"><i class="fas fa-star"></i></label>
                            <input type="radio" id="star1" name="rating" value="1">
                            <label for="star1"><i class="fas fa-star"></i></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="review-text">Your Review:</label>
                        <textarea id="review-text" name="review_text" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Submit Review</button>
                </form>
            </div>

            <!-- All Reviews Display -->
            <div class="all-reviews-section">
                <h2>Restaurant Reviews</h2>
                <div id="reviews-container">
                    <!-- Dynamically populated -->
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load all reviews on page load
            loadAllReviews();
            
            // Populate sidebar with restaurants
            populateSidebar();

            // Handle form submission
            document.getElementById('review-form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('submit_review.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Thank you for your review!');
                        document.getElementById('review-form').reset();
                        loadAllReviews();
                        populateSidebar();
                    } else {
                        throw new Error(data.message || 'Failed to submit review');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error: ' + error.message);
                });
            });

            // Handle filter change
            document.getElementById('filter-restaurant').addEventListener('change', function() {
                filterReviews(this.value);
            });
        });

        function loadAllReviews() {
            fetch('get_all_reviews.php')
                .then(response => response.json())
                .then(data => {
                    window.allReviews = data; // Store all reviews for filtering
                    displayReviews(data);
                })
                .catch(error => {
                    console.error('Error loading reviews:', error);
                    document.getElementById('reviews-container').innerHTML = 
                        '<div class="no-reviews">Error loading reviews. Please try again later.</div>';
                });
        }

        function filterReviews(restaurantId) {
            if (restaurantId === 'all') {
                displayReviews(window.allReviews);
                return;
            }
            
            const filteredReviews = window.allReviews.filter(review => {
                return review.restaurant_id == restaurantId;
            });
            
            displayReviews(filteredReviews);
        }

        function resetFilter() {
            document.getElementById('filter-restaurant').value = 'all';
            displayReviews(window.allReviews);
        }

        function displayReviews(reviews) {
            const container = document.getElementById('reviews-container');
            container.innerHTML = '';
            
            if (reviews.length === 0) {
                container.innerHTML = '<div class="no-reviews">No reviews found for the selected restaurant.</div>';
                return;
            }
            
            // Group reviews by restaurant
            const reviewsByRestaurant = {};
            reviews.forEach(review => {
                if (!reviewsByRestaurant[review.restaurant_id]) {
                    reviewsByRestaurant[review.restaurant_id] = {
                        name: review.restaurant_name,
                        reviews: [],
                        avgRating: 0
                    };
                }
                reviewsByRestaurant[review.restaurant_id].reviews.push(review);
            });
            
            // Calculate average ratings
            for (const restaurantId in reviewsByRestaurant) {
                const restaurant = reviewsByRestaurant[restaurantId];
                const total = restaurant.reviews.reduce((sum, review) => sum + parseInt(review.rating), 0);
                restaurant.avgRating = (total / restaurant.reviews.length).toFixed(1);
            }
            
            // Display reviews
            for (const restaurantId in reviewsByRestaurant) {
                const restaurant = reviewsByRestaurant[restaurantId];
                
                const restaurantSection = document.createElement('div');
                restaurantSection.className = 'restaurant-review-section';
                restaurantSection.id = `restaurant-${restaurantId}`;
                
                restaurantSection.innerHTML = `
                    <h3>${restaurant.name}</h3>
                    <div class="restaurant-rating">
                        <span class="avg-rating">${restaurant.avgRating}</span>
                        <div class="stars">
                            ${generateStarIcons(restaurant.avgRating)}
                        </div>
                        <span class="review-count">(${restaurant.reviews.length} reviews)</span>
                    </div>
                    <div class="reviews-list" id="reviews-list-${restaurantId}">
                        <!-- Reviews will be added here -->
                    </div>
                `;
                
                container.appendChild(restaurantSection);
                
                const reviewsList = document.getElementById(`reviews-list-${restaurantId}`);
                restaurant.reviews.forEach(review => {
                    const reviewElement = document.createElement('div');
                    reviewElement.className = 'review-item';
                    reviewElement.innerHTML = `
                        <div class="review-header">
                            <span class="review-user">${review.user_name}</span>
                            <div class="review-rating">
                                ${generateStarIcons(review.rating)}
                            </div>
                            <span class="review-date">${formatDate(review.created_at)}</span>
                        </div>
                        <div class="review-text">${review.review_text}</div>
                    `;
                    reviewsList.appendChild(reviewElement);
                });
            }
        }

        function populateSidebar() {
            fetch('get_restaurants_with_ratings.php')
                .then(response => response.json())
                .then(data => {
                    const sidebar = document.getElementById('restaurant-list');
                    sidebar.innerHTML = '';
                    
                    data.forEach(restaurant => {
                        const item = document.createElement('div');
                        item.className = 'sidebar-restaurant';
                        item.innerHTML = `
                            <a href="#restaurant-${restaurant.id}" onclick="highlightRestaurant(${restaurant.id})">
                                ${restaurant.name}
                                <div class="sidebar-rating">
                                    ${generateStarIcons(restaurant.avg_rating || 0)}
                                    <span>${restaurant.review_count || 0}</span>
                                </div>
                            </a>
                        `;
                        sidebar.appendChild(item);
                    });
                });
        }

        function highlightRestaurant(restaurantId) {
            document.querySelectorAll('.sidebar-restaurant').forEach(item => {
                item.classList.remove('active');
            });
            document.querySelector(`.sidebar-restaurant a[href="#restaurant-${restaurantId}"]`).parentNode.classList.add('active');
            
            // Set the filter dropdown
            document.getElementById('filter-restaurant').value = restaurantId;
            filterReviews(restaurantId);
            
            // Scroll to the restaurant section
            const element = document.getElementById(`restaurant-${restaurantId}`);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth' });
            }
        }

        function generateStarIcons(rating) {
            let stars = '';
            const fullStars = Math.floor(rating);
            const hasHalfStar = rating % 1 >= 0.5;
            
            for (let i = 1; i <= 5; i++) {
                if (i <= fullStars) {
                    stars += '<i class="fas fa-star"></i>';
                } else if (i === fullStars + 1 && hasHalfStar) {
                    stars += '<i class="fas fa-star-half-alt"></i>';
                } else {
                    stars += '<i class="far fa-star"></i>';
                }
            }
            return stars;
        }

        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return new Date(dateString).toLocaleDateString(undefined, options);
        }
    </script>
</body>
</html>