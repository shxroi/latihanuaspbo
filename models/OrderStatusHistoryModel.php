<?php
class OrderStatusHistoryModel {
    private $conn;
    private $table = 'order_status_history';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addStatusHistory($orderId, $status, $notes = '') {
        $query = "INSERT INTO " . $this->table . " 
                 (order_id, status, notes, created_at) 
                 VALUES (?, ?, ?, NOW())";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$orderId, $status, $notes]);
    }

    public function getStatusHistory($orderId) {
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE order_id = ?
                 ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
