<?php
session_start();
require_once 'config/db.php';

try {
    // Fetch all products
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    // If table doesn't exist or other DB errors
    $products = [];
    $error = "Database Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium TechStore</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="public/js/tailwind-config.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="public/css/style.css">
    <script src="public/js/cart.js" defer></script>
</head>
<body class="antialiased min-h-screen flex flex-col selection:bg-indigo-500/30">

    <!-- Navigation Bar -->
    <nav class="glass-nav fixed w-full z-50 top-0 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0 flex items-center cursor-pointer group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mr-3 shadow-lg shadow-indigo-500/20 group-hover:shadow-indigo-500/40 transition-all duration-300">
                        <i class="fa-solid fa-store text-white text-lg"></i>
                    </div>
                    <span class="font-bold text-2xl tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-400">TechStore</span>
                </div>
                
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-8">
                        <a href="index.php" class="text-white relative group px-1 py-2 text-sm font-medium">
                            Home
                            <span class="absolute -bottom-1 left-0 w-full h-0.5 bg-indigo-500 rounded-full"></span>
                        </a>
                        <a href="products.php" class="text-slate-400 hover:text-white transition-colors relative group px-1 py-2 text-sm font-medium">
                            Products
                            <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-indigo-500 rounded-full transition-all group-hover:w-full"></span>
                        </a>
                        <a href="products.php" class="text-slate-400 hover:text-white transition-colors relative group px-1 py-2 text-sm font-medium">
                            Categories
                            <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-indigo-500 rounded-full transition-all group-hover:w-full"></span>
                        </a>
                    </div>
                </div>

                <div class="flex items-center space-x-6">
                    <button class="text-slate-400 hover:text-white transition-colors relative">
                        <i class="fa-solid fa-search text-lg"></i>
                    </button>
                    
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <div class="hidden sm:flex items-center space-x-4">
                            <a href="login.php" class="text-slate-400 hover:text-white transition-colors text-sm font-medium">Login</a>
                            <a href="register.php" class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-lg shadow-indigo-500/20">Register</a>
                        </div>
                    <?php else: ?>
                        <div class="hidden sm:flex items-center space-x-4 border-r border-slate-700/50 pr-4">
                            <span class="text-slate-300 text-sm">Hi, <span class="font-semibold text-white"><?= htmlspecialchars($_SESSION['first_name'] ?? 'User') ?></span></span>
                            <a href="logout.php" class="text-slate-400 hover:text-rose-400 transition-colors" title="Logout"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                        <a href="admin/dashboard.php" class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-lg shadow-indigo-500/20 flex items-center">
                            <i class="fa-solid fa-chart-line mr-2"></i> Admin Dashboard
                        </a>
                    <?php endif; ?>
                    <a href="cart.php" class="relative group block">
                        <div class="flex items-center space-x-2 bg-slate-800/80 hover:bg-slate-700 border border-slate-700/50 rounded-full px-5 py-2.5 transition-all group-hover:border-indigo-500/50 shadow-sm">
                            <i class="fa-solid fa-cart-shopping text-slate-300 group-hover:text-white transition-colors"></i>
                            <span class="text-sm font-medium text-white hidden sm:block">Cart</span>
                            <span class="absolute -top-1.5 -right-1.5 bg-gradient-to-r from-rose-500 to-pink-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-lg shadow-rose-500/30 border border-slate-900">0</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative pt-32 pb-16 sm:pt-40 sm:pb-24 overflow-hidden border-b border-slate-800/50">
        <!-- Abstract Background Elements -->
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-indigo-600/20 rounded-full mix-blend-screen filter blur-[100px] opacity-70"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-600/20 rounded-full mix-blend-screen filter blur-[100px] opacity-70"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
            <div class="inline-flex items-center space-x-2 bg-slate-800/50 border border-slate-700 rounded-full px-3 py-1 mb-8 shadow-sm backdrop-blur-sm cursor-pointer hover:bg-slate-800 transition-colors">
                <span class="flex h-2 w-2 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                </span>
                <span class="text-xs font-medium text-slate-300">New arrivals for Summer 2026</span>
            </div>
            
            <h1 class="text-5xl sm:text-7xl font-extrabold tracking-tight mb-6">
                <span class="block text-white mb-2">Next Generation</span>
                <span class="block bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400">Tech & Electronics</span>
            </h1>
            <p class="mt-6 max-w-2xl text-lg sm:text-xl text-slate-400 mx-auto mb-10 leading-relaxed">
                Discover the latest premium devices tailored to elevate your digital lifestyle. Exceptional quality meets stunning design aesthetics.
            </p>
            <div class="flex justify-center space-x-4">
                <button class="btn-gradient text-white font-medium px-8 py-3.5 rounded-full shadow-lg shadow-indigo-500/25 flex items-center space-x-2">
                    <span>Shop Now</span>
                    <i class="fa-solid fa-arrow-right text-sm"></i>
                </button>
                <button class="bg-slate-800/80 hover:bg-slate-700 text-white font-medium px-8 py-3.5 rounded-full border border-slate-700 transition-all flex items-center space-x-2">
                    <span>View Offers</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 w-full relative z-10">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-12 gap-4">
            <div>
                <h2 class="text-3xl font-bold text-white mb-2">Featured Products</h2>
                <p class="text-slate-400">Explore our most popular curated selections</p>
            </div>
            <div class="flex space-x-2">
                <select class="bg-slate-800/80 border border-slate-700 text-sm text-slate-300 rounded-lg px-4 py-2 outline-none focus:border-indigo-500 transition-colors appearance-none pr-8 relative">
                    <option>Latest Additions</option>
                    <option>Price: Low to High</option>
                    <option>Price: High to Low</option>
                </select>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-red-500/10 border border-red-500/50 rounded-2xl p-6 text-center max-w-2xl mx-auto">
                <i class="fa-solid fa-triangle-exclamation text-3xl text-red-400 mb-3"></i>
                <h3 class="text-lg font-medium text-white mb-1">Failed to load products</h3>
                <p class="text-red-200/70 text-sm"><?= htmlspecialchars($error) ?></p>
            </div>
        <?php elseif (empty($products)): ?>
            <div class="text-center py-24 glass-card rounded-3xl max-w-3xl mx-auto">
                <div class="w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                    <i class="fa-solid fa-box-open text-3xl text-slate-500"></i>
                </div>
                <h3 class="text-2xl font-medium text-white">No products found</h3>
                <p class="text-slate-400 mt-2 max-w-md mx-auto">Our inventory is currently being updated. Please check back later for new arrivals.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 sm:gap-8">
                <?php foreach ($products as $product): ?>
                    <div class="glass-card rounded-2xl overflow-hidden flex flex-col relative group">
                        <!-- Category Badge & Wishlist -->
                        <div class="absolute top-4 left-4 right-4 z-10 flex justify-between items-start">
                            <span class="bg-slate-900/60 backdrop-blur-md text-slate-200 text-xs font-medium px-3 py-1.5 rounded-full border border-slate-700/50">
                                <?= htmlspecialchars($product['category']) ?>
                            </span>
                            <button class="w-8 h-8 rounded-full bg-slate-900/60 backdrop-blur-md flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-slate-800 transition-all border border-slate-700/50">
                                <i class="fa-regular fa-heart"></i>
                            </button>
                        </div>
                        
                        <!-- Image -->
                        <div class="image-container h-64 w-full relative bg-slate-800 flex items-center justify-center p-4">
                            <!-- Using picsum for diverse placeholders -->
                            <img src="https://picsum.photos/seed/<?= $product['id'] ?>/600/600" alt="<?= htmlspecialchars($product['name']) ?>" class="max-h-full max-w-full object-contain drop-shadow-2xl">
                            
                            <!-- Gradient Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/20 to-transparent opacity-80"></div>
                        </div>

                        <!-- Content -->
                        <div class="p-5 flex-grow flex flex-col -mt-4 relative z-10">
                            <h3 class="text-lg font-semibold text-white mb-2 line-clamp-2 leading-snug group-hover:text-indigo-300 transition-colors">
                                <?= htmlspecialchars($product['name']) ?>
                            </h3>
                            
                            <!-- Rating -->
                            <div class="flex items-center space-x-1 mb-4">
                                <?php $rating = rand(4, 5); for($i=0; $i<5; $i++): ?>
                                    <i class="fa-solid fa-star text-[10px] <?= $i < $rating ? 'text-amber-400' : 'text-slate-600' ?>"></i>
                                <?php endfor; ?>
                                <span class="text-xs text-slate-400 font-medium ml-1.5">(<?= rand(12, 120) ?> reviews)</span>
                            </div>
                            
                            <div class="mt-auto flex items-center justify-between pt-4 border-t border-slate-700/50">
                                <div>
                                    <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-0.5 font-medium">Price</p>
                                    <p class="text-xl font-bold text-white flex items-baseline gap-1">
                                        <span class="text-sm text-indigo-400">$</span><?= number_format($product['selling_price'], 2) ?>
                                    </p>
                                </div>
                                <button class="add-to-cart-btn btn-gradient text-white text-sm font-medium px-4 py-2 rounded-xl flex items-center space-x-2 shadow-lg shadow-indigo-500/20 group/btn" data-id="<?= $product['id'] ?>" data-price="<?= $product['selling_price'] ?>">
                                    <span>Add</span>
                                    <i class="fa-solid fa-cart-plus group-hover/btn:scale-110 transition-transform"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800/60 bg-slate-900/80 backdrop-blur-xl mt-12 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-8">
                <div class="lg:col-span-1">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mr-3 shadow-lg shadow-indigo-500/20">
                            <i class="fa-solid fa-store text-white"></i>
                        </div>
                        <span class="font-bold text-2xl text-white tracking-tight">TechStore</span>
                    </div>
                    <p class="text-slate-400 text-sm leading-relaxed mb-6">
                        Your premium destination for the latest technology. Experience innovation with our curated selection of top-tier electronics.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-indigo-500 hover:text-white transition-all shadow-sm"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-indigo-500 hover:text-white transition-all shadow-sm"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-indigo-500 hover:text-white transition-all shadow-sm"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-white font-semibold mb-6">Shop Categories</h4>
                    <ul class="space-y-3 text-sm text-slate-400">
                        <li><a href="#" class="hover:text-indigo-400 transition-colors flex items-center space-x-2"><i class="fa-solid fa-chevron-right text-[10px]"></i><span>Smartphones</span></a></li>
                        <li><a href="#" class="hover:text-indigo-400 transition-colors flex items-center space-x-2"><i class="fa-solid fa-chevron-right text-[10px]"></i><span>Laptops & PC</span></a></li>
                        <li><a href="#" class="hover:text-indigo-400 transition-colors flex items-center space-x-2"><i class="fa-solid fa-chevron-right text-[10px]"></i><span>Audio & Wearables</span></a></li>
                        <li><a href="#" class="hover:text-indigo-400 transition-colors flex items-center space-x-2"><i class="fa-solid fa-chevron-right text-[10px]"></i><span>Accessories</span></a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-white font-semibold mb-6">Customer Service</h4>
                    <ul class="space-y-3 text-sm text-slate-400">
                        <li><a href="#" class="hover:text-indigo-400 transition-colors flex items-center space-x-2"><i class="fa-solid fa-chevron-right text-[10px]"></i><span>Track Order</span></a></li>
                        <li><a href="#" class="hover:text-indigo-400 transition-colors flex items-center space-x-2"><i class="fa-solid fa-chevron-right text-[10px]"></i><span>Returns & Refunds</span></a></li>
                        <li><a href="#" class="hover:text-indigo-400 transition-colors flex items-center space-x-2"><i class="fa-solid fa-chevron-right text-[10px]"></i><span>FAQ</span></a></li>
                        <li><a href="#" class="hover:text-indigo-400 transition-colors flex items-center space-x-2"><i class="fa-solid fa-chevron-right text-[10px]"></i><span>Contact Support</span></a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-semibold mb-6">Newsletter</h4>
                    <p class="text-slate-400 text-sm mb-4">Subscribe for exclusive offers and tech news.</p>
                    <div class="flex">
                        <input type="email" placeholder="Your email..." class="bg-slate-800 border border-slate-700 text-sm text-white rounded-l-lg px-4 py-2.5 w-full outline-none focus:border-indigo-500 transition-colors">
                        <button class="btn-gradient px-4 py-2.5 rounded-r-lg text-white shadow-lg">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-slate-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-slate-500 text-sm">&copy; <?= date('Y') ?> Premium TechStore. All rights reserved.</p>
                <div class="flex space-x-6 text-sm text-slate-500">
                    <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
