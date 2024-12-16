<?php
require_once '../../config/database.php';
require_once '../../controllers/CustomerController.php';
require_once 'helpers.php';

session_start();
if ($_SESSION['role'] !== 'customer') {
    header('Location: ../login.php');
    exit();
}

$database = new Database();
$customerController = new CustomerController($database->getConnection());
$orders = $customerController->getOrders($_SESSION['user_id']);

$cartItems = $customerController->getCartItems($_SESSION['user_id']);
$cartItemCount = count($cartItems);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">My Orders</h1>

        <?php if (!empty($orders)): ?>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Order Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo date('d M Y', strtotime($order['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo formatRupiah($order['total_amount']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php echo getStatusColor($order['current_status']); ?>">
                                        <?php echo ucfirst($order['current_status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="order-detail.php?id=<?php echo $order['order_id']; ?>" 
                                       class="text-blue-600 hover:text-blue-900">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600">You haven't placed any orders yet.</p>
                <a href="dashboard.php" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                    Start Shopping
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 