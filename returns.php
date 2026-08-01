<?php
session_start();
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Returns & Refunds - KWRmart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="public/js/tailwind-config.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/style.css">
    <script src="public/js/cart.js" defer></script>
</head>
<body class="antialiased min-h-screen flex flex-col selection:bg-indigo-500/30">

    <!-- Navigation Bar -->
    <nav class="sticky top-0 z-50 backdrop-blur-md bg-slate-900/80 border-b border-slate-700 w-full transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                
                <!-- Left: Logo -->
                <div class="flex-shrink-0 flex items-center cursor-pointer group" onclick="window.location.href='index.php'">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mr-3 shadow-lg shadow-indigo-500/20 group-hover:shadow-indigo-500/40 transition-all duration-300">
                        <i class="fa-solid fa-store text-white text-lg"></i>
                    </div>
                    <span class="font-bold text-2xl tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-400">KWRmart</span>
                </div>
                
                <!-- Center: Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-slate-400 border-b-2 border-transparent pb-1 hover:border-indigo-500 hover:text-indigo-400 text-sm font-medium transition-all duration-300">Home</a>
                    <a href="products.php" class="text-slate-400 border-b-2 border-transparent pb-1 hover:border-indigo-500 hover:text-indigo-400 text-sm font-medium transition-all duration-300">Products</a>
                </div>

                <!-- Right: Actions -->
                <div class="flex items-center space-x-4">
                    
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <div class="hidden sm:flex items-center space-x-4">
                            <a href="login.php" class="text-slate-400 hover:text-indigo-400 transition-colors text-sm font-medium">Login</a>
                            <a href="register.php" class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-lg shadow-indigo-500/20">Register</a>
                        </div>
                    <?php else: ?>
                        <div class="hidden sm:flex items-center space-x-4 pr-4 border-r border-slate-700/50">
                            <a href="profile.php" class="text-slate-300 text-sm hover:text-indigo-400 transition-colors">Hi, <span class="font-semibold text-white"><?= htmlspecialchars($_SESSION['first_name'] ?? 'User') ?></span></a>
                            <a href="logout.php" class="text-slate-400 hover:text-rose-400 transition-colors" title="Logout"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                        <a href="admin/dashboard.php" class="bg-indigo-500/20 text-indigo-300 hover:bg-indigo-500/30 border border-indigo-500/50 rounded-lg px-4 py-2 text-sm font-medium transition-colors flex items-center">
                            <i class="fa-solid fa-chart-line mr-2"></i> Admin Dashboard
                        </a>
                    <?php endif; ?>
                    
                    <a href="cart.php" class="relative group block ml-2">
                        <div class="flex items-center space-x-2 bg-slate-800/80 hover:bg-slate-700 border border-slate-700/50 rounded-full px-4 py-2.5 transition-all group-hover:border-indigo-500/50 shadow-sm relative">
                            <i class="fa-solid fa-cart-shopping text-slate-300 group-hover:text-white transition-colors"></i>
                            <span class="text-sm font-medium text-white hidden sm:block">Cart</span>
                            <span class="absolute -top-1 -right-1 bg-gradient-to-r from-rose-500 to-pink-500 text-white text-[10px] font-bold w-4 h-4 flex items-center justify-center rounded-full shadow-lg shadow-rose-500/30 border border-slate-900">0</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="flex-grow pt-32 pb-16 relative">
        <!-- Abstract Background -->
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-indigo-600/20 rounded-full mix-blend-screen filter blur-[100px] opacity-70"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-600/20 rounded-full mix-blend-screen filter blur-[100px] opacity-70"></div>
        
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <div class="w-20 h-20 mx-auto rounded-2xl bg-slate-800/80 flex items-center justify-center shadow-lg border border-slate-700/50 mb-8">
                <i class="fa-solid fa-arrow-rotate-left text-3xl text-indigo-400"></i>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-6">Returns & Refunds</h1>
            <p class="text-slate-400 text-lg mb-12">Read our comprehensive return policy.</p>
            
            <div class="glass-panel p-8 rounded-2xl text-left border border-slate-700/50 shadow-xl">
                <div class="space-y-6 text-slate-300 leading-relaxed">
                    <p>Welcome to the Returns & Refunds page. This section is currently under development.</p>
                    <p>At KWRmart, we are dedicated to providing the best premium electronics and customer service. Please check back later for detailed information regarding this topic.</p>
                </div>
            </div>
        </div>
    </div>
<!-- Footer -->
    <footer class="border-t border-slate-800/60 bg-slate-900/80 backdrop-blur-xl mt-12 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-8">
                <div class="lg:col-span-1">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mr-3 shadow-lg shadow-indigo-500/20">
                            <i class="fa-solid fa-store text-white"></i>
                        </div>
                        <span class="font-bold text-2xl text-white tracking-tight">KWRmart</span>
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
                        <li><a href="products.php?category=Smartphones" class="hover:text-indigo-400 transition-colors flex items-center space-x-2"><i class="fa-solid fa-chevron-right text-[10px]"></i><span>Smartphones</span></a></li>
                        <li><a href="products.php?category=Laptops" class="hover:text-indigo-400 transition-colors flex items-center space-x-2"><i class="fa-solid fa-chevron-right text-[10px]"></i><span>Laptops & PC</span></a></li>
                        <li><a href="products.php?category=Audio" class="hover:text-indigo-400 transition-colors flex items-center space-x-2"><i class="fa-solid fa-chevron-right text-[10px]"></i><span>Audio & Wearables</span></a></li>
                        <li><a href="products.php?category=Accessories" class="hover:text-indigo-400 transition-colors flex items-center space-x-2"><i class="fa-solid fa-chevron-right text-[10px]"></i><span>Accessories</span></a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-white font-semibold mb-6">Customer Service</h4>
                    <ul class="space-y-3 text-sm text-slate-400">
                        <li><a href="track_order.php" class="hover:text-indigo-400 transition-colors flex items-center space-x-2"><i class="fa-solid fa-chevron-right text-[10px]"></i><span>Track Order</span></a></li>
                        <li><a href="returns.php" class="hover:text-indigo-400 transition-colors flex items-center space-x-2"><i class="fa-solid fa-chevron-right text-[10px]"></i><span>Returns & Refunds</span></a></li>
                        <li><a href="faq.php" class="hover:text-indigo-400 transition-colors flex items-center space-x-2"><i class="fa-solid fa-chevron-right text-[10px]"></i><span>FAQ</span></a></li>
                        <li><a href="contact.php" class="hover:text-indigo-400 transition-colors flex items-center space-x-2"><i class="fa-solid fa-chevron-right text-[10px]"></i><span>Contact Support</span></a></li>
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
                <p class="text-slate-500 text-sm">&copy; <?= date('Y') ?> Premium KWRmart. All rights reserved.</p>
                <div class="flex space-x-6 text-sm text-slate-500">
                    <a href="privacy.php" class="hover:text-white transition-colors">Privacy Policy</a>
                    <a href="terms.php" class="hover:text-white transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>