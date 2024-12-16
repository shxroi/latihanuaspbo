<?php
require_once '../../config/database.php';
require_once '../../controllers/CartController.php';

session_start();
if ($_SESSION['role'] !== 'customer') {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $cartController = new CartController($database->getConnection());

    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $userId = $_SESSION['user_id'];

    if ($cartController->addToCart($userId, $productId, $quantity)) {
        header('Location: cart.php?success=1');
    } else {
        header('Location: cart.php?error=1');
    }
    exit();
}

header('Location: dashboard.php');
exit(); 