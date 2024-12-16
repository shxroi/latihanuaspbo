<div class="w-64 bg-white border-r">
    <div class="p-4 border-b">
        <div class="flex items-center">
            <i class="fas fa-store text-blue-600 text-2xl"></i>
            <span class="ml-2 text-xl font-semibold">Admin Panel</span>
        </div>
    </div>
    <nav class="p-4">
        <div class="space-y-2">
            <a href="dashboard.php" class="flex items-center text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-colors <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                <i class="fas fa-home w-6"></i>
                <span>Dashboard</span>
            </a>
            <a href="products.php" class="flex items-center text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-colors <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                <i class="fas fa-box w-6"></i>
                <span>Products</span>
            </a>
            <a href="orders.php" class="flex items-center text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-colors <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                <i class="fas fa-shopping-cart w-6"></i>
                <span>Orders</span>
            </a>
            <a href="../logout.php" class="flex items-center text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-colors">
                <i class="fas fa-sign-out-alt w-6"></i>
                <span>Logout</span>
            </a>
        </div>
    </nav>
</div> 