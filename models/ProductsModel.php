<?php
class ProductsModel {
    private $conn;
    private $table = 'products';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllProducts() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById($productId) {
        $query = "SELECT * FROM " . $this->table . " WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createProduct($name, $description, $price, $stock, $category, $image) {
        $query = "INSERT INTO products 
                 (name, description, price, stock, category, image_url) 
                 VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $description, $price, $stock, $category, $image]);
    }

    public function updateProduct($id, $name, $description, $price, $stock, $category, $image) {
        $query = "UPDATE products 
                 SET name = ?, description = ?, price = ?, 
                     stock = ?, category = ?, image_url = ? 
                 WHERE product_id = ?";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $description, $price, $stock, $category, $image, $id]);
    }

    public function deleteProduct($id) {
        $query = "DELETE FROM products WHERE product_id = ?";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}
