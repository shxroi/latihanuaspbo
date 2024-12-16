<?php
require_once '../../config/database.php';
require_once '../../models/ProductsModel.php';
require_once '../../controllers/CustomerController.php';
require_once 'helpers.php';

session_start();
if ($_SESSION['role'] !== 'customer') {
    header('Location: ../login.php');
    exit();
}

$database = new Database();
$customerController = new CustomerController($database->getConnection());
$products = $customerController->getProducts();
$recentOrders = $customerController->getRecentOrders($_SESSION['user_id'], 5);

// Ambil jumlah item di cart untuk navbar
$cartItems = $customerController->getCartItems($_SESSION['user_id']);
$cartItemCount = count($cartItems);

function formatRupiah($price) {
    return "IDR " . number_format((int)$price, 0, '', ',');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../../public/css/style.css" rel="stylesheet">
    <script src="../../public/js/customer.js" defer></script>
</head>
<body class="bg-gray-50">
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <!-- Slideshow Container -->
        <div class="container mx-auto">
            <div class="relative h-[500px] overflow-hidden mb-8 rounded-xl">
                <!-- Slides -->
                <div class="mySlides absolute w-full h-full transition-all duration-300 ease-in-out opacity-0 hidden">
                    <img src="../assets/slideshow/slide1.jpg" alt="Slide 1" 
                         class="w-full h-full object-cover rounded-xl">
                    <div class="absolute inset-0 bg-gradient-to-r from-black/50 to-transparent rounded-xl">
                        <div class="flex flex-col justify-center h-full ml-20 text-white">
                            <h2 class="text-4xl font-bold mb-4 transform transition-all duration-300 translate-x-0">Special Offer</h2>
                            <p class="text-xl mb-6 transform transition-all duration-300 delay-100 translate-x-0">Discover our latest collection</p>
                        </div>
                    </div>
                </div>
                <div class="mySlides absolute w-full h-full transition-all duration-300 ease-in-out opacity-0 hidden">
                    <img src="../assets/slideshow/slide2.jpg" alt="Slide 2" class="w-full h-full object-cover rounded-xl">
                    <div class="absolute inset-0 bg-gradient-to-r from-black/50 to-transparent rounded-xl">
                        <div class="flex flex-col justify-center h-full ml-20 text-white">
                            <h2 class="text-4xl font-bold mb-4">New Arrivals</h2>
                            <p class="text-xl mb-6">Check out our latest products</p>
                        </div>
                    </div>
                </div>
                <div class="mySlides absolute w-full h-full transition-all duration-300 ease-in-out opacity-0 hidden">
                    <img src="../assets/slideshow/slide3.jpg" alt="Slide 3" class="w-full h-full object-cover rounded-xl">
                    <div class="absolute inset-0 bg-gradient-to-r from-black/50 to-transparent rounded-xl">
                        <div class="flex flex-col justify-center h-full ml-20 text-white">
                            <h2 class="text-4xl font-bold mb-4">Best Sellers</h2>
                            <p class="text-xl mb-6">Most popular items</p>
                        </div>
                    </div>
                </div>
                <div class="mySlides absolute w-full h-full transition-all duration-300 ease-in-out opacity-0 hidden">
                    <img src="../assets/slideshow/slide4.jpg" alt="Slide 4" class="w-full h-full object-cover rounded-xl">
                    <div class="absolute inset-0 bg-gradient-to-r from-black/50 to-transparent rounded-xl">
                        <div class="flex flex-col justify-center h-full ml-20 text-white">
                            <h2 class="text-4xl font-bold mb-4">Limited Edition</h2>
                            <p class="text-xl mb-6">Get them while you can</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <button onclick="plusSlides(-1)" 
                        class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 flex items-center justify-center bg-black/30 text-white rounded-full hover:bg-black/50 transition-all duration-300 z-20 hover:scale-110">
                    <i class="fas fa-chevron-left text-xl"></i>
                </button>
                <button onclick="plusSlides(1)" 
                        class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 flex items-center justify-center bg-black/30 text-white rounded-full hover:bg-black/50 transition-all duration-300 z-20 hover:scale-110">
                    <i class="fas fa-chevron-right text-xl"></i>
                </button>

                <!-- Dots -->
                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex space-x-3 z-20">
                    <button onclick="currentSlide(1)" class="dot w-4 h-4 rounded-full bg-white/50 hover:bg-white/75 transition-all duration-300 hover:scale-110"></button>
                    <button onclick="currentSlide(2)" class="dot w-4 h-4 rounded-full bg-white/50 hover:bg-white/75 transition-all duration-300 hover:scale-110"></button>
                    <button onclick="currentSlide(3)" class="dot w-4 h-4 rounded-full bg-white/50 hover:bg-white/75 transition-all duration-300 hover:scale-110"></button>
                    <button onclick="currentSlide(4)" class="dot w-4 h-4 rounded-full bg-white/50 hover:bg-white/75 transition-all duration-300 hover:scale-110"></button>
                </div>
            </div>
        </div>

        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between">
                <div class="text-white">
                    <h1 class="text-3xl font-bold mb-2">Welcome back, <?php echo $_SESSION['username']; ?>!</h1>
                    <p class="text-blue-100">Browse our latest products and special offers.</p>
                </div>
                <div class="hidden md:block">
                    <img src="../assets/welcome.png" alt="Welcome" class="h-32">
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Featured Products</h2>
                <div class="flex items-center space-x-4">
                    <select id="categoryFilter" class="border rounded-lg px-4 py-2">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select id="sortFilter" class="border rounded-lg px-4 py-2">
                        <option value="newest">Newest</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6" id="productsGrid">
                <?php foreach ($products as $product): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden product-card" 
                     data-category="<?php echo $product['category']; ?>"
                     data-price="<?php echo $product['price']; ?>">
                    <img src="../uploads/products/<?php echo $product['image_url']; ?>" 
                         alt="<?php echo $product['name']; ?>"
                         class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="font-medium text-lg mb-2"><?php echo $product['name']; ?></h3>
                        <p class="text-gray-600 text-sm mb-2"><?php echo $product['category']; ?></p>
                        <div class="flex justify-between items-center mb-3">
                            <p class="text-blue-600 font-bold"><?php echo formatRupiah($product['price']); ?></p>
                            <button onclick="addToCart(<?php echo $product['product_id']; ?>)" 
                                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                                Add to Cart
                            </button>
                        </div>
                        <a href="product-detail.php?id=<?php echo $product['product_id']; ?>" 
                           class="text-blue-600 hover:text-blue-800 text-sm block text-center py-2 border border-blue-600 rounded hover:bg-blue-50 transition-colors">
                            View Details
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Recent Orders Section -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Recent Orders</h2>
                <a href="orders.php" class="text-blue-600 hover:text-blue-800">
                    View All Orders <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>

            <?php if (!empty($recentOrders)): ?>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($recentOrders as $order): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">#<?php echo $order['order_id']; ?></td>
                            <td class="px-6 py-4"><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                            <td class="px-6 py-4"><?php echo formatRupiah($order['total_amount']); ?></td>
                            <td class="px-6 py-4">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo getStatusColor($order['current_status']); ?>">
                                    <?php echo ucfirst($order['current_status']); ?>
                                </span>
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
            <?php else: ?>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600">You haven't placed any orders yet.</p>
                <button onclick="scrollToProducts()" class="mt-4 text-blue-600 hover:text-blue-800">
                    Start Shopping Now
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- User Dropdown Menu -->
    <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2">
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
</body>
</html>
