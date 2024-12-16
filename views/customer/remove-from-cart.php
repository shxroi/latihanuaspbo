<?php
require_once '../../config/database.php';
require_once '../../controllers/CartController.php';

session_start();
if ($_SESSION['role'] !== 'customer') {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['cart_id'])) {
        header('Location: cart.php?error=invalid_input');
        exit();
    }

    try {
        $database = new Database();
        $cartController = new CartController($database->getConnection());
        
        $cartId = $_POST['cart_id'];
        $success = $cartController->removeFromCart($cartId);
        
        if ($success) {
            header('Location: cart.php?success=removed');
        } else {
            header('Location: cart.php?error=remove_failed');
        }
    } catch (Exception $e) {
        error_log("Error removing from cart: " . $e->getMessage());
        header('Location: cart.php?error=system_error');
    }
    exit();
}

header('Location: cart.php');
exit(); 