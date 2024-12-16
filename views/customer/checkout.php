<?php
require_once '../../config/database.php';
require_once '../../controllers/CartController.php';
require_once '../../controllers/OrderController.php';

session_start();
if ($_SESSION['role'] !== 'customer') {
    header('Location: ../login.php');
    exit();
}

$database = new Database();
$cartController = new CartController($database->getConnection());
$cartItems = $cartController->getCartItems($_SESSION['user_id']);

if (empty($cartItems)) {
    header('Location: cart.php');
    exit();
}

$grandTotal = 0;
foreach ($cartItems as $item) {
    $grandTotal += $item['price'] * $item['quantity'];
}

function formatRupiah($price) {
    return "IDR " . number_format((int)$price, 0, '', ',');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Navbar (sama seperti sebelumnya) -->

    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Checkout</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Form Checkout -->
            <div class="bg-white rounded-lg shadow p-6">
                <form action="process-order.php" method="POST" id="checkoutForm" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Shipping Address</label>
                        <textarea 
                            name="address" 
                            required
                            rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                        <div class="mt-2 space-y-2">
                            <div class="flex items-center">
                                <input type="radio" name="payment_method" value="cod" required class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                <label class="ml-3 block text-sm font-medium text-gray-700">Cash on Delivery</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" name="payment_method" value="transfer" required class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                <label class="ml-3 block text-sm font-medium text-gray-700">Bank Transfer</label>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button 
                            type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            Place Order
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tambahkan JavaScript untuk debugging -->
            <script>
            document.getElementById('checkoutForm').addEventListener('submit', function(e) {
                const address = document.querySelector('[name="address"]').value.trim();
                const paymentMethod = document.querySelector('[name="payment_method"]:checked');

                if (!address) {
                    e.preventDefault();
                    alert('Please enter your shipping address');
                    return;
                }

                if (!paymentMethod) {
                    e.preventDefault();
                    alert('Please select a payment method');
                    return;
                }
            });
            </script>

            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold mb-4">Order Summary</h2>
                <div class="space-y-4">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <img src="../uploads/products/<?php echo $item['image_url']; ?>" 
                                     alt="<?php echo $item['name']; ?>"
                                     class="w-16 h-16 object-cover rounded">
                                <div class="ml-4">
                                    <h3 class="font-medium"><?php echo $item['name']; ?></h3>
                                    <p class="text-gray-500">
                                        <?php echo $item['quantity']; ?> x <?php echo formatRupiah($item['price']); ?>
                                    </p>
                                </div>
                            </div>
                            <span class="font-medium">
                                <?php echo formatRupiah($item['price'] * $item['quantity']); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>

                    <div class="border-t pt-4">
                        <div class="flex justify-between font-bold">
                            <span>Total</span>
                            <span><?php echo formatRupiah($grandTotal); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 