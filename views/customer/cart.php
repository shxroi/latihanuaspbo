<?php
require_once '../../config/database.php';
require_once '../../controllers/CartController.php';

session_start();
if ($_SESSION['role'] !== 'customer') {
    header('Location: ../login.php');
    exit();
}

$database = new Database();
$cartController = new CartController($database->getConnection());
$cartItems = $cartController->getCartItems($_SESSION['user_id']);

function formatRupiah($price) {
    return "IDR " . number_format((int)$price, 0, '', ',');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Navbar (sama seperti dashboard) -->

    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Shopping Cart</h1>

        <?php if (empty($cartItems)): ?>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600">Your cart is empty</p>
                <a href="dashboard.php" class="mt-4 inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Continue Shopping
                </a>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php 
                        $grandTotal = 0;
                        foreach ($cartItems as $item): 
                            $total = $item['price'] * $item['quantity'];
                            $grandTotal += $total;
                        ?>
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <img src="../uploads/products/<?php echo $item['image_url']; ?>" 
                                             alt="<?php echo $item['name']; ?>"
                                             class="w-16 h-16 object-cover rounded">
                                        <span class="ml-4"><?php echo $item['name']; ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4"><?php echo formatRupiah($item['price']); ?></td>
                                <td class="px-6 py-4">
                                    <form action="update-cart.php" method="POST" class="flex items-center">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                               min="1" class="w-20 border rounded px-2 py-1">
                                        <button type="submit" class="ml-2 text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4"><?php echo formatRupiah($total); ?></td>
                                <td class="px-6 py-4">
                                    <form action="remove-from-cart.php" method="POST" class="inline">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right font-bold">Grand Total:</td>
                            <td class="px-6 py-4 font-bold"><?php echo formatRupiah($grandTotal); ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-6 flex justify-between">
                <a href="dashboard.php" class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700">
                    Continue Shopping
                </a>
                <a href="checkout.php" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Proceed to Checkout
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 