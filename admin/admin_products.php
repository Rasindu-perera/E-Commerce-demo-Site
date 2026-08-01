<?php
session_start();
require_once '../config/db.php';

// Auth check
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

// Auto-migrate: ensure image_path and discount_percentage exist in products
try {
    $pdo->exec("ALTER TABLE products ADD COLUMN image_path VARCHAR(255) DEFAULT NULL AFTER category");
} catch (PDOException $e) {}

try {
    $pdo->exec("ALTER TABLE products ADD COLUMN discount_percentage INT DEFAULT 0 AFTER selling_price");
} catch (PDOException $e) {}

$successMsg = '';
$errorMsg = '';

// Handle Delete Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $productId = (int)$_POST['product_id'];
    
    // Check if product is referenced in order_items before deleting
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM order_items WHERE product_id = ?");
    $checkStmt->execute([$productId]);
    $count = $checkStmt->fetchColumn();
    
    if ($count > 0) {
        $errorMsg = "Cannot delete this product because it has been ordered by customers. Consider marking it out of stock instead.";
    } else {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        if ($stmt->execute([$productId])) {
            $successMsg = "Product deleted successfully.";
        } else {
            $errorMsg = "Failed to delete product.";
        }
    }
}

// Fetch all products
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();

$pageTitle = "Manage Products";
$activeMenu = "products";

ob_start();
?>

