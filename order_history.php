<?php require 'check_auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="static/css/index.css">
    <link rel="stylesheet" href="static/css/order_history.css">
</head>
<body>
    <header>
        <h1>Order History</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="order_history.php">My Orders</a>
            <a href="cooking_video.html">Cooking Videos</a>
            <a href="about.html">About</a>
            <a href="contact.html">Contact</a>
        </nav>
    </header>

    <div class="order-container">
        <h2>Your Orders</h2>
        
        <?php
        require 'db_connection.php';
        
        $stmt = $pdo->prepare("
            SELECT 
                o.id, 
                o.order_date, 
                o.restaurant_name,
                o.delivery_address,
                o.special_instructions,
                o.status,
                o.total_amount,
                COUNT(oi.id) AS item_count
            FROM 
                orders o
            JOIN 
                order_items oi ON o.id = oi.order_id
            WHERE 
                o.user_id = ?
            GROUP BY 
                o.id
            ORDER BY 
                o.order_date DESC
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $orders = $stmt->fetchAll();
        
        if ($orders) {
            foreach ($orders as $order) {
                echo '<div class="order-card">';
                echo '<div class="order-header">';
                echo '<div>';
                echo '<p style="display: inline-block; background: #5f27cd; color: white; padding: 6px 12px; border-radius: 20px; font-size: 14px;">
                       <i class="fas fa-store" style="margin-right: 6px;"></i>
                       ' . htmlspecialchars($order['restaurant_name']) . '
                     </p>';

                echo '</div>';
                echo '<div>';
                echo '<p><i class="fas fa-calendar-alt"></i> ' . $order['order_date'] . '</p>';
                echo '<p class="status-' . $order['status'] . '"><i class="fas fa-info-circle"></i> ' . ucfirst($order['status']) . '</p>';
                echo '</div>';
                echo '</div>';
                
                echo '<p style="display: flex; align-items: center; gap: 8px; color: #555; margin: 12px 0;">
                       <i class="fas fa-map-marker-alt" style="color: #e74c3c;"></i>
                       <span>' . htmlspecialchars($order['delivery_address'] ?? 'Address not specified') . '</span>
                     </p>';
                     
                if (!empty($order['special_instructions'])) {
                    echo '<p><i class="fas fa-sticky-note"></i> ' . htmlspecialchars($order['special_instructions']) . '</p>';
                }
                
                // Get order items
                $stmtItems = $pdo->prepare("
                    SELECT 
                        mi.name,
                        oi.quantity,
                        oi.price
                    FROM 
                        order_items oi
                    JOIN 
                        menu_items mi ON oi.menu_item_id = mi.id
                    WHERE 
                        oi.order_id = ?
                ");
                $stmtItems->execute([$order['id']]);
                $items = $stmtItems->fetchAll();
                
                echo '<div class="order-items">';
                foreach ($items as $item) {
                    echo '<div class="order-item">';
                    echo '<span>' . htmlspecialchars($item['name']) . ' × ' . $item['quantity'] . '</span>';
                    echo '<span>$' . number_format($item['price'] * $item['quantity'], 2) . '</span>';
                    echo '</div>';
                }
                echo '</div>';
                
                echo '<div class="order-total">';
                echo 'Total: $' . number_format($order['total_amount'], 2);
                echo '</div>';
                
                echo '</div>';
            }
        } else {
            echo '<p>You have no orders yet.</p>';
        }
        ?>
    </div>
</body>
</html>