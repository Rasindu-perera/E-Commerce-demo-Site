<?php
session_start();
require_once '../config/db.php';

// Auth check
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

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
        <a href="#" class="btn-gradient bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium px-5 py-2.5 rounded-lg shadow-lg shadow-indigo-500/25 flex items-center space-x-2 transition-transform hover:-translate-y-0.5">
            <i class="fa-solid fa-plus text-sm"></i>
            <span>Add New Product</span>
        </a>
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
                <input type="text" placeholder="Filter products..." class="w-full bg-slate-900 border border-slate-700/50 text-sm text-white rounded-lg pl-9 pr-4 py-2 outline-none focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/50 transition-all">
            </div>
            <div class="flex items-center space-x-2 w-full sm:w-auto">
                <select class="bg-slate-900 border border-slate-700/50 text-sm text-slate-300 rounded-lg px-3 py-2 outline-none focus:border-indigo-500/50 transition-all">
                    <option>All Categories</option>
                    <option>Smartphones</option>
                    <option>Laptops</option>
                    <option>Audio</option>
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
                        <tr class="hover:bg-slate-800/30 transition-colors group">
                            <td class="py-3 px-6 text-slate-400">#<?= $product['id'] ?></td>
                            <td class="py-3 px-6">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-lg bg-slate-800 border border-slate-700/50 flex items-center justify-center mr-3 overflow-hidden">
                                        <img src="https://picsum.photos/seed/<?= $product['id'] ?>/100/100" alt="Product" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <p class="text-white font-medium group-hover:text-indigo-300 transition-colors"><?= htmlspecialchars($product['name']) ?></p>
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
                            <td class="py-3 px-6">
                                <div class="flex items-center justify-center space-x-2">
                                    <button class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 hover:bg-indigo-500 hover:text-white transition-colors flex items-center justify-center" title="Edit">
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

<?php
$content = ob_get_clean();
require_once 'admin_layout.php';
?>
