<?php
require_once __DIR__ . '/../models/ProductsModel.php';
require_once __DIR__ . '/../models/CartModel.php';
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/OrderItemModel.php';

class CustomerController {
    private $productsModel;
    private $cartModel;
    private $orderModel;
    private $orderItemModel;

    public function __construct($db) {
        $this->productsModel = new ProductsModel($db);
        $this->cartModel = new CartModel($db);
        $this->orderModel = new OrderModel($db);
        $this->orderItemModel = new OrderItemModel($db);
    }

    public function getProducts() {
        return $this->productsModel->getAllProducts();
    }

    public function getProductDetails($productId) {
        return $this->productsModel->getProductById($productId);
    }

    public function addToCart($userId, $productId, $quantity) {
        return $this->cartModel->addToCart($userId, $productId, $quantity);
    }

    public function getCartItems($userId) {
        return $this->cartModel->getCartItems($userId);
    }

    public function createOrder($userId, $address, $paymentMethod) {
        $cartItems = $this->cartModel->getCartItems($userId);
        if (empty($cartItems)) {
            return false;
        }

        // Hitung total
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Buat order
        $orderId = $this->orderModel->createOrder($userId, $total, $address, $paymentMethod);
        if (!$orderId) {
            return false;
        }

        // Tambahkan items ke order_items
        foreach ($cartItems as $item) {
            $this->orderItemModel->addOrderItem(
                $orderId, 
                $item['product_id'], 
                $item['quantity'], 
                $item['price']
            );
        }

        // Kosongkan cart
        $this->cartModel->clearCart($userId);

        return $orderId;
    }

    public function getOrders($userId) {
        return $this->orderModel->getOrdersByUser($userId);
    }

    public function getOrderDetails($orderId) {
        $order = $this->orderModel->getOrderDetails($orderId);
        if ($order) {
            $order['items'] = $this->orderItemModel->getOrderItems($orderId);
        }
        return $order;
    }

    public function getOrderStatusHistory($orderId) {
        return $this->orderModel->getOrderStatusHistory($orderId);
    }

    public function getOrderItems($orderId) {
        return $this->orderModel->getOrderItems($orderId);
    }

    public function getRecentOrders($userId, $limit = 5) {
        $query = "SELECT * FROM Orders 
                  WHERE user_id = ? 
                  ORDER BY created_at DESC 
                  LIMIT $limit";
        $stmt = $this->orderModel->getConnection()->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}