<div class="p-6 md:p-8 relative z-10 w-full max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-white mb-1">Products</h2>
            <p class="text-slate-400 text-sm">Manage your store's inventory and product details.</p>
        </div>
        <button onclick="openProductModal()" class="btn-gradient bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium px-5 py-2.5 rounded-lg shadow-lg shadow-indigo-500/25 flex items-center space-x-2 transition-transform hover:-translate-y-0.5">
            <i class="fa-solid fa-plus text-sm"></i>
            <span>Add New Product</span>
        </button>
    </div>

    <?php if ($successMsg): ?>
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl mb-6 text-sm flex items-center">
            <i class="fa-solid fa-circle-check mr-2 text-lg"></i> <?= htmlspecialchars($successMsg) ?>
        </div>
    <?php endif; ?>
    
    <?php if ($errorMsg): ?>
        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 px-4 py-3 rounded-xl mb-6 text-sm flex items-center">
            <i class="fa-solid fa-circle-exclamation mr-2 text-lg"></i> <?= htmlspecialchars($errorMsg) ?>
        </div>
    <?php endif; ?>

    <div class="glass-panel rounded-2xl border border-slate-700/50 shadow-xl overflow-hidden">
        <!-- Table Toolbar -->
        <div class="p-4 border-b border-slate-700/50 flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-800/30">
            <div class="relative w-full sm:w-64">
                <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="searchInput" placeholder="Search products..." class="w-full bg-slate-900 border border-slate-700/50 text-sm text-white rounded-lg pl-9 pr-4 py-2 outline-none focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/50 transition-all">
            </div>
            <div class="flex items-center space-x-2 w-full sm:w-auto">
                <select id="categoryFilter" class="bg-slate-900 border border-slate-700/50 text-sm text-slate-300 rounded-lg px-3 py-2 outline-none focus:border-indigo-500/50 transition-all">
                    <option value="">All Categories</option>
                    <option value="Smartphones">Smartphones</option>
                    <option value="Laptops">Laptops</option>
                    <option value="Accessories">Accessories</option>
                    <option value="Audio">Audio</option>
                </select>
                <button class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-3 py-2 rounded-lg border border-slate-700/50 transition-colors">
                    <i class="fa-solid fa-filter text-xs"></i>
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-800/50 border-b border-slate-700/50 text-xs uppercase tracking-wider text-slate-400">
                        <th class="py-4 px-6 font-semibold">ID</th>
                        <th class="py-4 px-6 font-semibold">Product Info</th>
                        <th class="py-4 px-6 font-semibold">Category</th>
                        <th class="py-4 px-6 font-semibold text-right">Cost Price</th>
                        <th class="py-4 px-6 font-semibold text-right">Selling Price</th>
                        <th class="py-4 px-6 font-semibold text-right">Discount</th>
                        <th class="py-4 px-6 font-semibold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-800/50">
                    <?php if(empty($products)): ?>
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">No products found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                        <tr class="hover:bg-slate-800/30 transition-colors group product-row" data-category="<?= htmlspecialchars($product['category']) ?>">
                            <td class="py-3 px-6 text-slate-400">#<?= $product['id'] ?></td>
                            <td class="py-3 px-6">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-lg bg-slate-800 border border-slate-700/50 flex items-center justify-center mr-3 overflow-hidden">
                                        <?php if(!empty($product['image_path'])): ?>
                                            <img src="../<?= htmlspecialchars($product['image_path']) ?>" alt="Product" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <img src="https://picsum.photos/seed/<?= $product['id'] ?>/100/100" alt="Product" class="w-full h-full object-cover">
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <p class="text-white font-medium group-hover:text-indigo-300 transition-colors product-name"><?= htmlspecialchars($product['name']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-6">
                                <span class="bg-slate-800 text-slate-300 text-[10px] font-medium px-2.5 py-1 rounded-md border border-slate-700/50">
                                    <?= htmlspecialchars($product['category']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-6 text-right text-slate-400 font-medium">
                                $<?= number_format($product['cost_price'], 2) ?>
                            </td>
                            <td class="py-3 px-6 text-right text-emerald-400 font-bold">
                                $<?= number_format($product['selling_price'], 2) ?>
                            </td>
                            <td class="py-3 px-6 text-right text-rose-400 font-bold">
                                <?= $product['discount_percentage'] ?? 0 ?>%
                            </td>
                            <td class="py-3 px-6">
                                    <button type="button" 
                                            onclick="openProductModal({
                                                id: <?= $product['id'] ?>,
                                                name: '<?= htmlspecialchars($product['name']) ?>',
                                                category: '<?= htmlspecialchars($product['category']) ?>',
                                                cost_price: <?= $product['cost_price'] ?>,
                                                selling_price: <?= $product['selling_price'] ?>,
                                                discount_percentage: <?= $product['discount_percentage'] ?? 0 ?>,
                                                description: `<?= htmlspecialchars($product['description'] ?? '') ?>`,
                                                image_path: '<?= htmlspecialchars($product['image_path'] ?? '') ?>'
                                            })" 
                                            class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 hover:bg-indigo-500 hover:text-white transition-colors flex items-center justify-center" title="Edit">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </button>
                                    <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                        <button type="submit" name="delete_product" class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-400 hover:bg-rose-500 hover:text-white transition-colors flex items-center justify-center" title="Delete">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination (Dummy) -->
        <div class="p-4 border-t border-slate-700/50 flex items-center justify-between bg-slate-800/30">
            <p class="text-xs text-slate-400">Showing <span class="font-medium text-white">1</span> to <span class="font-medium text-white"><?= count($products) ?></span> of <span class="font-medium text-white"><?= count($products) ?></span> results</p>
            <div class="flex space-x-1">
                <button class="px-3 py-1 text-sm bg-slate-800 border border-slate-700/50 text-slate-400 rounded-lg hover:text-white transition-colors disabled:opacity-50" disabled>Prev</button>
                <button class="px-3 py-1 text-sm bg-indigo-500 text-white rounded-lg shadow-sm">1</button>
                <button class="px-3 py-1 text-sm bg-slate-800 border border-slate-700/50 text-slate-400 rounded-lg hover:text-white transition-colors disabled:opacity-50" disabled>Next</button>
            </div>
        </div>
    </div>
</div>

