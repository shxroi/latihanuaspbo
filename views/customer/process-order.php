<?php
require_once '../../config/database.php';
require_once '../../controllers/CartController.php';

session_start();
if ($_SESSION['role'] !== 'customer') {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate input
        if (empty($_POST['address']) || empty($_POST['payment_method'])) {
            throw new Exception("Missing required fields");
        }

        $database = new Database();
        $db = $database->getConnection();
        $cartController = new CartController($db);
        
        $address = htmlspecialchars($_POST['address']);
        $paymentMethod = htmlspecialchars($_POST['payment_method']);
        $userId = $_SESSION['user_id'];

        // Process checkout
        $orderId = $cartController->processCheckout($userId, $address, $paymentMethod);
        
        if ($orderId) {
            header('Location: order-detail.php?id=' . $orderId . '&success=1');
            exit();
        } else {
            throw new Exception("Failed to process order");
        }

    } catch (Exception $e) {
        error_log("Order Processing Error: " . $e->getMessage());
        header('Location: checkout.php?error=' . urlencode($e->getMessage()));
        exit();
    }
}

header('Location: cart.php');
exit(); 