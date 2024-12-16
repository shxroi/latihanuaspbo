<?php
require_once '../../config/database.php';
require_once '../../models/ProductsModel.php';

session_start();
if ($_SESSION['role'] !== 'customer') {
    header('Location: ../login.php');
    exit();
}

$database = new Database();
$productsModel = new ProductsModel($database->getConnection());

// Get product ID from URL
$productId = isset($_GET['id']) ? $_GET['id'] : null;
if (!$productId) {
    header('Location: dashboard.php');
    exit();
}

$product = $productsModel->getProductById($productId);
if (!$product) {
    header('Location: dashboard.php');
    exit();
}

// Fungsi helper untuk format harga
function formatRupiah($price) {
    return "IDR " . number_format((int)$price, 0, '', ',');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $product['name']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="dashboard.php" class="text-xl font-semibold">MyShop</a>
                <div class="flex items-center space-x-4">
                    <a href="cart.php" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="ml-1">Cart</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-6">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="md:flex">
                <div class="md:flex-shrink-0">
                    <?php if ($product['image_url']): ?>
                        <img src="../uploads/products/<?php echo $product['image_url']; ?>" 
                             alt="<?php echo $product['name']; ?>"
                             class="h-96 w-full object-cover md:w-96">
                    <?php else: ?>
                        <div class="h-96 w-full md:w-96 bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 text-4xl"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="p-8">
                    <h1 class="text-2xl font-bold mb-2"><?php echo $product['name']; ?></h1>
                    <p class="text-gray-600 mb-4"><?php echo $product['description']; ?></p>
                    <p class="text-2xl font-bold text-blue-600 mb-4"><?php echo formatRupiah($product['price']); ?></p>
                    <p class="text-gray-600 mb-4">Stock: <?php echo $product['stock']; ?></p>
                    
                    <form action="add-to-cart.php" method="POST" class="space-y-4">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <div>
                            <label class="block text-gray-700 mb-2">Quantity:</label>
                            <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>"
                                   class="w-20 border rounded-md px-3 py-2">
                        </div>
                        <button type="submit" 
                                class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                            Add to Cart
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 