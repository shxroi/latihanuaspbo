<?php
class ProductModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllProducts() {
        $query = "SELECT * FROM Products ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById($id) {
        $query = "SELECT * FROM Products WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createProduct($data) {
        $query = "INSERT INTO Products (name, description, price, stock, category, image_url, is_active) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['name'],
            $data['description'],
            $data['price'],
            $data['stock'],
            $data['category'],
            $data['image_url'],
            $data['is_active']
        ]);
    }

    public function updateProduct($id, $data) {
        $query = "UPDATE Products 
                 SET name = ?, description = ?, price = ?, stock = ?, 
                     category = ?, image_url = ?, is_active = ? 
                 WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['name'],
            $data['description'],
            $data['price'],
            $data['stock'],
            $data['category'],
            $data['image_url'],
            $data['is_active'],
            $id
        ]);
    }

    public function deleteProduct($id) {
        $query = "DELETE FROM Products WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    public function getTopProducts($limit) {
        $query = "SELECT p.*, COUNT(oi.order_item_id) as total_sold, 
                 SUM(oi.quantity * oi.price_at_time) as total_revenue
                 FROM Products p
                 LEFT JOIN Order_items oi ON p.product_id = oi.product_id
                 GROUP BY p.product_id, p.name, p.description, p.price, 
                          p.stock, p.category, p.image_url, p.is_active
                 ORDER BY total_revenue DESC
                 LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStock($productId, $quantity, $operation = 'decrease') {
        $query = "UPDATE Products 
                  SET stock = stock " . ($operation === 'decrease' ? '-' : '+') . " ? 
                  WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$quantity, $productId]);
    }

    public function checkStock($productId, $requestedQuantity) {
        $query = "SELECT stock FROM Products WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$productId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result && $result['stock'] >= $requestedQuantity;
    }
} 