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
$orders = $adminController->getAllOrders();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Orders</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <?php include 'sidebar.php'; ?>

        <div class="flex-1 flex flex-col overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold">Manage Orders</h1>
                    <div class="flex space-x-4">
                        <select id="statusFilter" class="border rounded-lg px-4 py-2">
                            <option value="">All Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Processing">Processing</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full" id="ordersTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($orders as $order): ?>
                                <tr class="order-row" data-status="<?php echo $order['current_status']; ?>">
                                    <td class="px-6 py-4">#<?php echo $order['order_id']; ?></td>
                                    <td class="px-6 py-4"><?php echo $order['username']; ?></td>
                                    <td class="px-6 py-4"><?php echo formatRupiah($order['total_amount']); ?></td>
                                    <td class="px-6 py-4">
                                        <form action="update-order-status.php" method="POST" class="flex items-center space-x-2">
                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                            <select name="new_status" 
                                                    class="text-sm border rounded px-2 py-1 status-select 
                                                           <?php echo getStatusColor($order['current_status']); ?>">
                                                <option value="Pending" <?php echo $order['current_status'] == 'Pending' ? 'selected' : ''; ?>>
                                                    Pending
                                                </option>
                                                <option value="Processing" <?php echo $order['current_status'] == 'Processing' ? 'selected' : ''; ?>>
                                                    Processing
                                                </option>
                                                <option value="Completed" <?php echo $order['current_status'] == 'Completed' ? 'selected' : ''; ?>>
                                                    Completed
                                                </option>
                                                <option value="Cancelled" <?php echo $order['current_status'] == 'Cancelled' ? 'selected' : ''; ?>>
                                                    Cancelled
                                                </option>
                                            </select>
                                            <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                                                Update
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php echo date('d M Y', strtotime($order['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 text-right">
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
            </div>
        </div>
    </div>

    <script>
    document.getElementById('statusFilter').addEventListener('change', function() {
        const selectedStatus = this.value;
        const rows = document.querySelectorAll('.order-row');
        
        rows.forEach(row => {
            const status = row.getAttribute('data-status');
            if (selectedStatus === '' || status === selectedStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Update select color based on status
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function() {
            const statusClasses = ['bg-yellow-100', 'bg-blue-100', 'bg-green-100', 'bg-red-100'];
            statusClasses.forEach(cls => this.classList.remove(cls));
            this.classList.add(getStatusColorClass(this.value));
        });
    });

    function getStatusColorClass(status) {
        switch(status) {
            case 'Pending': return 'bg-yellow-100';
            case 'Processing': return 'bg-blue-100';
            case 'Completed': return 'bg-green-100';
            case 'Cancelled': return 'bg-red-100';
            default: return 'bg-gray-100';
        }
    }
    </script>
</body>
</html> 