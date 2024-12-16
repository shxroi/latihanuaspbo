<?php
class OrderModel {
    private $conn;
    private $table = 'orders';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createOrder($userId, $total, $address, $paymentMethod) {
        try {
            error_log("Starting order creation...");
            $this->conn->beginTransaction();
            
            $query = "INSERT INTO Orders 
                    (user_id, total_amount, shipping_address, payment_method, current_status) 
                    VALUES (?, ?, ?, ?, 'Pending')";
            
            $stmt = $this->conn->prepare($query);
            $success = $stmt->execute([
                $userId,
                $total,
                $address,
                $paymentMethod
            ]);
            
            if (!$success) {
                error_log("Order insert error: " . json_encode($stmt->errorInfo()));
                throw new Exception("Failed to insert order");
            }
            
            $orderId = $this->conn->lastInsertId();
            
            $historyQuery = "INSERT INTO Order_status_history 
                           (order_id, previous_status, new_status, changed_by, notes) 
                           VALUES (?, NULL, 'Pending', ?, 'Order created')";
            
            $historyStmt = $this->conn->prepare($historyQuery);
            if (!$historyStmt->execute([$orderId, $userId])) {
                error_log("History insert error: " . json_encode($historyStmt->errorInfo()));
                throw new Exception("Failed to create status history");
            }
            
            $this->conn->commit();
            return $orderId;
            
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Error in createOrder: " . $e->getMessage());
            throw new Exception("Failed to create order: " . $e->getMessage());
        }
    }

    public function getOrdersByUser($userId) {
        $query = "SELECT o.*, COUNT(oi.order_item_id) as total_items 
                 FROM " . $this->table . " o
                 LEFT JOIN order_items oi ON o.order_id = oi.order_id
                 WHERE o.user_id = ?
                 GROUP BY o.order_id
                 ORDER BY o.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderDetails($orderId) {
        $query = "SELECT o.*, u.username, u.email
                 FROM " . $this->table . " o
                 JOIN users u ON o.user_id = u.user_id
                 WHERE o.order_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($orderId, $status, $notes = '') {
        $query = "UPDATE " . $this->table . " 
                 SET status = ? 
                 WHERE order_id = ?";
        
        $stmt = $this->conn->prepare($query);
        if ($stmt->execute([$status, $orderId])) {
            return $this->addStatusHistory($orderId, $status, $notes);
        }
        return false;
    }

    public function addStatusHistory($orderId, $status, $notes = '') {
        $query = "INSERT INTO order_status_history 
                 (order_id, status, notes, created_at) 
                 VALUES (?, ?, ?, NOW())";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$orderId, $status, $notes]);
    }

    public function getOrderItems($orderId) {
        $query = "SELECT oi.*, p.name, p.image_url 
                 FROM Order_items oi
                 JOIN Products p ON oi.product_id = p.product_id
                 WHERE oi.order_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllOrders() {
        $query = "SELECT o.*, u.username 
                  FROM Orders o 
                  JOIN Users u ON o.user_id = u.user_id 
                  ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrders($userId) {
        $query = "SELECT o.* 
                  FROM Orders o 
                  WHERE o.user_id = ? 
                  ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateOrderStatus($orderId, $newStatus, $userId, $notes = '') {
        try {
            $this->conn->beginTransaction();

            // Get current status
            $query = "SELECT current_status FROM Orders WHERE order_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Update order status
            $query = "UPDATE Orders SET current_status = ? WHERE order_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$newStatus, $orderId]);

            // Insert into status history
            $query = "INSERT INTO Order_status_history 
                     (order_id, previous_status, new_status, changed_by, notes) 
                     VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$orderId, $order['current_status'], $newStatus, $userId, $notes]);

            // If order is cancelled, restore stock
            if ($newStatus === 'Cancelled' && $order['current_status'] !== 'Cancelled') {
                $this->restoreStock($orderId);
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function addOrderItem($orderId, $productId, $quantity, $price) {
        $query = "INSERT INTO order_items 
                 (order_id, product_id, quantity, price_at_time) 
                 VALUES (?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$orderId, $productId, $quantity, $price]);
    }

    public function getOrderStatusHistory($orderId) {
        $query = "SELECT * FROM Order_status_history 
                 WHERE order_id = ? 
                 ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentOrders($userId, $limit = 5) {
        $query = "SELECT o.*, COUNT(oi.order_item_id) as total_items 
                 FROM Orders o
                 LEFT JOIN Order_items oi ON o.order_id = oi.order_id
                 WHERE o.user_id = ?
                 GROUP BY o.order_id
                 ORDER BY o.created_at DESC
                 LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrdersByStatus($status) {
        $query = "SELECT o.*, u.username 
                  FROM Orders o 
                  JOIN Users u ON o.user_id = u.user_id 
                  WHERE o.current_status = ?
                  ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalOrders() {
        $query = "SELECT COUNT(*) as total FROM Orders";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getTotalSales() {
        $query = "SELECT COALESCE(SUM(total_amount), 0) as total 
                  FROM Orders 
                  WHERE current_status != 'Cancelled'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getMonthlySales() {
        $query = "SELECT 
            COALESCE(SUM(CASE WHEN MONTH(created_at) = MONTH(CURRENT_DATE) 
                             THEN total_amount ELSE 0 END), 0) as current,
            COALESCE(SUM(CASE WHEN MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) 
                             THEN total_amount ELSE 0 END), 0) as last
            FROM Orders 
            WHERE current_status != 'Cancelled'
            AND created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDailySales($days) {
        $query = "SELECT DATE(created_at) as date, 
                  COALESCE(SUM(total_amount), 0) as total
                  FROM Orders
                  WHERE current_status != 'Cancelled'
                  AND created_at >= DATE_SUB(CURRENT_DATE, INTERVAL ? DAY)
                  GROUP BY DATE(created_at)
                  ORDER BY date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$days]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPendingOrders() {
        $query = "SELECT * FROM Orders WHERE current_status = 'Pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMonthlyGrowth() {
        $query = "SELECT 
            (SELECT COUNT(*) FROM Orders 
             WHERE MONTH(created_at) = MONTH(CURRENT_DATE)) as current_month,
            (SELECT COUNT(*) FROM Orders 
             WHERE MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)) as last_month";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['last_month'] > 0) {
            return (($result['current_month'] - $result['last_month']) / $result['last_month']) * 100;
        }
        return 0;
    }

    public function restoreStock($orderId) {
        try {
            $this->conn->beginTransaction();

            // Get order items
            $query = "SELECT product_id, quantity FROM Order_items WHERE order_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$orderId]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $productModel = new ProductModel($this->conn);
            foreach ($items as $item) {
                // Restore stock
                $productModel->updateStock($item['product_id'], $item['quantity'], 'increase');
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}
