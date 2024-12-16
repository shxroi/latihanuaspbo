<?php
require_once '../../config/database.php';
require_once '../../controllers/CustomerController.php';
require_once 'helpers.php';

session_start();
if ($_SESSION['role'] !== 'customer') {
    header('Location: ../login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: orders.php');
    exit();
}

$database = new Database();
$customerController = new CustomerController($database->getConnection());
$order = $customerController->getOrderDetails($_GET['id']);

// Verifikasi order milik user yang login
if (!$order || $order['user_id'] != $_SESSION['user_id']) {
    header('Location: orders.php');
    exit();
}

$cartItems = $customerController->getCartItems($_SESSION['user_id']);
$cartItemCount = count($cartItems);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Detail #<?php echo $order['order_id']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Order #<?php echo $order['order_id']; ?></h1>
            <span class="px-3 py-1 rounded-full text-sm font-semibold
                <?php echo getStatusColor($order['current_status']); ?>">
                <?php echo ucfirst($order['current_status']); ?>
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Order Info -->
            <div class="col-span-2">
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-lg font-semibold mb-4">Order Items</h2>
                    <div class="divide-y divide-gray-200">
                        <?php 
                        $orderItems = $customerController->getOrderItems($order['order_id']);
                        foreach ($orderItems as $item): 
                        ?>
                            <div class="py-4 flex items-center justify-between">
                                <div class="flex items-center">
                                    <img src="../uploads/products/<?php echo $item['image_url']; ?>" 
                                         alt="<?php echo $item['name']; ?>"
                                         class="w-16 h-16 object-cover rounded">
                                    <div class="ml-4">
                                        <h3 class="font-medium"><?php echo $item['name']; ?></h3>
                                        <p class="text-gray-500">
                                            <?php echo $item['quantity']; ?> x <?php echo formatRupiah($item['price_at_time']); ?>
                                        </p>
                                    </div>
                                </div>
                                <span class="font-medium">
                                    <?php echo formatRupiah($item['price_at_time'] * $item['quantity']); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex justify-between font-bold">
                            <span>Total</span>
                            <span><?php echo formatRupiah($order['total_amount']); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Details -->
            <div class="col-span-1">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4">Order Details</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Order Date</h3>
                            <p><?php echo date('d M Y H:i', strtotime($order['created_at'])); ?></p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Payment Method</h3>
                            <p><?php echo ucfirst($order['payment_method']); ?></p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Shipping Address</h3>
                            <p class="whitespace-pre-line"><?php echo $order['shipping_address']; ?></p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Status History</h3>
                            <?php 
                            $statusHistory = $customerController->getOrderStatusHistory($order['order_id']);
                            foreach ($statusHistory as $history): 
                            ?>
                                <div class="text-sm text-gray-600 mt-1">
                                    <span class="font-medium">
                                        <?php echo ucfirst($history['new_status']); ?>
                                    </span>
                                    <span class="text-gray-400">
                                        - <?php echo date('d M Y H:i', strtotime($history['created_at'])); ?>
                                    </span>
                                    <?php if ($history['notes']): ?>
                                        <p class="text-gray-500 mt-1"><?php echo $history['notes']; ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 