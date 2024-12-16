<?php
class CartModel {
    private $conn;
    private $table = 'cart';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addToCart($userId, $productId, $quantity) {
        // Cek apakah produk sudah ada di cart
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE user_id = ? AND product_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId, $productId]);
        
        if ($stmt->rowCount() > 0) {
            // Update quantity jika produk sudah ada
            $query = "UPDATE " . $this->table . " 
                     SET quantity = quantity + ? 
                     WHERE user_id = ? AND product_id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$quantity, $userId, $productId]);
        } else {
            // Insert item baru jika belum ada
            $query = "INSERT INTO " . $this->table . " 
                     (user_id, product_id, quantity) 
                     VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$userId, $productId, $quantity]);
        }
    }

    public function getCartItems($userId) {
        $query = "SELECT c.*, p.name, p.price, p.image_url 
                 FROM " . $this->table . " c
                 JOIN products p ON c.product_id = p.product_id
                 WHERE c.user_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateQuantity($cartId, $quantity) {
        $query = "UPDATE " . $this->table . " 
                 SET quantity = ? 
                 WHERE cart_id = ?";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$quantity, $cartId]);
    }

    public function removeItem($cartId) {
        $query = "DELETE FROM " . $this->table . " 
                 WHERE cart_id = ?";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$cartId]);
    }

    public function clearCart($userId) {
        $query = "DELETE FROM " . $this->table . " 
                 WHERE user_id = ?";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$userId]);
    }

    public function getCartItemById($cartId) {
        $query = "SELECT c.*, p.stock 
                 FROM Cart c
                 JOIN Products p ON c.product_id = p.product_id
                 WHERE c.cart_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$cartId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateCartItem($cartId, $quantity) {
        $query = "UPDATE Cart SET quantity = ? WHERE cart_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$quantity, $cartId]);
    }

    public function removeFromCart($cartId) {
        $query = "DELETE FROM Cart WHERE cart_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$cartId]);
    }
}
