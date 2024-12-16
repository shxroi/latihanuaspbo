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

// Get all required data
$totalSales = $adminController->getTotalSales();
$monthlySales = $adminController->getMonthlySales();
$topProducts = $adminController->getTopProducts(5);
$dailySales = $adminController->getDailySales(7);
$totalOrders = $adminController->getTotalOrders();
$pendingOrders = $adminController->getPendingOrders();
$monthlyGrowth = $adminController->getMonthlyGrowth();

// Calculate monthly sales growth
$salesGrowth = 0;
if ($monthlySales['last'] > 0) {
    $salesGrowth = (($monthlySales['current'] - $monthlySales['last']) / $monthlySales['last']) * 100;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales Statistics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-shopping-cart text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-gray-500">Total Sales</p>
                                <p class="text-2xl font-bold"><?php echo formatRupiah($totalSales); ?></p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center">
                                <span class="text-green-500 mr-2">
                                    <i class="fas fa-arrow-up"></i> 12%
                                </span>
                                <span class="text-gray-500">vs last month</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-chart-line text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-gray-500">Total Orders</p>
                                <p class="text-2xl font-bold"><?php echo $totalOrders; ?></p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center">
                                <span class="text-<?php echo $monthlyGrowth >= 0 ? 'green' : 'red'; ?>-500 mr-2">
                                    <i class="fas fa-arrow-<?php echo $monthlyGrowth >= 0 ? 'up' : 'down'; ?>"></i>
                                    <?php echo abs(round($monthlyGrowth, 1)); ?>%
                                </span>
                                <span class="text-gray-500">vs last month</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <i class="fas fa-box text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-gray-500">Pending Orders</p>
                                <p class="text-2xl font-bold"><?php echo count($pendingOrders); ?></p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center">
                                <span class="text-red-500 mr-2">
                                    <i class="fas fa-arrow-down"></i> 3%
                                </span>
                                <span class="text-gray-500">vs last month</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sales Chart -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Sales Overview</h2>
                    <canvas id="salesChart" height="100"></canvas>
                </div>

                <!-- Top Products -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Top Selling Products</h2>
                    <div class="space-y-4">
                        <?php foreach ($topProducts as $product): ?>
                            <div class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-lg transition-colors">
                                <div class="flex items-center">
                                    <img src="../uploads/products/<?php echo $product['image_url']; ?>" 
                                         alt="<?php echo $product['name']; ?>"
                                         class="w-12 h-12 object-cover rounded">
                                    <div class="ml-4">
                                        <p class="font-medium"><?php echo $product['name']; ?></p>
                                        <p class="text-sm text-gray-500"><?php echo $product['total_sold']; ?> units sold</p>
                                    </div>
                                </div>
                                <p class="font-medium"><?php echo formatRupiah($product['total_revenue']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($dailySales, 'date')); ?>,
                datasets: [{
                    label: 'Sales',
                    data: <?php echo json_encode(array_column($dailySales, 'total')); ?>,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
