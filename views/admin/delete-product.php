<?php
require_once '../../config/database.php';
require_once '../../controllers/AdminController.php';

session_start();
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: products.php');
    exit();
}

$database = new Database();
$adminController = new AdminController($database->getConnection());

// Get product details first to delete image
$product = $adminController->getProductById($_GET['id']);

if ($product && $adminController->deleteProduct($_GET['id'])) {
    // Delete product image if exists
    if ($product['image_url']) {
        $imagePath = '../uploads/products/' . $product['image_url'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    header('Location: products.php?success=deleted');
} else {
    header('Location: products.php?error=delete_failed');
}
exit();