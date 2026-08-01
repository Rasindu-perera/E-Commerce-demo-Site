<?php
session_start();
require_once 'config/db.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

// Build the query
$query = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($search !== '') {
    $query .= " AND name LIKE ?";
    $params[] = "%$search%";
}

if ($category !== '') {
    $query .= " AND category = ?";
    $params[] = $category;
}

$query .= " ORDER BY id DESC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
    
    // Fetch unique categories for the sidebar
    $catStmt = $pdo->query("SELECT DISTINCT category FROM products");
    $categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Fetch 3 random products for 'Special Offers' with a mock 20% discount
    $offerStmt = $pdo->query("SELECT * FROM products ORDER BY RAND() LIMIT 3");
    $specialOffers = $offerStmt->fetchAll();
    
} catch (PDOException $e) {
    $products = [];
    $categories = [];
    $specialOffers = [];
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
                        <a href="index.php" class="text-slate-400 hover:text-white transition-colors relative group px-1 py-2 text-sm font-medium">
                            Home
                            <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-indigo-500 rounded-full transition-all group-hover:w-full"></span>
                        </a>
                        <a href="products.php" class="text-white relative group px-1 py-2 text-sm font-medium">
                            Products
                            <span class="absolute -bottom-1 left-0 w-full h-0.5 bg-indigo-500 rounded-full"></span>
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
                    <a href="checkout.php" class="relative group block">
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
    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 w-full relative z-10 flex flex-col lg:flex-row gap-8">
        
        <!-- Sidebar -->
        <aside class="w-full lg:w-64 flex-shrink-0 space-y-8">
            <!-- Search Bar -->
            <form action="products.php" method="GET" class="relative">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search products..." class="w-full bg-slate-800/80 border border-slate-700/50 text-sm text-white rounded-xl pl-10 pr-4 py-3 outline-none focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/50 transition-all shadow-sm">
                <i class="fa-solid fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                <?php if ($category !== ''): ?>
                    <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
                <?php endif; ?>
            </form>

            <!-- Categories -->
            <div class="glass-card rounded-2xl p-6 border border-slate-700/50 shadow-lg">
                <h3 class="text-white font-semibold mb-4 text-lg">Categories</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="products.php<?= $search !== '' ? '?search='.urlencode($search) : '' ?>" class="flex items-center justify-between text-sm <?= $category === '' ? 'text-indigo-400 font-medium' : 'text-slate-400 hover:text-white' ?> transition-colors">
                            <span>All Products</span>
                        </a>
                    </li>
                    <?php foreach ($categories as $cat): ?>
                    <li>
                        <?php 
                            $queryArgs = ['category' => $cat];
                            if ($search !== '') $queryArgs['search'] = $search;
                            $url = 'products.php?' . http_build_query($queryArgs);
                        ?>
                        <a href="<?= $url ?>" class="flex items-center justify-between text-sm <?= $category === $cat ? 'text-indigo-400 font-medium' : 'text-slate-400 hover:text-white' ?> transition-colors">
                            <span><?= htmlspecialchars($cat) ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Special Offers -->
            <div class="glass-card rounded-2xl p-6 border border-slate-700/50 shadow-lg relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-rose-500/20 rounded-full blur-xl"></div>
                <h3 class="text-white font-semibold mb-4 text-lg flex items-center">
                    <i class="fa-solid fa-fire text-rose-500 mr-2"></i> Special Offers
                </h3>
                <div class="space-y-4">
                    <?php foreach ($specialOffers as $offer): ?>
                    <div class="flex items-center space-x-3 group cursor-pointer">
                        <div class="w-12 h-12 rounded-lg overflow-hidden bg-slate-800 flex-shrink-0">
                            <img src="https://picsum.photos/seed/<?= $offer['id'] ?>/100/100" alt="<?= htmlspecialchars($offer['name']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-white line-clamp-1 group-hover:text-indigo-300 transition-colors"><?= htmlspecialchars($offer['name']) ?></h4>
                            <div class="flex items-center space-x-2 mt-0.5">
                                <span class="text-xs font-bold text-rose-400">$<?= number_format($offer['selling_price'] * 0.8, 2) ?></span>
                                <span class="text-[10px] text-slate-500 line-through">$<?= number_format($offer['selling_price'], 2) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </aside>

        <!-- Product Grid -->
        <div class="flex-grow">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-8 gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-2">Our Collection</h2>
                    <p class="text-slate-400">
                        <?php if ($search !== ''): ?>
                            Showing results for "<?= htmlspecialchars($search) ?>"
                        <?php elseif ($category !== ''): ?>
                            Browsing <?= htmlspecialchars($category) ?>
                        <?php else: ?>
                            Explore all our premium devices
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <?php if (isset($error)): ?>
                <div class="bg-red-500/10 border border-red-500/50 rounded-2xl p-6 text-center">
                    <i class="fa-solid fa-triangle-exclamation text-3xl text-red-400 mb-3"></i>
                    <h3 class="text-lg font-medium text-white mb-1">Failed to load products</h3>
                    <p class="text-red-200/70 text-sm"><?= htmlspecialchars($error) ?></p>
                </div>
            <?php elseif (empty($products)): ?>
                <div class="text-center py-24 glass-card rounded-3xl w-full">
                    <div class="w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                        <i class="fa-solid fa-box-open text-3xl text-slate-500"></i>
                    </div>
                    <h3 class="text-2xl font-medium text-white">No products found</h3>
                    <p class="text-slate-400 mt-2 max-w-md mx-auto">Try adjusting your search or filter to find what you're looking for.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6 sm:gap-8">
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
                                <img src="https://picsum.photos/seed/<?= $product['id'] ?>/600/600" alt="<?= htmlspecialchars($product['name']) ?>" class="max-h-full max-w-full object-contain drop-shadow-2xl">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/20 to-transparent opacity-80"></div>
                            </div>

                            <!-- Content -->
                            <div class="p-5 flex-grow flex flex-col -mt-4 relative z-10">
                                <h3 class="text-lg font-semibold text-white mb-2 line-clamp-2 leading-snug group-hover:text-indigo-300 transition-colors">
                                    <?= htmlspecialchars($product['name']) ?>
                                </h3>
                                
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
        </div>
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
