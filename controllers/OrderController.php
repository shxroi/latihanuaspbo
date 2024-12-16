<?php
require_once __DIR__ . '/../models/OrderModel.php';

class OrderController {
    private $orderModel;

    public function __construct($db) {
        $this->orderModel = new OrderModel($db);
    }

    public function createOrder($userId, $total, $address, $paymentMethod) {
        return $this->orderModel->createOrder($userId, $total, $address, $paymentMethod);
    }

    public function getOrdersByUser($userId) {
        return $this->orderModel->getOrdersByUser($userId);
    }

    public function getOrderItems($orderId) {
        return $this->orderModel->getOrderItems($orderId);
    }

    public function getAllOrders() {
        return $this->orderModel->getAllOrders();
    }

    public function updateOrderStatus($orderId, $status, $userId, $notes = '') {
        return $this->orderModel->updateOrderStatus($orderId, $status, $userId, $notes);
    }
}

