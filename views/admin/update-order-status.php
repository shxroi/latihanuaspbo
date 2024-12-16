<?php
require_once '../../config/database.php';
require_once '../../controllers/AdminController.php';

session_start();
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['order_id']) || !isset($_POST['new_status'])) {
        header('Location: orders.php?error=invalid_input');
        exit();
    }

    $database = new Database();
    $adminController = new AdminController($database->getConnection());
    
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['new_status'];
    $notes = $_POST['notes'] ?? '';
    
    if ($adminController->updateOrderStatus($orderId, $newStatus, $notes)) {
        header('Location: orders.php?success=status_updated');
    } else {
        header('Location: orders.php?error=update_failed');
    }
    exit();
}

header('Location: orders.php');
exit();