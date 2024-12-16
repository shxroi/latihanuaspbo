<div id="productModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4" id="modalTitle">Add Product</h3>
            <form id="productForm" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="productId">
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                    <input type="text" name="name" id="productName" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                    <textarea name="description" id="productDescription" 
                              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Price</label>
                    <input type="text" name="price" id="productPrice" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" 
                           required
                           value="<?php echo isset($product) ? $product['price'] : ''; ?>"
                           oninput="this.value = this.value.replace(/\D/g, '')">
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Stock</label>
                    <input type="number" name="stock" id="productStock" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Category</label>
                    <input type="text" name="category" id="productCategory" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Product Image</label>
                    <input type="file" name="product_image" id="productImage" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" 
                           accept="image/*">
                    <div id="currentImage" class="mt-2 hidden">
                        <img src="" alt="Current product image" class="w-20 h-20 object-cover rounded">
                    </div>
                </div>
                
                <div class="flex justify-end mt-4">
                    <button type="button" onclick="closeModal()" 
                            class="mr-2 px-4 py-2 text-gray-500 hover:text-gray-700">Cancel</button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>
</div> 