<!-- Product Add/Edit Modal -->
<div id="productModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-lg max-h-[90vh] overflow-hidden flex flex-col shadow-2xl relative">
        <form id="productForm" onsubmit="submitProductForm(event)" enctype="multipart/form-data">
            <div class="p-6 border-b border-slate-700 flex justify-between items-center bg-slate-800/50">
                <h3 class="text-xl font-bold text-white" id="modalTitle">Add New Product</h3>
                <button type="button" onclick="closeProductModal()" class="text-slate-400 hover:text-white transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto space-y-4">
                <input type="hidden" id="productId" name="id" value="0">
                <input type="hidden" id="existingImage" name="existing_image" value="">
                
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Product Name</label>
                    <input type="text" id="productName" name="name" required class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-2 text-white outline-none focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Category</label>
                    <select id="productCategory" name="category" required class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-2 text-white outline-none focus:border-indigo-500">
                        <option value="Smartphones">Smartphones</option>
                        <option value="Laptops">Laptops</option>
                        <option value="Accessories">Accessories</option>
                        <option value="Audio">Audio</option>
                    </select>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Cost Price ($)</label>
                        <input type="number" step="0.01" id="costPrice" name="cost_price" required class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-2 text-white outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Selling Price ($)</label>
                        <input type="number" step="0.01" id="sellingPrice" name="selling_price" required class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-2 text-white outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Discount (%)</label>
                        <input type="number" min="0" max="100" id="discountPercentage" name="discount_percentage" required value="0" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-2 text-white outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Description</label>
                    <textarea id="productDescription" name="description" rows="3" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-2 text-white outline-none focus:border-indigo-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Product Image</label>
                    <input type="file" id="productImage" name="product_image" accept="image/*" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-2 text-white outline-none file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-500/20 file:text-indigo-400 hover:file:bg-indigo-500/30">
                    <p class="text-xs text-slate-500 mt-1">Leave empty to keep current image (if editing).</p>
                </div>
            </div>
            <div class="p-6 border-t border-slate-700 bg-slate-800/50 flex justify-end space-x-3">
                <button type="button" onclick="closeProductModal()" class="px-5 py-2 rounded-lg font-medium text-slate-300 hover:text-white hover:bg-slate-700 transition-colors">Cancel</button>
                <button type="submit" id="saveProductBtn" class="bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2 rounded-lg font-medium shadow-lg transition-colors">Save Product</button>
            </div>
        </form>
    </div>
</div>

<script>
    // 1. Search & Filter
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const rows = document.querySelectorAll('.product-row');

    function filterTable() {
        const query = searchInput.value.toLowerCase();
        const category = categoryFilter.value;
        
        rows.forEach(row => {
            const name = row.querySelector('.product-name').innerText.toLowerCase();
            const rowCategory = row.getAttribute('data-category');
            
            const matchesSearch = name.includes(query);
            const matchesCategory = category === '' || rowCategory === category;
            
            if (matchesSearch && matchesCategory) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterTable);
    categoryFilter.addEventListener('change', filterTable);

    // 2. Add / Edit Modal Logic
    const modal = document.getElementById('productModal');
    
    function openProductModal(product = null) {
        if (product) {
            document.getElementById('modalTitle').innerText = "Edit Product";
            document.getElementById('productId').value = product.id;
            document.getElementById('productName').value = product.name;
            document.getElementById('productCategory').value = product.category;
            document.getElementById('costPrice').value = product.cost_price;
            document.getElementById('sellingPrice').value = product.selling_price;
            document.getElementById('discountPercentage').value = product.discount_percentage;
            document.getElementById('productDescription').value = product.description || '';
            document.getElementById('existingImage').value = product.image_path || '';
        } else {
            document.getElementById('modalTitle').innerText = "Add New Product";
            document.getElementById('productForm').reset();
            document.getElementById('productId').value = "0";
            document.getElementById('discountPercentage').value = "0";
            document.getElementById('existingImage').value = "";
        }
        modal.classList.remove('hidden');
    }

    function closeProductModal() {
        modal.classList.add('hidden');
    }

    // 3. Form Submit (AJAX with FormData for File Upload)
    function submitProductForm(e) {
        e.preventDefault();
        const btn = document.getElementById('saveProductBtn');
        btn.innerText = "Saving...";
        btn.disabled = true;

        const form = document.getElementById('productForm');
        const formData = new FormData(form);

        fetch('../api/save_product.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload(); // Reload to see the new image and data
            } else {
                alert("Error: " + data.message);
                btn.innerText = "Save Product";
                btn.disabled = false;
            }
        })
        .catch(err => {
            alert("Network error occurred.");
            btn.innerText = "Save Product";
            btn.disabled = false;
        });
    }
</script>

<?php
$content = ob_get_clean();
require_once 'admin_layout.php';
?>
