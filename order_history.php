<?php
// order_history.php
require 'check_auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="static/css/Index.css">
    <link rel="stylesheet" href="static/css/order_history.css">
    <style>
        .status-pending { color: #e67e22; }
        .status-confirmed { color: #3498db; }
        .status-preparing { color: #e74c3c; }
        .status-out_for_delivery { color: #9b59b6; }
        .status-delivered { color: #2ecc71; }
        .status-cancelled { color: #95a5a6; }
        .order-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .order-items {
            margin: 10px 0;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .order-total, .order-payment {
            margin-top: 10px;
            font-weight: bold;
        }
        .order-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .no-orders {
            text-align: center;
            color: #7f8c8d;
            font-size: 18px;
        }
        .profile-dropdown {
            position: relative;
            display: inline-block;
        }
        .profile-icon {
            cursor: pointer;
            font-size: 24px;
            color: #333;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background: #fff;
            min-width: 200px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 8px;
        }
        .dropdown-content.show {
            display: block;
        }
        .dropdown-header {
            padding: 10px;
            background: #5f27cd;
            color: white;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .dropdown-content a, .dropdown-content button {
            color: #333;
            padding: 12px 16px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }
        .dropdown-content a:hover, .dropdown-content button:hover {
            background: #f1f1f1;
        }
        .logout-btn {
            color: #e74c3c;
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
            <a href="reviews.php">Reviews</a>
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
                    <a href="change_password.php"><i class="fas fa-key"></i> Change Password</a>
                    <a href="order_history.php"><i class="fas fa-history"></i> Order History</a>
                    <button class="logout-btn" onclick="window.location.href='logout.php'">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </div>
            </div>
        </nav>
    </header>

    <div class="order-container">
        <h2><i class="fas fa-history"></i> Your Orders</h2>
        
        <?php
        require 'db_connection.php';
        
        try {
            $stmt = $pdo->prepare("
                SELECT 
                    order_id, 
                    name,  -- Updated from restaurant_name to name
                    order_date, 
                    delivery_address, 
                    special_instructions, 
                    status, 
                    total_amount, 
                    payment_method, 
                    payment_number, 
                    transaction_id, 
                    items, 
                    item_count 
                FROM 
                    order_details 
                WHERE 
                    user_id = ? 
                ORDER BY 
                    order_date DESC
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($orders) {
                foreach ($orders as $order) {
                    echo '<div class="order-card">';
                    echo '<div class="order-header">';
                    echo '<div>';
                    echo '<p style="display: inline-block; background: #5f27cd; color: white; padding: 6px 12px; border-radius: 20px; font-size: 14px;">
                           <i class="fas fa-store" style="margin-right: 6px;"></i>
                           ' . htmlspecialchars($order['name']) . '
                         </p>';
                    echo '</div>';
                    echo '<div>';
                    echo '<p><i class="fas fa-calendar-alt"></i> ' . date('Y-m-d H:i:s', strtotime($order['order_date'])) . '</p>';
                    echo '<p class="status-' . $order['status'] . '"><i class="fas fa-info-circle"></i> ' . ucfirst(str_replace('_', ' ', $order['status'])) . '</p>';
                    echo '</div>';
                    echo '</div>';
                    
                    echo '<p style="display: flex; align-items: center; gap: 8px; color: #555; margin: 12px 0;">
                           <i class="fas fa-map-marker-alt" style="color: #e74c3c;"></i>
                           <span>' . htmlspecialchars($order['delivery_address'] ?? 'Address not specified') . '</span>
                         </p>';
                         
                    if (!empty($order['special_instructions'])) {
                        echo '<p style="display: flex; align-items: center; gap: 8px; color: #555;">
                               <i class="fas fa-sticky-note"></i> ' . htmlspecialchars($order['special_instructions']) . '
                             </p>';
                    }
                    
                    echo '<div class="order-items">';
                    if (!empty($order['items'])) {
                        $items = explode(', ', $order['items']);
                        foreach ($items as $item) {
                            echo '<div class="order-item">';
                            echo '<span>' . htmlspecialchars($item) . '</span>';
                            preg_match('/\((\d+) × \$([\d.]+)\)/', $item, $matches);
                            if ($matches) {
                                $quantity = (int)$matches[1];
                                $price = (float)$matches[2];
                                echo '<span>$' . number_format($quantity * $price, 2) . '</span>';
                            } else {
                                echo '<span>N/A</span>';
                            }
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No items found.</p>';
                    }
                    echo '</div>';
                    
                    echo '<div class="order-total">';
                    echo 'Total: $' . number_format($order['total_amount'], 2);
                    echo '</div>';

                    echo '<div class="order-payment">';
                    echo '<p style="display: flex; align-items: center; gap: 8px; color: #555;">
                           <i class="fas fa-credit-card"></i> Payment Method: ' . htmlspecialchars($order['payment_method'] ? ucwords(str_replace('_', ' ', $order['payment_method'])) : 'N/A') . '
                         </p>';
                    if (!empty($order['payment_number'])) {
                        echo '<p style="display: flex; align-items: center; gap: 8px; color: #555;">
                               <i class="fas fa-phone"></i> Payment Number: ' . htmlspecialchars($order['payment_number']) . '
                             </p>';
                    }
                    if (!empty($order['transaction_id'])) {
                        echo '<p style="display: flex; align-items: center; gap: 8px; color: #555;">
                               <i class="fas fa-exchange-alt"></i> Transaction ID: ' . htmlspecialchars($order['transaction_id']) . '
                             </p>';
                    }
                    echo '</div>';
                    
                    echo '</div>';
                }
            } else {
                echo '<p class="no-orders">You have no orders yet.</p>';
            }
        } catch (Exception $e) {
            error_log("Error in order_history.php: " . $e->getMessage());
            echo '<p class="no-orders">Failed to load orders: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        ?>
    </div>

    <script>
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