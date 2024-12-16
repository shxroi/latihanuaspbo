<?php
require_once __DIR__ . '/../models/CartModel.php';
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/ProductsModel.php';

class CartController {
    private $cartModel;
    private $orderModel;
    private $productModel;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->cartModel = new CartModel($db);
        $this->orderModel = new OrderModel($db);
        $this->productModel = new ProductsModel($db);
    }

    public function addToCart($userId, $productId, $quantity) {
        return $this->cartModel->addToCart($userId, $productId, $quantity);
    }

    public function getCartItems($userId) {
        return $this->cartModel->getCartItems($userId);
    }

    public function updateQuantity($cartId, $quantity) {
        return $this->cartModel->updateQuantity($cartId, $quantity);
    }

    public function removeItem($cartId) {
        return $this->cartModel->removeItem($cartId);
    }

    public function processCheckout($userId, $address, $paymentMethod) {
        try {
            // 1. Get cart items first
            $cartItems = $this->cartModel->getCartItems($userId);
            if (empty($cartItems)) {
                throw new Exception("Cart is empty");
            }

            // 2. Calculate total
            $total = 0;
            foreach ($cartItems as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            // 3. Create order (transaction handled inside OrderModel)
            $orderId = $this->orderModel->createOrder($userId, $total, $address, $paymentMethod);
            if (!$orderId) {
                throw new Exception("Failed to create order");
            }

            // 4. Add order items
            foreach ($cartItems as $item) {
                $success = $this->orderModel->addOrderItem(
                    $orderId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['price']
                );
                if (!$success) {
                    throw new Exception("Failed to add order item");
                }
            }

            // 5. Clear cart after successful order
            if (!$this->cartModel->clearCart($userId)) {
                throw new Exception("Failed to clear cart");
            }

            return $orderId;

        } catch (Exception $e) {
            error_log("Checkout Error: " . $e->getMessage());
            throw $e; // Re-throw to be caught by process-order.php
        }
    }

    public function updateCartItem($cartId, $quantity) {
        try {
            // Verify cart item belongs to current user
            $cartItem = $this->cartModel->getCartItemById($cartId);
            if (!$cartItem || $cartItem['user_id'] != $_SESSION['user_id']) {
                return false;
            }

            // Check product stock
            $product = $this->productModel->getProductById($cartItem['product_id']);
            if (!$product || $product['stock'] < $quantity) {
                return false;
            }

            return $this->cartModel->updateCartItem($cartId, $quantity);
        } catch (Exception $e) {
            error_log("Error in updateCartItem: " . $e->getMessage());
            return false;
        }
    }

    public function removeFromCart($cartId) {
        try {
            // Verify cart item belongs to current user
            $cartItem = $this->cartModel->getCartItemById($cartId);
            if (!$cartItem || $cartItem['user_id'] != $_SESSION['user_id']) {
                return false;
            }

            return $this->cartModel->removeFromCart($cartId);
        } catch (Exception $e) {
            error_log("Error in removeFromCart: " . $e->getMessage());
            return false;
        }
    }
}
