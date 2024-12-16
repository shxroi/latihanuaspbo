<?php
// Fungsi helper untuk format harga
function formatRupiah($price) {
    // Pastikan price adalah integer
    $price = (int)$price;
    // Format angka dengan pemisah ribuan
    return "IDR " . number_format($price, 0, '', ',');
}
?>

<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        <?php foreach ($products as $product): ?>
            <tr></tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <?php if ($product['image_url']): ?>
                        <img src="../uploads/products/<?php echo $product['image_url']; ?>" 
                             alt="<?php echo $product['name']; ?>" 
                             class="w-16 h-16 object-cover rounded">
                    <?php else: ?>
                        <div class="w-16 h-16 bg-gray-100 flex items-center justify-center rounded">
                            <i class="fas fa-image text-gray-400"></i>
                        </div>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap"><?php echo $product['name']; ?></td>
                <td class="px-6 py-4"><?php echo $product['description']; ?></td>
                <td class="px-6 py-4"><?php echo formatRupiah($product['price']); ?></td>
                <td class="px-6 py-4"><?php echo $product['stock']; ?></td>
                <td class="px-6 py-4"><?php echo $product['category']; ?></td>
                <td class="px-6 py-4 space-x-2">
                    <button onclick="showEditModal(<?php echo htmlspecialchars(json_encode($product)); ?>)" 
                            class="text-blue-600 hover:text-blue-900">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteProduct(<?php echo $product['product_id']; ?>)" 
                            class="text-red-600 hover:text-red-900">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table> 