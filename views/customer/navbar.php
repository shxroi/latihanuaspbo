<?php
if (!isset($cartItemCount)) {
    require_once '../../config/database.php';
    require_once '../../controllers/CustomerController.php';
    $database = new Database();
    $customerController = new CustomerController($database->getConnection());
    $cartItems = $customerController->getCartItems($_SESSION['user_id']);
    $cartItemCount = count($cartItems);
}
?>

<nav class="bg-white shadow-lg">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <a href="dashboard.php" class="flex items-center">
                    <i class="fas fa-store text-blue-600 text-2xl mr-2"></i>
                    <span class="font-bold text-xl">MyStore</span>
                </a>
            </div>

            <div class="flex items-center space-x-4">
                <a href="cart.php" class="relative">
                    <i class="fas fa-shopping-cart text-gray-600 text-xl"></i>
                    <?php if ($cartItemCount > 0): ?>
                        <span id="cartCount" 
                              class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            <?php echo $cartItemCount; ?>
                        </span>
                    <?php endif; ?>
                </a>

                <div class="relative">
                    <button onclick="toggleDropdown()" class="flex items-center space-x-2 focus:outline-none">
                        <img src="https://ui-avatars.com/api/?name=<?php echo $_SESSION['username']; ?>" 
                             alt="Profile" 
                             class="h-8 w-8 rounded-full">
                        <span class="hidden md:block"><?php echo $_SESSION['username']; ?></span>
                        <i class="fas fa-chevron-down text-sm"></i>
                    </button>
                    
                    <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                        <a href="profile.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">
                            <i class="fas fa-user mr-2"></i> Profile
                        </a>
                        <a href="orders.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">
                            <i class="fas fa-shopping-bag mr-2"></i> My Orders
                        </a>
                        <hr class="my-2">
                        <a href="../logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-100">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
function toggleDropdown() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('userDropdown');
    const button = event.target.closest('button');
    
    if (!button && !dropdown.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});
</script> 