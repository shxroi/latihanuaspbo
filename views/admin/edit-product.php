<?php
require_once '../../config/database.php';
require_once '../../controllers/AdminController.php';

session_start();
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$database = new Database();
$adminController = new AdminController($database->getConnection());

if (!isset($_GET['id'])) {
    header('Location: products.php');
    exit();
}

$product = $adminController->getProductById($_GET['id']);
if (!$product) {
    header('Location: products.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle file upload
    $imagePath = $product['image_url']; // Keep existing image by default
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = '../uploads/products/';
        $imageFileName = uniqid() . '_' . basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $imageFileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            // Delete old image if exists
            if ($product['image_url'] && file_exists($uploadDir . $product['image_url'])) {
                unlink($uploadDir . $product['image_url']);
            }
            $imagePath = $imageFileName;
        }
    }

    $productData = [
        'name' => $_POST['name'],
        'description' => $_POST['description'],
        'price' => $_POST['price'],
        'stock' => $_POST['stock'],
        'category' => $_POST['category'],
        'image_url' => $imagePath,
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];

    if ($adminController->updateProduct($_GET['id'], $productData)) {
        header('Location: products.php?success=updated');
        exit();
    } else {
        $error = "Failed to update product";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <?php include 'sidebar.php'; ?>

        <div class="flex-1 flex flex-col overflow-hidden">
            <div class="p-6">
                <h1 class="text-2xl font-semibold mb-6">Edit Product</h1>

                <form action="edit-product.php?id=<?php echo $product['product_id']; ?>" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      class="max-w-2xl">
                    <div class="bg-white rounded-lg shadow p-6 space-y-6">
                        <?php if (isset($error)): ?>
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Product Name</label>
                            <input type="text" name="name" required
                                   value="<?php echo htmlspecialchars($product['name']); ?>"
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" rows="4"
                                      class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($product['description']); ?></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Price</label>
                                <input type="number" name="price" required min="0"
                                       value="<?php echo $product['price']; ?>"
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                                <input type="number" name="stock" required min="0"
                                       value="<?php echo $product['stock']; ?>"
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <input type="text" name="category"
                                   value="<?php echo htmlspecialchars($product['category']); ?>"
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Product Image</label>
                            <?php if ($product['image_url']): ?>
                                <img src="../uploads/products/<?php echo $product['image_url']; ?>" 
                                     alt="Current product image"
                                     class="w-32 h-32 object-cover mb-2 rounded">
                            <?php endif; ?>
                            <input type="file" name="image" accept="image/*"
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active"
                                   <?php echo $product['is_active'] ? 'checked' : ''; ?>
                                   class="rounded text-blue-600 focus:ring-blue-500">
                            <label for="is_active" class="ml-2 text-sm text-gray-700">Active Product</label>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <a href="products.php" 
                               class="px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-100">Cancel</a>
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Update Product
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 