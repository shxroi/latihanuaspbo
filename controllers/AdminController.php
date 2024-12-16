<?php
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/ProductModel.php';

class AdminController {
    private $orderModel;
    private $productModel;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->orderModel = new OrderModel($db);
        $this->productModel = new ProductModel($db);
    }

    // Statistics Methods
    public function getTotalSales() {
        return $this->orderModel->getTotalSales();
    }

    public function getMonthlySales() {
        return $this->orderModel->getMonthlySales();
    }

    public function getDailySales($days) {
        return $this->orderModel->getDailySales($days);
    }

    public function getTopProducts($limit) {
        return $this->productModel->getTopProducts($limit);
    }

    // Order Methods
    public function getAllOrders() {
        return $this->orderModel->getAllOrders();
    }

    public function getOrdersByStatus($status) {
        return $this->orderModel->getOrdersByStatus($status);
    }

    public function updateOrderStatus($orderId, $newStatus, $notes = '') {
        return $this->orderModel->updateOrderStatus($orderId, $newStatus, $_SESSION['user_id'], $notes);
    }

    // Product Methods
    public function getAllProducts() {
        return $this->productModel->getAllProducts();
    }

    public function getProductById($id) {
        return $this->productModel->getProductById($id);
    }

    public function createProduct($data) {
        return $this->productModel->createProduct($data);
    }

    public function updateProduct($id, $data) {
        return $this->productModel->updateProduct($id, $data);
    }

    public function deleteProduct($id) {
        return $this->productModel->deleteProduct($id);
    }

    public function getTotalOrders() {
        return $this->orderModel->getTotalOrders();
    }

    public function getPendingOrders() {
        return $this->orderModel->getPendingOrders();
    }

    public function getMonthlyGrowth() {
        return $this->orderModel->getMonthlyGrowth();
    }
}
