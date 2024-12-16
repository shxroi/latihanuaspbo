<?php
require_once '../../config/database.php';
require_once '../../controllers/AdminController.php';
require_once '../customer/helpers.php';

session_start();
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$database = new Database();
$adminController = new AdminController($database->getConnection());
$products = $adminController->getAllProducts();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Products</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <!-- Sidebar - Fixed position -->
        <div class="fixed inset-y-0">
            <?php include 'sidebar.php'; ?>
        </div>

        <!-- Main Content - Scrollable -->
        <div class="flex-1 ml-64"> <!-- ml-64 to offset the fixed sidebar width -->
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold">Products</h1>
                    <a href="add-product.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Add New Product
                    </a>
                </div>

                <!-- Products Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 pb-6">
                    <?php foreach ($products as $product): ?>
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                            <div class="aspect-w-16 aspect-h-9">
                                <img src="../uploads/products/<?php echo $product['image_url']; ?>" 
                                     alt="<?php echo $product['name']; ?>"
                                     class="w-full h-48 object-cover">
                            </div>
                            <div class="p-4">
                                <h3 class="font-medium text-lg mb-2"><?php echo $product['name']; ?></h3>
                                <p class="text-gray-600 text-sm mb-2"><?php echo $product['category']; ?></p>
                                <p class="text-blue-600 font-bold mb-4"><?php echo formatRupiah($product['price']); ?></p>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Stock: <?php echo $product['stock']; ?></span>
                                    <div class="space-x-2">
                                        <a href="edit-product.php?id=<?php echo $product['product_id']; ?>" 
                                           class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteProduct(<?php echo $product['product_id']; ?>)" 
                                                class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    function deleteProduct(productId) {
        if (confirm('Are you sure you want to delete this product?')) {
            window.location.href = `delete-product.php?id=${productId}`;
        }
    }
    </script>
</body>
</html> 