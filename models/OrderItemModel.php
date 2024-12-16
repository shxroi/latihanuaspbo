<?php
class OrderItemModel {
    private $conn;
    private $table = 'order_items';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addOrderItem($orderId, $productId, $quantity, $price) {
        $query = "INSERT INTO " . $this->table . " 
                 (order_id, product_id, quantity, price) 
                 VALUES (?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$orderId, $productId, $quantity, $price]);
    }

    public function getOrderItems($orderId) {
        $query = "SELECT oi.*, p.name, p.image_url 
                 FROM " . $this->table . " oi
                 JOIN products p ON oi.product_id = p.product_id
                 WHERE oi.order_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 