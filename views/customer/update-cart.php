<?php
require_once '../../config/database.php';
require_once '../../controllers/CartController.php';

session_start();
if ($_SESSION['role'] !== 'customer') {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['cart_id']) || !isset($_POST['quantity'])) {
        header('Location: cart.php?error=invalid_input');
        exit();
    }

    try {
        $database = new Database();
        $cartController = new CartController($database->getConnection());
        
        $cartId = $_POST['cart_id'];
        $quantity = (int)$_POST['quantity'];
        
        if ($quantity < 1) {
            header('Location: cart.php?error=invalid_quantity');
            exit();
        }

        $success = $cartController->updateCartItem($cartId, $quantity);
        
        if ($success) {
            header('Location: cart.php?success=updated');
        } else {
            header('Location: cart.php?error=update_failed');
        }
    } catch (Exception $e) {
        error_log("Error updating cart: " . $e->getMessage());
        header('Location: cart.php?error=system_error');
    }
    exit();
}

header('Location: cart.php');
exit(); 